<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('office_extensions', function (Blueprint $table) {
            $table->id();
            $table->string('extension_number')->unique();
            $table->string('direct_number')->nullable();
            $table->string('status')->default('disponible'); // disponible, asignada, baja
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_extensions');
    }
};
