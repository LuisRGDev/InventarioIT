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
        Schema::create('phone_lines', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('provider')->nullable();
            $table->string('data_plan')->nullable();
            $table->decimal('plan_cost', 10, 2)->nullable();
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
        Schema::dropIfExists('phone_lines');
    }
};
