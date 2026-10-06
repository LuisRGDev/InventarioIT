<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Autenticación en dos pasos con TOTP (RFC 6238) + códigos de respaldo.
 *
 * - El secreto vive cifrado en users.two_factor_secret y solo cuenta como
 *   activo cuando el usuario confirma un código (two_factor_confirmed_at).
 * - Cada código TOTP solo se acepta una vez (two_factor_last_used_step).
 * - Los códigos de respaldo se guardan como SHA-256 (tienen ~50 bits de
 *   entropía aleatoria, así que no hace falta un hash lento) y se consumen
 *   al usarse.
 */
class TwoFactorService
{
    public function __construct(private Google2FA $google2fa) {}

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey(32);
    }

    /** URL otpauth:// que lee la app autenticadora. */
    public function otpAuthUrl(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('two_factor.issuer') ?: config('app.name'),
            $user->email,
            $secret
        );
    }

    /** QR como SVG (sin extensiones de imagen ni servicios externos). */
    public function qrCodeSvg(User $user, string $secret): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(192, 1), new SvgImageBackEnd));

        return $writer->writeString($this->otpAuthUrl($user, $secret));
    }

    /**
     * Valida un código contra un secreto que aún no está guardado (paso de
     * confirmación al activar). Devuelve el periodo aceptado o null.
     */
    public function verifySecret(string $secret, string $code): ?int
    {
        // El "periodo anterior" debe ser numérico (0): con null la librería
        // devuelve true en vez del periodo aceptado.
        $step = $this->google2fa->verifyKeyNewer($secret, $this->normalizeCode($code), 0, config('two_factor.window'));

        return is_int($step) ? $step : null;
    }

    /** Valida el código TOTP de un usuario con 2FA activo y lo marca como usado. */
    public function verifyCode(User $user, string $code): bool
    {
        if (! $user->hasTwoFactorEnabled()) {
            return false;
        }

        $step = $this->google2fa->verifyKeyNewer(
            $user->two_factor_secret,
            $this->normalizeCode($code),
            $user->two_factor_last_used_step ?? 0,
            config('two_factor.window')
        );

        if (! is_int($step)) {
            return false;
        }

        $user->forceFill(['two_factor_last_used_step' => $step])->save();

        return true;
    }

    /**
     * Activa el 2FA con un secreto ya confirmado. Devuelve los códigos de
     * respaldo en claro (esta es la única vez que se pueden ver).
     *
     * @return list<string>
     */
    public function enable(User $user, string $secret, int $confirmedStep): array
    {
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
            'two_factor_last_used_step' => $confirmedStep,
        ])->save();

        return $this->regenerateRecoveryCodes($user);
    }

    /**
     * @return list<string> los códigos nuevos en claro
     */
    public function regenerateRecoveryCodes(User $user): array
    {
        $plain = [];

        for ($i = 0; $i < config('two_factor.recovery_codes'); $i++) {
            $plain[] = Str::lower(Str::random(5).'-'.Str::random(5));
        }

        $user->forceFill([
            'two_factor_recovery_codes' => array_map(fn (string $code) => $this->hashRecoveryCode($code), $plain),
        ])->save();

        return $plain;
    }

    /** Consume un código de respaldo; false si no existe o ya se usó. */
    public function useRecoveryCode(User $user, string $code): bool
    {
        $hash = $this->hashRecoveryCode($code);
        $stored = $user->two_factor_recovery_codes ?? [];

        foreach ($stored as $index => $storedHash) {
            if (hash_equals($storedHash, $hash)) {
                unset($stored[$index]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($stored)])->save();

                return true;
            }
        }

        return false;
    }

    public function remainingRecoveryCodes(User $user): int
    {
        return count($user->two_factor_recovery_codes ?? []);
    }

    /**
     * Quita el 2FA del usuario y cierra todas sus sesiones abiertas (si el
     * motivo es un teléfono perdido, no debe quedar ninguna sesión viva).
     */
    public function reset(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_last_used_step' => null,
            'remember_token' => null,
        ])->save();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
        }
    }

    private function normalizeCode(string $code): string
    {
        return preg_replace('/\s+/', '', $code);
    }

    private function hashRecoveryCode(string $code): string
    {
        return hash('sha256', Str::lower(preg_replace('/[\s-]+/', '', $code)));
    }
}
