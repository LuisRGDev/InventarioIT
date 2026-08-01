<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Device;
use App\Models\PhoneLine;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Extract data to phone_lines
        $devicesWithPhones = DB::table('devices')->whereNotNull('phone_number')->get();
        foreach ($devicesWithPhones as $device) {
            DB::table('phone_lines')->insert([
                'number' => $device->phone_number,
                'data_plan' => $device->data_plan,
                'plan_cost' => $device->plan_cost,
                'status' => 'disponible', // Default, we won't try to guess assignments here
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Drop columns from devices
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'data_plan', 'plan_cost']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('data_plan')->nullable();
            $table->decimal('plan_cost', 10, 2)->nullable();
        });

        // We won't restore data from phone_lines back to devices as it could be lossy,
        // but we added the columns back.
    }
};
