<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── COMPROBANTES ──────────────────────────────────────────
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->unique()->constrained('pedidos')->cascadeOnDelete();
            $table->string('numero_comprobante', 30)->unique();
            $table->json('contenido_json');    // snapshot completo del pedido
            $table->string('pdf_path')->nullable();
            $table->boolean('impreso')->default(false);
            $table->timestamp('impreso_at')->nullable();
            $table->timestamps();
        });

        // ── STOCK MOVIMIENTOS ─────────────────────────────────────
        Schema::create('stock_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();

            $table->enum('tipo', [
                'entrada',
                'salida',
                'ajuste',
                'venta',
                'devolucion',
            ]);
            $table->integer('cantidad');
            $table->unsignedInteger('stock_antes');
            $table->unsignedInteger('stock_despues');
            $table->string('motivo')->nullable();
            $table->timestamps();
        });

        // ── PROMOCIONES (banners modo reposo) ─────────────────────
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable();      // storage/promociones/
            $table->string('precio_destacado', 20)->nullable();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->unsignedSmallInteger('duracion_segundos')->default(4);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // ── CONFIGURACIONES ───────────────────────────────────────
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->enum('tipo', ['string', 'boolean', 'integer', 'json'])->default('string');
            $table->string('descripcion')->nullable();
            $table->string('grupo', 50)->default('general');
            $table->timestamps();
        });

        // ── AUDIT LOGS ────────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion');
            $table->string('modelo')->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('configuraciones');
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('stock_movimientos');
        Schema::dropIfExists('comprobantes');
    }
};