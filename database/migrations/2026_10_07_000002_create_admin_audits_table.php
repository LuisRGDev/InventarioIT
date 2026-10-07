<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audits', function (Blueprint $table) {
            $table->id();
            // Quién y sobre quién: además del id se guarda el correo, para que
            // el registro siga siendo legible aunque se borre la cuenta.
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_email');
            $table->foreignId('subject_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_email')->nullable();
            $table->string('action', 64);
            $table->json('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audits');
    }
};
