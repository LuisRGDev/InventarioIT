<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/**
 * Cambio obligatorio de la contraseña temporal fijada por un admin.
 *
 * Esta ruta NO lleva el middleware password_changed: es a donde se redirige
 * a quien aún no la ha cambiado.
 */
class ForcePasswordChange extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        if (! auth()->user()->must_change_password) {
            return $this->redirect(route('dashboard', absolute: false), navigate: true);
        }
    }

    public function save()
    {
        $user = auth()->user();
        $key = 'force-password-change|'.$user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('current_password', trans('auth.throttle', [
                'seconds' => RateLimiter::availableIn($key),
                'minutes' => ceil(RateLimiter::availableIn($key) / 60),
            ]));

            return;
        }

        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Escribe la contraseña temporal que te dieron.',
            'password.confirmed' => 'La confirmación no coincide.',
            'password.different' => 'La contraseña nueva debe ser distinta de la temporal.',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            RateLimiter::hit($key, 900);
            $this->addError('current_password', 'La contraseña temporal no es correcta.');

            return;
        }

        RateLimiter::clear($key);

        $user->forceFill([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ])->save();

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('status', 'Contraseña actualizada.');

        return $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function logout(Logout $logout)
    {
        $logout();

        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.force-password-change')
            ->layout('layouts.guest');
    }
}
