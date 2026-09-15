<?php

namespace Database\Seeders;

use App\Models\AreaVenta;
use Illuminate\Database\Seeder;

class AreasVentaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['nombre' => 'Bar',         'slug' => 'bar',        'tipo' => 'bar'],
            ['nombre' => 'Cocina',      'slug' => 'cocina',     'tipo' => 'cocina'],
            ['nombre' => 'Pastelería',  'slug' => 'pasteleria', 'tipo' => 'pasteleria'],
        ];

        foreach ($areas as $area) {
            AreaVenta::firstOrCreate(
                ['slug' => $area['slug']],
                array_merge($area, [
                    'activo'           => false,  // desactivadas en v1
                    'is_future_module' => true,
                ])
            );
        }
    }
}