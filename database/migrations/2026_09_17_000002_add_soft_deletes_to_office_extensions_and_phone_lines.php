<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_extensions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('phone_lines', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('office_extensions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('phone_lines', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
