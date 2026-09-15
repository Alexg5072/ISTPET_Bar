<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('qr_cuenta_id')->nullable()->constrained('qr_cuentas')->nullOnDelete();

            $table->enum('metodo', ['efectivo', 'qr_deuna']);
            $table->decimal('monto', 8, 2);

            $table->enum('estado', [
                'pendiente',
                'verificado',
                'pagado',
                'rechazado',
            ])->default('pendiente');

            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verificado_at')->nullable();
            $table->string('referencia')->nullable();     // número de transferencia si aplica
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};