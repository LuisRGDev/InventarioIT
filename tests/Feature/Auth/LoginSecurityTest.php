<?php

namespace Tests\Feature\Auth;

use App\Models\LoginAudit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * Cubre el endurecimiento del login (propuesta de seguridad): cuentas
 * desactivadas no pueden entrar aunque la contraseña sea correcta, cada
 * intento (exitoso o fallido) queda en login_audits, y el límite de
 * intentos por correo (independiente de la IP) corta un ataque que rota
 * de IP contra la misma cuenta.
 */
class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_log_in_even_with_the_correct_password(): void
    {
        $user = User::factory()->create(['active' => false]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors()->assertNoRedirect();

        $this->assertGuest();

        $this->assertDatabaseHas('login_audits', [
            'email' => $user->email,
            'successful' => false,
            'reason' => 'inactive_account',
        ]);
    }

    /**
     * Regresión: el error de login para una cuenta desactivada era un
     * mensaje distinto ("Esta cuenta está desactivada...") al de
     * credenciales inválidas, lo que permitía enumerar si un correo
     * específico existe (pero está desactivado) vs. no existe. Ahora usa
     * el mismo mensaje genérico en ambos casos; el motivo real
     * (inactive_account vs invalid_credentials) sigue quedando en
     * login_audits para que el admin lo vea desde /users.
     */
    public function test_inactive_account_shows_the_same_generic_message_as_invalid_credentials(): void
    {
        $inactiveUser = User::factory()->create(['active' => false]);
        $otherUser = User::factory()->create();

        $inactiveAttempt = Volt::test('pages.auth.login')
            ->set('form.email', $inactiveUser->email)
            ->set('form.password', 'password');
        $inactiveAttempt->call('login');

        $wrongPasswordAttempt = Volt::test('pages.auth.login')
            ->set('form.email', $otherUser->email)
            ->set('form.password', 'wrong-password');
        $wrongPasswordAttempt->call('login');

        $this->assertSame(
            $wrongPasswordAttempt->errors()->first('form.email'),
            $inactiveAttempt->errors()->first('form.email')
        );
    }

    public function test_successful_login_is_recorded_in_the_audit_log(): void
    {
        $user = User::factory()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $audit = LoginAudit::where('email', $user->email)->first();

        $this->assertNotNull($audit);
        $this->assertTrue($audit->successful);
        $this->assertSame($user->id, $audit->user_id);
    }

    public function test_failed_login_with_wrong_password_is_recorded_in_the_audit_log(): void
    {
        $user = User::factory()->create();

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login');

        $this->assertDatabaseHas('login_audits', [
            'email' => $user->email,
            'successful' => false,
            'reason' => 'invalid_credentials',
        ]);
    }

    public function test_repeated_failures_against_the_same_email_are_locked_out_even_across_different_ips(): void
    {
        $user = User::factory()->create();

        // El límite por email+IP (5 intentos) no dispara aquí porque cada
        // intento simula una IP distinta; el límite adicional por email
        // (10 intentos, sin importar la IP) sí debe cortar el ataque.
        for ($i = 0; $i < 10; $i++) {
            request()->server->set('REMOTE_ADDR', "10.0.0.{$i}");

            Volt::test('pages.auth.login')
                ->set('form.email', $user->email)
                ->set('form.password', 'wrong-password')
                ->call('login');
        }

        request()->server->set('REMOTE_ADDR', '10.0.0.200');

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors();
        $this->assertGuest();
    }

    /**
     * Regresión: login_audits.email/user_agent son VARCHAR(255). Un correo
     * o un User-Agent (controlado por completo por el cliente, sin
     * validación de Laravel) más largos que eso reventaban el INSERT con
     * una excepción no capturada (SQLSTATE 22001 en MySQL/MariaDB) en
     * pleno intento de login. SQLite no aplica el límite de VARCHAR, así
     * que esta prueba verifica el truncado en sí (LoginAuditService),
     * no el motor de base de datos.
     */
    public function test_an_overly_long_user_agent_does_not_crash_the_audit_log(): void
    {
        $user = User::factory()->create();

        request()->headers->set('User-Agent', str_repeat('A', 2000));

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $audit = LoginAudit::where('email', $user->email)->first();

        $this->assertNotNull($audit);
        $this->assertLessThanOrEqual(255, strlen($audit->user_agent));
    }

    public function test_an_overly_long_login_email_is_rejected_by_validation(): void
    {
        $component = Volt::test('pages.auth.login')
            ->set('form.email', str_repeat('a', 250).'@example.com')
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors(['form.email']);
    }
}
