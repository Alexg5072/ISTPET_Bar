<?php

namespace Database\Seeders;

use App\Models\QrCuenta;
use Illuminate\Database\Seeder;

class QrCuentaSeeder extends Seeder
{
    public function run(): void
    {
        $qr = QrCuenta::firstOrCreate(
            ['nombre' => 'DeUna Bar ISTPET'],
            [
                'titular'           => 'Bar ISTPET',
                'imagen_qr_path'    => 'qr/DEUNA.jpg',
                'descripcion'       => 'Cuenta principal DeUna del bar institucional',
                'activo'            => true,
                'is_global'         => true,
                'is_future_module'  => false,
            ]
        );

        if (!$qr->imagen_qr_path || !file_exists(public_path('storage/' . $qr->imagen_qr_path))) {
            $qr->update(['imagen_qr_path' => 'qr/DEUNA.jpg']);
        }
    }
}