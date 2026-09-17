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

        Schema::table('device_assignments', function (Blueprint $table) {
            $table->index(['device_id', 'returned_at']);
            $table->index(['employee_id', 'returned_at']);
        });

        Schema::table('phone_line_assignments', function (Blueprint $table) {
            $table->index(['phone_line_id', 'returned_at']);
            $table->index(['employee_id', 'returned_at']);
        });

        Schema::table('office_extension_assignments', function (Blueprint $table) {
            $table->index(['office_extension_id', 'returned_at']);
            $table->index(['employee_id', 'returned_at']);
        });
    }

    public function down(): void
    {
        Schema::table('device_maintenances', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['type']);
            $table->dropIndex(['device_id', 'status']);
        });

        Schema::table('phone_lines', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('office_extensions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('device_assignments', function (Blueprint $table) {
            $table->dropIndex(['device_id', 'returned_at']);
            $table->dropIndex(['employee_id', 'returned_at']);
        });

        Schema::table('phone_line_assignments', function (Blueprint $table) {
            $table->dropIndex(['phone_line_id', 'returned_at']);
            $table->dropIndex(['employee_id', 'returned_at']);
        });

        Schema::table('office_extension_assignments', function (Blueprint $table) {
            $table->dropIndex(['office_extension_id', 'returned_at']);
            $table->dropIndex(['employee_id', 'returned_at']);
        });
    }
};
