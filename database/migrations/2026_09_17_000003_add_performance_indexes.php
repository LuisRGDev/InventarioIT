<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->index('status');
            $table->index('type');
            $table->index(['device_id', 'status']);
        });

        Schema::table('phone_lines', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('office_extensions', function (Blueprint $table) {
            $table->index('status');
        });

        // Nota: los índices de device_assignments(device_id, returned_at) y
        // device_assignments(employee_id, returned_at) ya existen desde
        // 2024_01_01_000004_create_device_assignments_table.php; no se repiten aquí.

        Schema::table('phone_line_assignments', function (Blueprint $table) {
            $table->index(['phone_line_id', 'returned_at']);
            $table->index(['employee_id', 'returned_at']);
        });

        Schema::table('office_extension_assignments', function (Blueprint $table) {
            // Nombre explícito y corto: el autogenerado por Laravel para
            // estas columnas supera el límite de 64 caracteres de
            // identificador de MySQL/MariaDB (aunque SQLite, usado en
            // desarrollo, no lo aplica).
            $table->index(['office_extension_id', 'returned_at'], 'office_ext_assignments_ext_id_returned_at_index');
            $table->index(['employee_id', 'returned_at']);
        });
    }

    public function down(): void
    {
        // device_maintenances.status y .type no son foreign keys: esos dos
        // sí se pueden revertir sin problema.
        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
        });

        Schema::table('phone_lines', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('office_extensions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        // No se revierten los índices compuestos (device_maintenances
        // device_id+status, phone_line_assignments phone_line_id/employee_id
        // +returned_at, office_extension_assignments office_extension_id/
        // employee_id+returned_at): en todos, la primera columna es una
        // foreign key, y en MySQL/MariaDB cada uno quedó como el único
        // índice que satisface su constraint tras aplicar esta migración
        // (a diferencia de SQLite). dropIndex falla con SQLSTATE 1553
        // "needed in a foreign key constraint". Quitar también el foreign
        // key para poder revertir el índice excede el alcance de esta
        // migración.
        //
        // Los índices device_assignments(device_id, returned_at) y
        // device_assignments(employee_id, returned_at) tampoco se tocan
        // aquí: pertenecen a 2024_01_01_000004_create_device_assignments_
        // table.php (ver comentario en up()), no a esta migración.
    }
};
