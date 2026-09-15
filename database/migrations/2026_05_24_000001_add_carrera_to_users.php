<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('carrera')->nullable()->after('cedula');
            $table->string('tipo_sede')->nullable()->after('carrera'); // instituto / conduccion
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['carrera', 'tipo_sede']);
        });
    }
};