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
        Schema::create('device_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Técnico de TI responsable / registrador
            $table->string('type', 30); // preventivo, correctivo, upgrade
            $table->string('status', 30)->default('en_proceso'); // programado, en_proceso, completado, cancelado
            $table->string('title'); // Título descriptivo del servicio
            $table->text('description')->nullable(); // Detalles, síntomas o motivo del mantenimiento
            $table->text('resolution_notes')->nullable(); // Diagnóstico final o solución aplicada
            $table->date('scheduled_at')->nullable(); // Fecha programada (si se agendó para futuro)
            $table->timestamp('started_at')->nullable(); // Fecha/hora de inicio en taller
            $table->timestamp('completed_at')->nullable(); // Fecha/hora de conclusión del servicio
            $table->date('next_due_at')->nullable(); // Próxima fecha sugerida para mantenimiento preventivo
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_maintenances');
    }
};
