<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MailAvailability;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * "¿Olvidaste tu contraseña?" solo se ofrece si el correo saliente está
 * realmente configurado, y `php artisan security:check` avisa de los ajustes
 * de .env que el código no puede garantizar (APP_DEBUG, cookies, HTTPS, 2FA…).
 */
class SecurityAndMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Sin depender de que el entorno de pruebas traiga un .env con clave.
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
    }

    private function useMailer(string $name, array $config): void
    {
        config(['mail.default' => $name, "mail.mailers.{$name}" => $config]);
    }

    // ── Disponibilidad del correo ────────────────────────────────────────

    public function test_log_and_array_mailers_do_not_deliver(): void
    {
        $this->useMailer('log', ['transport' => 'log']);
        $this->assertFalse(MailAvailability::canDeliver());

        $this->useMailer('array', ['transport' => 'array']);
        $this->assertFalse(MailAvailability::canDeliver());
    }

    public function test_smtp_delivers(): void
    {
        $this->useMailer('smtp', ['transport' => 'smtp', 'host' => 'smtp.example.com']);

        $this->assertTrue(MailAvailability::canDeliver());
    }

    public function test_failover_delivers_only_if_some_member_is_real(): void
    {
        config([
            'mail.mailers.smtp' => ['transport' => 'smtp'],
            'mail.mailers.log' => ['transport' => 'log'],
            'mail.mailers.failover' => ['transport' => 'failover', 'mailers' => ['smtp', 'log']],
            'mail.default' => 'failover',
        ]);
        $this->assertTrue(MailAvailability::canDeliver());

        config(['mail.mailers.failover' => ['transport' => 'failover', 'mailers' => ['log', 'array']]]);
        $this->assertFalse(MailAvailability::canDeliver());
    }

    public function test_an_unknown_mailer_does_not_deliver(): void
    {
        config(['mail.default' => 'inexistente']);

        $this->assertFalse(MailAvailability::canDeliver());
    }

    // ── Rutas y login ────────────────────────────────────────────────────

    public function test_password_reset_pages_are_closed_without_real_mail(): void
    {
        $this->useMailer('log', ['transport' => 'log']);

        $this->get('/forgot-password')->assertNotFound();
        $this->get('/reset-password/algun-token')->assertNotFound();
    }

    public function test_password_reset_pages_open_with_real_mail(): void
    {
        $this->useMailer('smtp', ['transport' => 'smtp']);

        $this->get('/forgot-password')->assertOk();
        $this->get('/reset-password/algun-token')->assertOk();
    }

    public function test_login_hides_the_forgot_password_link_without_real_mail(): void
    {
        $this->useMailer('log', ['transport' => 'log']);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee(route('password.request'), false)
            ->assertSee('Pide a un administrador de TI que la restablezca');
    }

    public function test_login_shows_the_forgot_password_link_with_real_mail(): void
    {
        $this->useMailer('smtp', ['transport' => 'smtp']);

        $this->get('/login')
            ->assertOk()
            ->assertSee(route('password.request'), false)
            ->assertDontSee('Pide a un administrador de TI');
    }

    public function test_the_reset_email_is_in_spanish_and_points_to_the_reset_page(): void
    {
        $this->useMailer('smtp', ['transport' => 'smtp']);
        Notification::fake();
        $user = User::factory()->create();

        Password::sendResetLink(['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $mail = $notification->toMail($user);

            $this->assertStringStartsWith('Restablecer contraseña', $mail->subject);
            $this->assertSame('Restablecer contraseña', $mail->actionText);
            $this->assertStringContainsString('/reset-password/'.$notification->token, $mail->actionUrl);

            return true;
        });
    }

    // ── security:check ───────────────────────────────────────────────────

    public function test_security_check_passes_on_a_local_setup(): void
    {
        config(['app.url' => 'http://localhost', 'app.debug' => true, 'two_factor.required' => true]);

        $this->artisan('security:check')
            ->expectsOutputToContain('APP_DEBUG=true (aceptable en local)')
            ->assertExitCode(0);
    }

    public function test_security_check_fails_when_debug_is_on_in_a_server(): void
    {
        config(['app.url' => 'http://server-test', 'app.debug' => true]);

        $this->artisan('security:check')
            ->expectsOutputToContain('APP_DEBUG=true en un servidor')
            ->assertExitCode(1);
    }

    public function test_security_check_requires_secure_cookies_over_https(): void
    {
        config(['app.url' => 'https://inventario.empresa.com', 'app.debug' => false, 'session.secure' => null]);

        $this->artisan('security:check')
            ->expectsOutputToContain('la cookie de sesión no es Secure')
            ->assertExitCode(1);

        config(['session.secure' => true]);

        $this->artisan('security:check')
            ->expectsOutputToContain('SESSION_SECURE_COOKIE=true')
            ->assertExitCode(0);
    }

    public function test_security_check_warns_about_http_without_failing(): void
    {
        config(['app.url' => 'http://192.168.1.50', 'app.debug' => false, 'app.env' => 'production']);

        $this->artisan('security:check')
            ->expectsOutputToContain('se sirve por HTTP')
            ->assertExitCode(0);
    }

    public function test_security_check_reports_mail_and_two_factor_status(): void
    {
        config(['app.url' => 'http://localhost', 'two_factor.required' => false]);
        $this->useMailer('log', ['transport' => 'log']);

        $this->artisan('security:check')
            ->expectsOutputToContain('2FA no es obligatorio')
            ->expectsOutputToContain('Correo saliente sin configurar')
            ->assertExitCode(0);

        $this->useMailer('smtp', ['transport' => 'smtp']);

        $this->artisan('security:check')
            ->expectsOutputToContain('Correo saliente configurado (smtp)')
            ->assertExitCode(0);
    }
}
