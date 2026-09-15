<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('nombre');
            $table->string('titular');
            $table->string('imagen_qr_path')->nullable();   // storage/qr/{nombre}.png
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(false);      // solo 1 activa en v1
            $table->boolean('is_global')->default(true);    // se aplica a ambas sedes
            $table->boolean('is_future_module')->default(false); // qr básico sí funciona en v1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_cuentas');
    }
};