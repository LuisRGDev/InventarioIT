<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Renombrar la columna existente
            $table->renameColumn('mac_address', 'mac_address_ethernet');

            // Agregar columna para MAC WiFi (después de mac_address_ethernet)
            $table->string('mac_address_wifi', 17)->nullable()->unique()->after('mac_address_ethernet');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('mac_address_wifi');
            $table->renameColumn('mac_address_ethernet', 'mac_address');
        });
    }
};

