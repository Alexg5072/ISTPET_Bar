<?php

namespace Database\Seeders;

use App\Models\QrCuenta;
use Illuminate\Database\Seeder;

class QrCuentaSeeder extends Seeder
{
    public function run(): void
    {
        QrCuenta::firstOrCreate(
            ['nombre' => 'DeUna Bar ISTPET'],
            [
                'titular'           => 'Bar ISTPET',
                'imagen_qr_path'    => null,   // se sube desde el panel admin
                'descripcion'       => 'Cuenta principal DeUna del bar institucional',
                'activo'            => true,
                'is_global'         => true,
                'is_future_module'  => false,  // este sí es funcional desde v1
            ]
        );
    }
}