<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Secreto TOTP y códigos de respaldo: se guardan cifrados con
            // APP_KEY (casts encrypted en User); por eso son TEXT.
            $table->text('two_factor_secret')->nullable()->after('remember_token');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            // Solo cuenta como "2FA activo" cuando el usuario confirmó un
            // código válido de su app: un secreto sin confirmar no protege nada.
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            // Último periodo de 30 s aceptado: evita reutilizar el mismo código.
            $table->unsignedInteger('two_factor_last_used_step')->nullable()->after('two_factor_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'two_factor_last_used_step',
            ]);
        });
    }
};
