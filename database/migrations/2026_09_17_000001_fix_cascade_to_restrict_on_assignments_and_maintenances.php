<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phone_line_assignments', function (Blueprint $table) {
            $table->dropForeign(['phone_line_id']);
            $table->dropForeign(['employee_id']);
            $table->foreign('phone_line_id')->references('id')->on('phone_lines')->restrictOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->restrictOnDelete();
        });

        Schema::table('office_extension_assignments', function (Blueprint $table) {
            $table->dropForeign(['office_extension_id']);
            $table->dropForeign(['employee_id']);
            $table->foreign('office_extension_id')->references('id')->on('office_extensions')->restrictOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->restrictOnDelete();
        });

        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->foreign('device_id')->references('id')->on('devices')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('phone_line_assignments', function (Blueprint $table) {
            $table->dropForeign(['phone_line_id']);
            $table->dropForeign(['employee_id']);
            $table->foreign('phone_line_id')->references('id')->on('phone_lines')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        Schema::table('office_extension_assignments', function (Blueprint $table) {
            $table->dropForeign(['office_extension_id']);
            $table->dropForeign(['employee_id']);
            $table->foreign('office_extension_id')->references('id')->on('office_extensions')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
        });
    }
};
