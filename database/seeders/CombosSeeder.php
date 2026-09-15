<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class CombosSeeder extends Seeder
{
    public function run(): void
    {
        $combosData = [
            [
                'nombre'      => 'Combo Clásico',
                'slug'        => 'combo-clasico',
                'descripcion' => 'Papas + Salchipapa pequeña + Gaseosa',
                'precio'      => 2.50,
                'productos'   => ['salchipapa', 'gaseosa-500ml'],
            ],
            [
                'nombre'      => 'Combo Familiar',
                'slug'        => 'combo-familiar',
                'descripcion' => 'Almuerzo del día + Jugo natural + Pan',
                'precio'      => 3.00,
                'productos'   => ['almuerzo-del-dia', 'jugo-natural'],
            ],
            [
                'nombre'      => 'Combo Desayuno',
                'slug'        => 'combo-desayuno',
                'descripcion' => 'Sánduche de jamón + Café + Tostada',
                'precio'      => 1.75,
                'productos'   => ['sandwich-de-jamon', 'cafe'],
            ],
        ];

        foreach ($combosData as $data) {
            $combo = Combo::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'nombre'      => $data['nombre'],
                    'descripcion' => $data['descripcion'],
                    'precio'      => $data['precio'],
                    'activo'      => true,
                ]
            );

            foreach ($data['productos'] as $slugProducto) {
                $producto = Producto::where('slug', $slugProducto)->first();
                if ($producto) {
                    ComboItem::firstOrCreate([
                        'combo_id'    => $combo->id,
                        'producto_id' => $producto->id,
                    ], ['cantidad' => 1]);
                }
            }
        }
    }
}