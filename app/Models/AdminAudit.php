<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bitácora de acciones administrativas sobre cuentas de usuario (quién creó,
 * cambió de rol, activó/desactivó o restableció a quién). Complementa a
 * LoginAudit, que solo registra inicios de sesión.
 */
class AdminAudit extends Model
{
    public const USER_CREATED = 'user_created';

    public const USER_UPDATED = 'user_updated';

    public const TWO_FACTOR_RESET = 'two_factor_reset';

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'actor_email',
        'subject_id',
        'subject_email',
        'action',
        'details',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_id');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::USER_CREATED => 'Creó la cuenta',
            self::USER_UPDATED => 'Modificó la cuenta',
            self::TWO_FACTOR_RESET => 'Restableció el 2FA',
            default => $this->action,
        };
    }

    /**
     * Resumen legible de los cambios (sin valores sensibles: de la
     * contraseña solo se registra que cambió).
     *
     * @return list<string>
     */
    public function detailLines(): array
    {
        $details = $this->details ?? [];
        $lines = [];

        if (isset($details['role'])) {
            $lines[] = is_array($details['role'])
                ? "Rol: {$details['role']['from']} → {$details['role']['to']}"
                : "Rol: {$details['role']}";
        }

        if (isset($details['active'])) {
            $lines[] = is_array($details['active'])
                ? 'Cuenta: '.($details['active']['from'] ? 'activa' : 'inactiva').' → '.($details['active']['to'] ? 'activa' : 'inactiva')
                : 'Cuenta: '.($details['active'] ? 'activa' : 'inactiva');
        }

        foreach (['name' => 'Nombre', 'email' => 'Correo'] as $field => $label) {
            if (isset($details[$field]) && is_array($details[$field])) {
                $lines[] = "{$label}: {$details[$field]['from']} → {$details[$field]['to']}";
            }
        }

        if (! empty($details['password_changed'])) {
            $lines[] = 'Contraseña restablecida (el usuario debe cambiarla al entrar)';
        }

        if (! empty($details['temporary_password'])) {
            $lines[] = 'Contraseña temporal (el usuario debe cambiarla al entrar)';
        }

        return $lines;
    }
}
