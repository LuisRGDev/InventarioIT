<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Console\Command;

class ResetTwoFactor extends Command
{
    protected $signature = '2fa:reset {email : Correo del usuario}';

    protected $description = 'Quita el 2FA de un usuario y cierra sus sesiones (recuperación si un admin perdió su teléfono y sus códigos de respaldo)';

    public function handle(TwoFactorService $twoFactor): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No existe un usuario con ese correo.');

            return self::FAILURE;
        }

        if (! $user->hasTwoFactorEnabled()) {
            $this->warn('Ese usuario no tiene el 2FA activado.');

            return self::SUCCESS;
        }

        $twoFactor->reset($user);

        $this->info("2FA restablecido para {$user->email}. Tendrá que configurarlo de nuevo al iniciar sesión.");

        return self::SUCCESS;
    }
}
