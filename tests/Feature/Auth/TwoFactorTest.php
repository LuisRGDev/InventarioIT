<?php

namespace Tests\Feature\Auth;

use App\Livewire\TwoFactorSettings;
use App\Models\LoginAudit;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Support\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre la autenticación en dos pasos (TOTP + códigos de respaldo): el login
 * en dos pasos, los límites de intentos, la obligatoriedad (redirige a quien
 * no lo ha activado), la pantalla de activación y el restablecimiento por un
 * administrador.
 */
class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([Roles::ADMIN, Roles::TECNICO, Roles::SOLO_LECTURA] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function otp(string $secret): string
    {
        return app(Google2FA::class)->getCurrentOtp($secret);
    }

    /** Usuario con 2FA ya activo; devuelve [usuario, secreto, códigos de respaldo]. */
    private function userWithTwoFactor(array $attributes = []): array
    {
        $user = User::factory()->create($attributes);
        $user->assignRole(Roles::TECNICO);

        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();
        $codes = $service->enable($user, $secret, 0);
        // Sin periodo usado, para poder iniciar sesión con el código actual.
        $user->forceFill(['two_factor_last_used_step' => null])->save();

        return [$user->fresh(), $secret, $codes];
    }

    private function pendingLogin(User $user, bool $remember = false, ?int $expiresAt = null): void
    {
        session()->put('two_factor.login', [
            'id' => $user->id,
            'remember' => $remember,
            'email' => $user->email,
            'expires_at' => $expiresAt ?? now()->addMinutes(5)->timestamp,
        ]);
    }

    // ── Servicio ─────────────────────────────────────────────────────────

    public function test_a_totp_code_is_accepted_once_and_a_replay_is_rejected(): void
    {
        [$user, $secret] = $this->userWithTwoFactor();
        $service = app(TwoFactorService::class);
        $code = $this->otp($secret);

        $this->assertTrue($service->verifyCode($user, $code));
        $this->assertFalse($service->verifyCode($user->fresh(), $code));
    }

    public function test_the_code_used_to_confirm_the_setup_cannot_be_reused_to_log_in(): void
    {
        $user = User::factory()->create();
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();
        $code = $this->otp($secret);

        $step = $service->verifySecret($secret, $code);
        $this->assertIsInt($step);
        $service->enable($user, $secret, $step);

        $this->assertFalse($service->verifyCode($user->fresh(), $code));
    }

    public function test_a_wrong_totp_code_is_rejected(): void
    {
        [$user] = $this->userWithTwoFactor();

        $this->assertFalse(app(TwoFactorService::class)->verifyCode($user, '000000'));
    }

    public function test_a_recovery_code_works_only_once(): void
    {
        [$user, , $codes] = $this->userWithTwoFactor();
        $service = app(TwoFactorService::class);

        $this->assertCount(8, $codes);
        $this->assertTrue($service->useRecoveryCode($user, $codes[0]));
        $this->assertFalse($service->useRecoveryCode($user->fresh(), $codes[0]));
        $this->assertSame(7, $service->remainingRecoveryCodes($user->fresh()));
    }

    public function test_recovery_codes_are_not_stored_in_plain_text_and_secret_is_hidden(): void
    {
        [$user, $secret, $codes] = $this->userWithTwoFactor();

        $raw = \DB::table('users')->where('id', $user->id)->first();

        $this->assertStringNotContainsString($secret, $raw->two_factor_secret);
        $this->assertStringNotContainsString($codes[0], $raw->two_factor_recovery_codes);
        $this->assertArrayNotHasKey('two_factor_secret', $user->toArray());
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $user->toArray());
    }

    // ── Login en dos pasos ───────────────────────────────────────────────

    public function test_login_with_two_factor_does_not_open_a_session_until_the_code_is_verified(): void
    {
        [$user] = $this->userWithTwoFactor();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('two-factor.challenge', absolute: false));

        $this->assertGuest();
        $this->assertSame($user->id, session('two_factor.login.id'));
        $this->assertDatabaseMissing('login_audits', ['user_id' => $user->id, 'successful' => true]);
    }

    public function test_login_without_two_factor_still_signs_in_directly(): void
    {
        $user = User::factory()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_with_two_factor_never_reaches_the_challenge(): void
    {
        [$user] = $this->userWithTwoFactor();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'incorrecta')
            ->call('login')
            ->assertHasErrors('form.email')
            ->assertNoRedirect();

        $this->assertNull(session('two_factor.login'));
    }

    public function test_challenge_with_a_valid_totp_code_signs_the_user_in(): void
    {
        [$user, $secret] = $this->userWithTwoFactor();
        $this->pendingLogin($user);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('code', $this->otp($secret))
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('two_factor.login'));
        $this->assertDatabaseHas('login_audits', ['user_id' => $user->id, 'successful' => true, 'reason' => null]);
    }

    public function test_challenge_with_a_wrong_code_fails_and_is_audited(): void
    {
        [$user] = $this->userWithTwoFactor();
        $this->pendingLogin($user);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('code', '000000')
            ->call('verify')
            ->assertHasErrors('code');

        $this->assertGuest();
        $this->assertDatabaseHas('login_audits', ['user_id' => $user->id, 'successful' => false, 'reason' => 'invalid_two_factor_code']);
    }

    public function test_challenge_accepts_a_recovery_code_and_flags_it_in_the_audit_log(): void
    {
        [$user, , $codes] = $this->userWithTwoFactor();
        $this->pendingLogin($user);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('useRecovery', true)
            ->set('recoveryCode', strtoupper($codes[0]))
            ->call('verify')
            ->assertHasNoErrors();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('login_audits', ['user_id' => $user->id, 'successful' => true, 'reason' => 'recovery_code_used']);
        $this->assertSame(7, app(TwoFactorService::class)->remainingRecoveryCodes($user->fresh()));
    }

    public function test_challenge_is_locked_after_repeated_wrong_codes(): void
    {
        [$user, $secret] = $this->userWithTwoFactor();
        $this->pendingLogin($user);

        $component = Volt::test('pages.auth.two-factor-challenge');

        for ($i = 0; $i < 5; $i++) {
            $component->set('code', '000000')->call('verify');
        }

        // Ni siquiera el código correcto entra una vez bloqueado.
        $component->set('code', $this->otp($secret))->call('verify')->assertHasErrors('code');

        $this->assertGuest();
    }

    public function test_an_expired_pending_login_is_rejected(): void
    {
        [$user, $secret] = $this->userWithTwoFactor();
        $this->pendingLogin($user, expiresAt: now()->subMinute()->timestamp);

        Volt::test('pages.auth.two-factor-challenge')
            ->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
    }

    public function test_challenge_page_without_a_pending_login_redirects_to_login(): void
    {
        Volt::test('pages.auth.two-factor-challenge')
            ->assertRedirect(route('login', absolute: false));
    }

    public function test_challenge_page_is_not_rate_limited_on_repeated_loads(): void
    {
        [$user] = $this->userWithTwoFactor();

        for ($i = 0; $i < 12; $i++) {
            $this->withSession(['two_factor.login' => [
                'id' => $user->id, 'remember' => false, 'email' => $user->email,
                'expires_at' => now()->addMinutes(5)->timestamp,
            ]])->get('/two-factor-challenge')->assertOk();
        }
    }

    public function test_an_inactive_user_cannot_complete_the_challenge(): void
    {
        [$user, $secret] = $this->userWithTwoFactor(['active' => false]);
        $this->pendingLogin($user);

        Volt::test('pages.auth.two-factor-challenge')->assertRedirect(route('login', absolute: false));

        $this->assertGuest();
    }

    // ── Obligatoriedad ───────────────────────────────────────────────────

    public function test_required_two_factor_redirects_users_who_have_not_enabled_it(): void
    {
        config(['two_factor.required' => true]);
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);

        foreach (['/dashboard', '/profile', '/users', '/devices', '/assignments'] as $uri) {
            $this->actingAs($admin)->get($uri)->assertRedirect(route('two-factor.settings'));
        }

        $this->actingAs($admin)->get('/two-factor')->assertOk();
    }

    public function test_required_two_factor_lets_users_with_it_enabled_through(): void
    {
        config(['two_factor.required' => true]);
        [$user] = $this->userWithTwoFactor();
        $user->assignRole(Roles::ADMIN);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_two_factor_not_required_lets_everyone_through(): void
    {
        config(['two_factor.required' => false]);
        $user = User::factory()->create();
        $user->assignRole(Roles::ADMIN);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    // ── Pantalla de activación ───────────────────────────────────────────

    public function test_user_can_enable_two_factor_by_confirming_a_code(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::TECNICO);
        $this->actingAs($user);

        $component = Livewire::test(TwoFactorSettings::class)
            ->assertSee('Escanea el código QR')
            ->assertSeeHtml('<svg');

        $secret = session('two_factor.setup_secret');
        $this->assertNotEmpty($secret);

        $component->set('code', $this->otp($secret))
            ->call('confirm')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertSame($secret, $user->two_factor_secret);
        $this->assertCount(8, $component->get('recoveryCodes'));
        $this->assertNull(session('two_factor.setup_secret'));
    }

    public function test_a_wrong_code_does_not_enable_two_factor(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(TwoFactorSettings::class)
            ->set('code', '000000')
            ->call('confirm')
            ->assertHasErrors('code');

        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_recovery_codes_can_be_regenerated_only_with_the_current_password(): void
    {
        [$user, , $oldCodes] = $this->userWithTwoFactor();
        $this->actingAs($user);

        Livewire::test(TwoFactorSettings::class)
            ->set('password', 'incorrecta')
            ->call('regenerate')
            ->assertHasErrors('password');

        $this->assertTrue(app(TwoFactorService::class)->useRecoveryCode($user->fresh(), $oldCodes[1]));

        $component = Livewire::test(TwoFactorSettings::class)
            ->set('password', 'password')
            ->call('regenerate')
            ->assertHasNoErrors();

        $this->assertCount(8, $component->get('recoveryCodes'));
        $this->assertFalse(app(TwoFactorService::class)->useRecoveryCode($user->fresh(), $oldCodes[2]));
    }

    // ── Restablecimiento por un admin ────────────────────────────────────

    public function test_admin_can_reset_another_users_two_factor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);
        [$target] = $this->userWithTwoFactor();

        $this->actingAs($admin)
            ->post(route('users.two-factor.reset', $target))
            ->assertRedirect(route('users.edit', $target))
            ->assertSessionHas('success');

        $target->refresh();
        $this->assertFalse($target->hasTwoFactorEnabled());
        $this->assertNull($target->two_factor_secret);
        $this->assertNull($target->two_factor_recovery_codes);
        $this->assertDatabaseHas('admin_audits', [
            'actor_id' => $admin->id,
            'subject_id' => $target->id,
            'action' => 'two_factor_reset',
        ]);
    }

    public function test_admin_cannot_reset_their_own_two_factor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);
        $service = app(TwoFactorService::class);
        $service->enable($admin, $service->generateSecret(), 0);

        $this->actingAs($admin)
            ->post(route('users.two-factor.reset', $admin))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->hasTwoFactorEnabled());
    }

    public function test_non_admins_cannot_reset_two_factor(): void
    {
        $tecnico = User::factory()->create();
        $tecnico->assignRole(Roles::TECNICO);
        [$target] = $this->userWithTwoFactor();

        $this->actingAs($tecnico)
            ->post(route('users.two-factor.reset', $target))
            ->assertForbidden();

        $this->assertTrue($target->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_edit_page_offers_the_reset_button_for_other_users_with_two_factor(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);
        [$target] = $this->userWithTwoFactor();

        $this->actingAs($admin)->get(route('users.edit', $target))
            ->assertOk()
            ->assertSee('Restablecer 2FA');

        $this->actingAs($admin)->get(route('users.edit', $admin))
            ->assertOk()
            ->assertDontSee('Restablecer 2FA');
    }

    public function test_reset_console_command_removes_two_factor(): void
    {
        [$user] = $this->userWithTwoFactor();

        $this->artisan('2fa:reset', ['email' => $user->email])->assertSuccessful();

        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
        $this->artisan('2fa:reset', ['email' => 'nadie@example.com'])->assertFailed();
    }

    public function test_users_index_shows_the_two_factor_state_and_legacy_reset_events(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);
        [$withTwoFactor] = $this->userWithTwoFactor();
        LoginAudit::create([
            'user_id' => $withTwoFactor->id, 'email' => $withTwoFactor->email, 'successful' => true,
            'reason' => 'two_factor_reset:'.$admin->email, 'created_at' => now(),
        ]);

        $this->actingAs($admin)->get('/users')
            ->assertOk()
            ->assertSee('Pendiente')
            ->assertSee('Activo')
            ->assertSee('2FA restablecido por un admin');
    }
}
