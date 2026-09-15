<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->enum('tipo', ['bar', 'cocina', 'pasteleria', 'otro'])->default('bar');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            // Marca este módulo como futuro — no mostrar lógica activa todavía
            $table->boolean('is_future_module')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas_venta');
    }
};