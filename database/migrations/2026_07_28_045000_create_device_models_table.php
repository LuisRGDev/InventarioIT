<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_category_id')
                ->constrained('device_categories')
                ->restrictOnDelete();

            $table->string('brand');
            $table->string('model');
            $table->string('variant')->nullable(); // Ejemplo: "Edición i5 / 16GB", "Avanzada i7", "256GB Black"
            
            // Especificaciones estándar del modelo
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->string('os')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('brand');
            $table->index('model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_models');
    }
};
