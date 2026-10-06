<?php

use App\Models\User;
use App\Services\LoginAuditService;
use App\Services\TwoFactorService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';

    public string $recoveryCode = '';

    public bool $useRecovery = false;

    public function mount()
    {
        if (! $this->pendingUser()) {
            return $this->redirect(route('login', absolute: false), navigate: true);
        }
    }

    /**
     * Usuario que ya validó su contraseña y espera el segundo factor. El
     * pendiente vive en la sesión (lo deja LoginForm::authenticate()) y
     * caduca a los pocos minutos.
     */
    private function pendingUser(): ?User
    {
        $pending = session('two_factor.login');

        if (! is_array($pending) || ($pending['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget('two_factor.login');

            return null;
        }

        return User::active()->find($pending['id']);
    }

    public function verify(TwoFactorService $twoFactor): void
    {
        $user = $this->pendingUser();

        if (! $user) {
            session()->flash('status', 'La verificación expiró. Inicia sesión de nuevo.');
            $this->redirect(route('login', absolute: false), navigate: true);

            return;
        }

        $field = $this->useRecovery ? 'recoveryCode' : 'code';

        $this->validate([
            'code' => $this->useRecovery ? ['nullable'] : ['required', 'string', 'max:12'],
            'recoveryCode' => $this->useRecovery ? ['required', 'string', 'max:32'] : ['nullable'],
        ], [
            'code.required' => 'Escribe el código de 6 dígitos de tu app.',
            'recoveryCode.required' => 'Escribe uno de tus códigos de respaldo.',
        ]);

        $ipKey = 'two-factor|'.$user->id.'|'.request()->ip();
        $userKey = 'two-factor-user|'.$user->id;

        foreach ([[$ipKey, 5], [$userKey, 10]] as [$key, $max]) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                event(new Lockout(request()));
                $seconds = RateLimiter::availableIn($key);

                $this->addError($field, trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]));

                return;
            }
        }

        $valid = $this->useRecovery
            ? $twoFactor->useRecoveryCode($user, $this->recoveryCode)
            : $twoFactor->verifyCode($user, $this->code);

        if (! $valid) {
            RateLimiter::hit($ipKey);
            RateLimiter::hit($userKey, 900);

            app(LoginAuditService::class)->record(
                $user,
                $user->email,
                false,
                $this->useRecovery ? 'invalid_recovery_code' : 'invalid_two_factor_code'
            );

            $this->addError($field, $this->useRecovery
                ? 'El código de respaldo no es válido o ya se usó.'
                : 'El código no es válido. Revisa que la hora de tu teléfono sea correcta.');

            return;
        }

        $remember = (bool) session('two_factor.login.remember', false);

        RateLimiter::clear($ipKey);
        RateLimiter::clear($userKey);
        session()->forget('two_factor.login');

        Auth::login($user, $remember);
        Session::regenerate();

        app(LoginAuditService::class)->record(
            $user,
            $user->email,
            true,
            $this->useRecovery ? 'recovery_code_used' : null
        );

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function toggleRecovery(): void
    {
        $this->useRecovery = ! $this->useRecovery;
        $this->reset('code', 'recoveryCode');
        $this->resetErrorBag();
    }

    public function cancel(): void
    {
        session()->forget('two_factor.login');

        $this->redirect(route('login', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        @if ($useRecovery)
            Escribe uno de tus códigos de respaldo. Cada código solo sirve una vez.
        @else
            Abre tu app autenticadora y escribe el código de 6 dígitos de esta cuenta.
        @endif
    </div>

    <form wire:submit="verify">
        @if ($useRecovery)
            <div>
                <x-input-label for="recoveryCode" value="Código de respaldo" />
                <x-text-input wire:model="recoveryCode" id="recoveryCode" class="block mt-1 w-full font-mono tracking-wider" type="text"
                              autocomplete="one-time-code" autocapitalize="none" spellcheck="false" required autofocus />
                <x-input-error :messages="$errors->get('recoveryCode')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="code" value="Código de verificación" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full font-mono text-center text-xl tracking-[0.4em]" type="text"
                              inputmode="numeric" autocomplete="one-time-code" maxlength="7" placeholder="000000" required autofocus />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-between mt-6">
            <button type="button" wire:click="toggleRecovery" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ $useRecovery ? 'Usar mi app autenticadora' : 'Usar un código de respaldo' }}
            </button>

            <x-primary-button>
                Verificar
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <button type="button" wire:click="cancel" class="underline text-sm text-gray-500 hover:text-gray-800">
            Cancelar e iniciar sesión de nuevo
        </button>
    </div>
</div>
