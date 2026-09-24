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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropIndex(['device_category_id']);
            $table->dropIndex(['device_model_id']);
        });

        Schema::table('device_models', function (Blueprint $table) {
            $table->dropIndex(['device_category_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['job_position_id']);
        });

        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('device_assignments', function (Blueprint $table) {
            $table->dropIndex(['assigned_by_user_id']);
            $table->dropIndex(['returned_by_user_id']);
        });
    }
};
