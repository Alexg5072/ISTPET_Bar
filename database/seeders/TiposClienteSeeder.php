<?php

namespace Database\Seeders;

use App\Models\TipoCliente;
use Illuminate\Database\Seeder;

class TiposClienteSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Estudiante Instituto',    'slug' => 'estudiante-instituto',   'tipo' => 'estudiante_instituto'],
            ['nombre' => 'Estudiante Conducción',   'slug' => 'estudiante-conduccion',  'tipo' => 'estudiante_conduccion'],
            ['nombre' => 'Invitado / Externo',      'slug' => 'invitado',               'tipo' => 'invitado'],
            ['nombre' => 'Personal Staff',          'slug' => 'staff',                  'tipo' => 'staff'],
        ];

        foreach ($tipos as $tipo) {
            TipoCliente::firstOrCreate(
                ['slug' => $tipo['slug']],
                array_merge($tipo, [
                    'permite_fiado'    => false,
                    'activo'           => true,
                    'is_future_module' => true,   // módulo extra — no activo en v1
                ])
            );
        }
    }
}