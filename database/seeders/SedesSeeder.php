<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedesSeeder extends Seeder
{
    public function run(): void
    {
        Sede::insert([
            [
                'nombre'           => 'Instituto Superior Tecnológico Mayor Pedro Traversari',
                'slug'             => 'instituto',
                'color_primario'   => '#1B2A6B',
                'color_secundario' => '#C9A84C',
                'descripcion'      => 'Sede principal del instituto tecnológico.',
                'activo'           => true,
                'orden'            => 1,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'nombre'           => 'Escuela de Conducción Profesional ISTPET',
                'slug'             => 'conduccion',
                'color_primario'   => '#0D1635',
                'color_secundario' => '#C9A84C',
                'descripcion'      => 'Escuela de conducción del sur de Quito.',
                'activo'           => true,
                'orden'            => 2,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}