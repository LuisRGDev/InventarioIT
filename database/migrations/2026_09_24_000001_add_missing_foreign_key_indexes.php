<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hallazgo Medio M8 de la auditoría: a diferencia de MySQL/InnoDB, SQLite
 * (la conexión por defecto de este proyecto) no crea automáticamente un
 * índice secundario para cada columna FK. Estas columnas se usan en
 * WHERE/whereIn de los listados Livewire (DeviceTable, etc.) y además se
 * escanean en cada borrado del lado "uno" de la relación para aplicar el
 * ON DELETE (restrict/set null/nullOnDelete). Sin índice, ambas operaciones
 * degradan a full table scan conforme crecen devices/employees/etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->index('device_category_id');
            $table->index('device_model_id');
        });

        Schema::table('device_models', function (Blueprint $table) {
            $table->index('device_category_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('job_position_id');
        });

        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('device_assignments', function (Blueprint $table) {
            $table->index('assigned_by_user_id');
            $table->index('returned_by_user_id');
        });
    }

    public function down(): void
    {
        // No se revierten estos índices: las seis columnas son foreign keys,
        // y en MySQL/MariaDB (a diferencia de SQLite) cada una de ellas
        // depende exclusivamente del índice creado aquí para satisfacer su
        // constraint — no queda ningún otro índice sobre la columna una vez
        // aplicada esta migración. Intentar un dropIndex directo falla con
        // "Cannot drop index ...: needed in a foreign key constraint"
        // (SQLSTATE 1553). Quitar también el foreign key para poder
        // revertir el índice cambiaría el alcance de esta migración más
        // allá de lo que agrega su up(), así que down() queda como no-op.
    }
};
