<?php

namespace App\Services;

use App\Models\LoginAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginAuditService
{
    public function __construct(private Request $request) {}

    public function record(?User $user, string $email, bool $successful, ?string $reason = null): LoginAudit
    {
        return LoginAudit::create([
            'user_id' => $user?->id,
            // email y user_agent son VARCHAR(255) en login_audits. El email
            // viene del formulario de login (sin max:255, ver LoginForm) y
            // el User-Agent lo controla el cliente por completo, sin
            // validación de Laravel: sin truncar aquí, cualquiera podía
            // mandar un valor más largo y tirar un 500 no controlado
            // (SQLSTATE 22001) en pleno intento de login, exitoso o no.
            'email' => Str::limit($email, 255, ''),
            'ip_address' => $this->request->ip(),
            'user_agent' => Str::limit((string) $this->request->userAgent(), 255, ''),
            'successful' => $successful,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
