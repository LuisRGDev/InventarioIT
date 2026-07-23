<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('device_category_id')
                ->constrained('device_categories')
                ->restrictOnDelete();

            $table->string('serial_number')->unique();
            $table->string('mac_address')->unique()->nullable();
            $table->string('brand');
            $table->string('model');
            $table->string('status')->default('disponible');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->json('specs')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('warranty_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
