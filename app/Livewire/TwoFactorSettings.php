<?php

namespace App\Livewire;

use App\Services\TwoFactorService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

/**
 * Activar el 2FA (escanear QR + confirmar un código) y, una vez activo,
 * ver cuántos códigos de respaldo quedan y regenerarlos.
 *
 * Esta ruta NO lleva el middleware two_factor: es a donde se redirige a
 * quien aún no lo ha activado.
 */
class TwoFactorSettings extends Component
{
    public string $code = '';

    public string $password = '';

    /**
     * Códigos de respaldo en claro: solo existen en pantalla justo después de
     * activar o regenerar (en BD quedan como hash).
     *
     * @var list<string>
     */
    public array $recoveryCodes = [];

    /**
     * El secreto pendiente de confirmar vive en la sesión (no en una
     * propiedad pública de Livewire, que el cliente podría manipular).
     */
    private function setupSecret(TwoFactorService $twoFactor): string
    {
        if (! session()->has('two_factor.setup_secret')) {
            session()->put('two_factor.setup_secret', $twoFactor->generateSecret());
        }

        return session('two_factor.setup_secret');
    }

    public function confirm(TwoFactorService $twoFactor): void
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return;
        }

        $this->validate(
            ['code' => ['required', 'string', 'max:12']],
            ['code.required' => 'Escribe el código de 6 dígitos que muestra tu app.']
        );

        $secret = $this->setupSecret($twoFactor);
        $step = $twoFactor->verifySecret($secret, $this->code);

        if ($step === null) {
            $this->addError('code', 'El código no es válido. Revisa que la hora de tu teléfono sea correcta e inténtalo de nuevo.');

            return;
        }

        $this->recoveryCodes = $twoFactor->enable($user, $secret, $step);
        session()->forget('two_factor.setup_secret');
        $this->reset('code');
    }

    public function regenerate(TwoFactorService $twoFactor): void
    {
        $user = auth()->user();

        if (! $user->hasTwoFactorEnabled()) {
            return;
        }

        $this->validate(
            ['password' => ['required', 'string']],
            ['password.required' => 'Escribe tu contraseña para generar códigos nuevos.']
        );

        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'La contraseña no es correcta.');

            return;
        }

        $this->recoveryCodes = $twoFactor->regenerateRecoveryCodes($user);
        $this->reset('password');
    }

    public function render(TwoFactorService $twoFactor)
    {
        $user = auth()->user()->fresh();
        $enabled = $user->hasTwoFactorEnabled();

        $secret = null;
        $qrSvg = null;

        if (! $enabled) {
            $secret = $this->setupSecret($twoFactor);
            // Quita la cabecera XML del SVG: se inserta inline en el HTML.
            $qrSvg = preg_replace('/^<\?xml[^>]*\?>\s*/', '', $twoFactor->qrCodeSvg($user, $secret));
        }

        return view('livewire.two-factor-settings', [
            'enabled' => $enabled,
            'secret' => $secret,
            'qrSvg' => $qrSvg,
            'remaining' => $twoFactor->remainingRecoveryCodes($user),
            'required' => (bool) config('two_factor.required'),
        ])->layout('layouts.app', ['title' => 'Autenticación en dos pasos']);
    }
}
