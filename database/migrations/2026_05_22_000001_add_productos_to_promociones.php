<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('promociones', function (Blueprint $table) {
            $table->foreignId('producto_id_1')->nullable()->constrained('productos')->nullOnDelete()->after('precio_destacado');
            $table->foreignId('producto_id_2')->nullable()->constrained('productos')->nullOnDelete()->after('producto_id_1');
            $table->foreignId('producto_id_3')->nullable()->constrained('productos')->nullOnDelete()->after('producto_id_2');
        });
    }

    public function down(): void
    {
        Schema::table('promociones', function (Blueprint $table) {
            $table->dropForeign(['producto_id_1']);
            $table->dropForeign(['producto_id_2']);
            $table->dropForeign(['producto_id_3']);
            $table->dropColumn(['producto_id_1', 'producto_id_2', 'producto_id_3']);
        });
    }
};