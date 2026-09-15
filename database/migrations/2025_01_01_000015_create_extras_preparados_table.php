<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MÓDULOS EXTRAS — solo estructura preparada.
 * La lógica de negocio de estos módulos NO está implementada todavía.
 * Controlados por configuraciones: modulo.fiado, modulo.inventario_avanzado
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── FIADOS ────────────────────────────────────────────────
        Schema::create('fiados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes');

            $table->decimal('monto_total', 8, 2)->default(0);
            $table->decimal('monto_pagado', 8, 2)->default(0);
            $table->decimal('monto_pendiente', 8, 2)->default(0);
            $table->decimal('limite_credito', 8, 2)->default(0);

            $table->enum('estado', ['activo', 'pagado', 'suspendido'])->default('activo');

            // Módulo desactivado en v1 — no aparece en kiosco físico
            $table->boolean('activo')->default(false);
            $table->boolean('is_future_module')->default(true);
            $table->timestamps();
        });

        // ── FIADO MOVIMIENTOS ─────────────────────────────────────
        Schema::create('fiado_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiado_id')->constrained('fiados')->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();

            $table->enum('tipo', ['cargo', 'abono', 'ajuste']);
            $table->decimal('monto', 8, 2);
            $table->string('descripcion')->nullable();

            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamps();
        });

        // ── INVENTARIO LOTES ──────────────────────────────────────
        Schema::create('inventario_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('area_venta_id')->nullable()->constrained('areas_venta')->nullOnDelete();

            $table->string('numero_lote')->nullable();
            $table->unsignedInteger('cantidad_inicial')->default(0);
            $table->unsignedInteger('cantidad_actual')->default(0);
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('costo_unitario', 8, 2)->nullable();

            $table->boolean('activo')->default(true);
            $table->boolean('is_future_module')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_lotes');
        Schema::dropIfExists('fiado_movimientos');
        Schema::dropIfExists('fiados');
    }
};