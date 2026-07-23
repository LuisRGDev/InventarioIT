<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_id')
                ->constrained('devices')
                ->restrictOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->restrictOnDelete();

            $table->foreignId('assigned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('returned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();

            $table->string('condition_on_delivery')->nullable(); // nuevo, buen_estado, daniado, obsoleto
            $table->string('condition_on_return')->nullable();   // nuevo, buen_estado, daniado, obsoleto

            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices para búsquedas de trazabilidad
            $table->index(['device_id', 'returned_at']);
            $table->index(['employee_id', 'returned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_assignments');
    }
};
