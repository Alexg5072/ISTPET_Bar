<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();        // PED-001, PED-002 …

            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tipo_cliente_id')->nullable()->constrained('tipos_cliente')->nullOnDelete();

            $table->enum('area_origen', ['kiosco', 'web', 'celular'])->default('kiosco');

            $table->enum('metodo_pago', ['efectivo', 'qr_deuna']);

            $table->enum('estado', [
                'pendiente_pago',
                'pendiente_verificacion',
                'pagado',
                'entregado',
                'cancelado',
            ])->default('pendiente_pago');

            $table->enum('estado_pago', [
                'pendiente',
                'verificado',
                'pagado',
                'rechazado',
            ])->default('pendiente');

            $table->decimal('subtotal', 8, 2)->default(0);
            $table->decimal('total', 8, 2)->default(0);
            $table->text('observaciones')->nullable();

            $table->foreignId('entregado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entregado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};