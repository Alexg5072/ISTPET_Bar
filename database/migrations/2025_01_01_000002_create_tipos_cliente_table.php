<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->enum('tipo', [
                'estudiante_instituto',
                'estudiante_conduccion',
                'invitado',
                'externo',
                'staff',
            ])->default('estudiante_instituto');
            $table->text('descripcion')->nullable();
            $table->boolean('permite_fiado')->default(false);
            $table->boolean('activo')->default(true);
            $table->boolean('is_future_module')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_cliente');
    }
};