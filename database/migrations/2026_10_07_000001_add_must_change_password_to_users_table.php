<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // true cuando un admin fijó la contraseña (alta o restablecimiento):
            // el usuario debe cambiarla en su siguiente inicio de sesión.
            // Las cuentas existentes quedan en false (no se les fuerza nada).
            $table->boolean('must_change_password')->default(false)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
