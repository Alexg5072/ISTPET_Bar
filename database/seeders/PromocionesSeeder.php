<?php

namespace Database\Seeders;

use App\Models\Promocion;
use Illuminate\Database\Seeder;

class PromocionesSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            ['titulo' => '¡Combo Clásico $2.50!',        'descripcion' => 'Papas + Salchipapa + Cola. La mejor combinación.',          'precio_destacado' => '$2.50', 'orden' => 1],
            ['titulo' => '¡Almuerzo Completo $2.00!',     'descripcion' => 'Sopa + Segundo + Jugo natural. Nutritivo y económico.',     'precio_destacado' => '$2.00', 'orden' => 2],
            ['titulo' => 'Combos desde $1.75',            'descripcion' => 'Ahorra más eligiendo un combo. Desayuno, almuerzo o snack.','precio_destacado' => '$1.75', 'orden' => 3],
            ['titulo' => '¡Empanadas $0.50!',             'descripcion' => 'De queso o de verde. Calientitas todo el día.',             'precio_destacado' => '$0.50', 'orden' => 4],
            ['titulo' => 'Pollo Broster $2.50',           'descripcion' => 'Pieza crujiente + papas fritas. El favorito del bar.',      'precio_destacado' => '$2.50', 'orden' => 5],
            ['titulo' => 'Jugo Natural $0.75',            'descripcion' => 'Naranja, mora o maracuyá. Fresco y natural.',               'precio_destacado' => '$0.75', 'orden' => 6],
        ];

        foreach ($promos as $promo) {
            Promocion::firstOrCreate(
                ['titulo' => $promo['titulo']],
                array_merge($promo, [
                    'duracion_segundos' => 4,
                    'activo'            => true,
                ])
            );
        }
    }
}