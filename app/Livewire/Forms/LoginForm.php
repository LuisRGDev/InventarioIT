<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Services\LoginAuditService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            // Límite adicional por email (sin importar la IP): el límite de
            // arriba es por email+IP, así que un atacante que rote de IP
            // podría seguir probando contraseñas contra la misma cuenta sin
            // freno. Este segundo contador es solo por email, con ventana
            // más larga, y no afecta a otros usuarios que compartan la
            // misma IP de salida (p. ej. toda la oficina detrás del mismo NAT).
            RateLimiter::hit($this->emailThrottleKey(), 900);

            app(LoginAuditService::class)->record($user, $this->email, false, 'invalid_credentials');

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        $authenticatedUser = Auth::user();

        if (! $authenticatedUser->active) {
            Auth::logout();

            app(LoginAuditService::class)->record($authenticatedUser, $this->email, false, 'inactive_account');

            throw ValidationException::withMessages([
                'form.email' => 'Esta cuenta está desactivada. Contacta a un administrador.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->emailThrottleKey());

        app(LoginAuditService::class)->record($authenticatedUser, $this->email, true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            $this->triggerLockout($this->throttleKey());
        }

        if (RateLimiter::tooManyAttempts($this->emailThrottleKey(), 10)) {
            $this->triggerLockout($this->emailThrottleKey());
        }
    }

    /**
     * Fire the Lockout event and abort with the standard throttle message.
     */
    protected function triggerLockout(string $key): void
    {
        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key (por email + IP).
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    /**
     * Segundo límite, solo por email, independiente de la IP de origen.
     */
    protected function emailThrottleKey(): string
    {
        return Str::transliterate('login-email|'.Str::lower($this->email));
    }
}
