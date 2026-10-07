<?php

namespace Tests\Feature;

use App\Livewire\ForcePasswordChange;
use App\Models\AdminAudit;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\TwoFactorService;
use App\Support\Roles;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre el cambio obligatorio de la contraseña temporal fijada por un admin
 * y la bitácora de acciones administrativas sobre usuarios.
 */
class PasswordChangeAndAdminAuditTest extends TestCase
{
    use RefreshDatabase;

    private const STRONG = 'NuevaClave!2026x';

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([Roles::ADMIN, Roles::TECNICO, Roles::SOLO_LECTURA] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);

        return $admin;
    }

    private function mustChangeUser(): User
    {
        $user = User::factory()->create(['must_change_password' => true]);
        $user->assignRole(Roles::TECNICO);

        return $user;
    }

    // ── Contraseña temporal ──────────────────────────────────────────────

    public function test_a_user_created_by_an_admin_must_change_the_password(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo Técnico',
            'email' => 'nuevo@middleby.com',
            'password' => 'ClaveTemporal!9',
            'password_confirmation' => 'ClaveTemporal!9',
            'role' => Roles::TECNICO,
            'active' => '1',
        ])->assertRedirect(route('users.index'));

        $created = User::where('email', 'nuevo@middleby.com')->firstOrFail();
        $this->assertTrue($created->must_change_password);
    }

    public function test_existing_users_are_not_forced_to_change_anything(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::ADMIN);

        $this->assertFalse($user->fresh()->must_change_password);
        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_users_with_a_temporary_password_are_redirected_to_the_change_screen(): void
    {
        $user = $this->mustChangeUser();

        foreach (['/dashboard', '/profile', '/devices', '/assignments'] as $uri) {
            $this->actingAs($user)->get($uri)->assertRedirect(route('password.force-change'));
        }

        $this->actingAs($user)->get('/change-password')->assertOk()->assertSee('Tu contraseña es temporal');
    }

    public function test_the_change_screen_redirects_users_who_do_not_need_it(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(ForcePasswordChange::class)
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_changing_the_temporary_password_clears_the_flag(): void
    {
        $user = $this->mustChangeUser();

        Livewire::actingAs($user)->test(ForcePasswordChange::class)
            ->set('current_password', 'password')
            ->set('password', self::STRONG)
            ->set('password_confirmation', self::STRONG)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check(self::STRONG, $user->password));

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_a_wrong_temporary_password_a_weak_password_or_the_same_password_is_rejected(): void
    {
        $user = $this->mustChangeUser();

        Livewire::actingAs($user)->test(ForcePasswordChange::class)
            ->set('current_password', 'incorrecta')
            ->set('password', self::STRONG)
            ->set('password_confirmation', self::STRONG)
            ->call('save')
            ->assertHasErrors('current_password');

        Livewire::actingAs($user)->test(ForcePasswordChange::class)
            ->set('current_password', 'password')
            ->set('password', 'debil')
            ->set('password_confirmation', 'debil')
            ->call('save')
            ->assertHasErrors('password');

        Livewire::actingAs($user)->test(ForcePasswordChange::class)
            ->set('current_password', 'password')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('save')
            ->assertHasErrors('password');

        $this->assertTrue($user->fresh()->must_change_password);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_guessing_the_temporary_password_is_rate_limited(): void
    {
        $user = $this->mustChangeUser();

        $component = Livewire::actingAs($user)->test(ForcePasswordChange::class);

        for ($i = 0; $i < 5; $i++) {
            $component->set('current_password', 'incorrecta'.$i)
                ->set('password', self::STRONG)->set('password_confirmation', self::STRONG)
                ->call('save');
        }

        // Ni siquiera la contraseña temporal correcta pasa una vez bloqueado.
        $component->set('current_password', 'password')->call('save')->assertHasErrors('current_password');

        $this->assertTrue($user->fresh()->must_change_password);
    }

    public function test_an_admin_setting_another_users_password_forces_a_change_and_kills_sessions(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['remember_token' => 'token-viejo']);
        $target->assignRole(Roles::TECNICO);

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            'active' => '1',
            'password' => 'ClaveReset!2026',
            'password_confirmation' => 'ClaveReset!2026',
        ])->assertRedirect(route('users.index'));

        $target->refresh();
        $this->assertTrue($target->must_change_password);
        $this->assertNull($target->remember_token);
        $this->assertTrue(Hash::check('ClaveReset!2026', $target->password));
        $this->assertDatabaseHas('admin_audits', ['subject_id' => $target->id, 'action' => 'user_updated']);
    }

    public function test_an_admin_changing_their_own_password_is_not_forced_to_change_it_again(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Roles::ADMIN,
            'active' => '1',
            'password' => 'MiPropia!2026x',
            'password_confirmation' => 'MiPropia!2026x',
        ]);

        $this->assertFalse($admin->fresh()->must_change_password);
    }

    public function test_resetting_the_password_by_email_clears_the_flag(): void
    {
        Notification::fake();
        $user = $this->mustChangeUser();

        Password::sendResetLink(['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            Volt::test('pages.auth.reset-password', ['token' => $notification->token])
                ->set('email', $user->email)
                ->set('password', self::STRONG)
                ->set('password_confirmation', self::STRONG)
                ->call('resetPassword')
                ->assertHasNoErrors();

            return true;
        });

        $this->assertFalse($user->fresh()->must_change_password);
    }

    public function test_users_index_flags_pending_password_changes(): void
    {
        $this->mustChangeUser();

        $this->actingAs($this->admin())->get('/users')->assertOk()->assertSee('Debe cambiar su contraseña');
    }

    // ── Bitácora de acciones administrativas ─────────────────────────────

    public function test_creating_a_user_is_recorded(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Nuevo', 'email' => 'nuevo@middleby.com',
            'password' => 'ClaveTemporal!9', 'password_confirmation' => 'ClaveTemporal!9',
            'role' => Roles::SOLO_LECTURA, 'active' => '1',
        ]);

        $audit = AdminAudit::where('action', AdminAudit::USER_CREATED)->firstOrFail();
        $this->assertSame($admin->id, $audit->actor_id);
        $this->assertSame($admin->email, $audit->actor_email);
        $this->assertSame('nuevo@middleby.com', $audit->subject_email);
        $this->assertSame(Roles::SOLO_LECTURA, $audit->details['role']);
        // Nunca se guarda la contraseña.
        $this->assertStringNotContainsString('ClaveTemporal', json_encode($audit->getAttributes()));
    }

    public function test_role_and_active_changes_are_recorded_with_before_and_after(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole(Roles::SOLO_LECTURA);

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            // sin "active": se desactiva
        ]);

        $audit = AdminAudit::where('action', AdminAudit::USER_UPDATED)->firstOrFail();
        $this->assertSame(['from' => Roles::SOLO_LECTURA, 'to' => Roles::TECNICO], $audit->details['role']);
        $this->assertSame(['from' => true, 'to' => false], $audit->details['active']);
        $this->assertContains('Rol: '.Roles::SOLO_LECTURA.' → '.Roles::TECNICO, $audit->detailLines());
        $this->assertContains('Cuenta: activa → inactiva', $audit->detailLines());
    }

    public function test_saving_without_changes_does_not_create_an_audit_entry(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole(Roles::TECNICO);

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'role' => Roles::TECNICO,
            'active' => '1',
        ]);

        $this->assertDatabaseCount('admin_audits', 0);
    }

    public function test_a_blocked_last_admin_change_is_not_recorded_as_done(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name, 'email' => $admin->email, 'role' => Roles::TECNICO, 'active' => '1',
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('admin_audits', 0);
    }

    public function test_resetting_two_factor_is_recorded(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $service = app(TwoFactorService::class);
        $service->enable($target, $service->generateSecret(), 0);

        $this->actingAs($admin)->post(route('users.two-factor.reset', $target));

        $this->assertDatabaseHas('admin_audits', [
            'actor_id' => $admin->id, 'subject_id' => $target->id, 'action' => AdminAudit::TWO_FACTOR_RESET,
        ]);
    }

    public function test_users_index_lists_the_admin_actions(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $target->assignRole(Roles::SOLO_LECTURA);

        $this->actingAs($admin)->put("/users/{$target->id}", [
            'name' => $target->name, 'email' => $target->email, 'role' => Roles::TECNICO, 'active' => '1',
        ]);

        $this->actingAs($admin)->get('/users')
            ->assertOk()
            ->assertSee('Acciones administrativas recientes')
            ->assertSee('Modificó la cuenta')
            ->assertSee($target->email)
            ->assertSee('Rol: '.Roles::SOLO_LECTURA.' → '.Roles::TECNICO);
    }

    public function test_the_audit_survives_deleting_the_accounts_involved(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();
        $this->actingAs($admin)->post(route('users.two-factor.reset', $target)); // sin 2FA: no registra
        $audit = app(AdminAuditService::class)->record($admin, AdminAudit::USER_UPDATED, $target, ['active' => ['from' => true, 'to' => false]]);

        $target->delete();

        $audit->refresh();
        $this->assertNull($audit->subject_id);
        $this->assertSame($target->email, $audit->subject_email);
    }
}
