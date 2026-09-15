<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            ['almuerzos', 'Almuerzo del Día', 'Sopa + Segundo + Jugo natural', 2.00, 30],
            ['almuerzos', 'Pollo Broster', 'Pieza de pollo + Papas fritas', 2.50, 20],
            ['almuerzos', 'Arroz Especial', 'Arroz con atún, ensalada fresca', 1.75, 0],

            ['snacks', 'Salchipapa', 'Mediana con salsa especial de la casa', 1.25, 50],
            ['snacks', 'Hot Dog', 'Con mostaza y kétchup', 1.00, 40],
            ['snacks', 'Empanada', 'De queso o de verde, calientita', 0.50, 60],
            ['snacks', 'Cachito', 'Relleno de queso y jamón', 0.60, 35],
            ['snacks', 'Pan con Queso', 'Pan artesanal tostado', 0.40, 0],

            ['bebidas', 'Gaseosa 500ml', 'Coca-Cola, Sprite o Fanta', 0.75, 48],
            ['bebidas', 'Agua Mineral', 'Botella 500ml sin gas', 0.50, 60],
            ['bebidas', 'Jugo Natural', 'Naranja, mora o maracuyá', 0.75, 25],
            ['bebidas', 'Café', 'Negro o con leche, recién hecho', 0.50, 40],

            ['postres', 'Arroz con Leche', 'Porción individual, bien dulce', 0.75, 20],
            ['postres', 'Torta de Chocolate', 'Porción generosa de pastel húmedo', 1.00, 15],

            ['desayunos', 'Sánduche de Jamón', 'Pan con jamón, queso y vegetales', 1.00, 20],
            ['desayunos', 'Tostada con Mantequilla', 'Pan tostado con mantequilla y mermelada', 0.50, 25],
        ];

        foreach ($datos as [$catSlug, $nombre, $desc, $precio, $stock]) {
            $categoria = Categoria::where('slug', $catSlug)->first();

            if (! $categoria) {
                continue;
            }

            $slug = Str::slug($nombre);

            Producto::firstOrCreate(
                ['slug' => $slug],
                [
                    'categoria_id' => $categoria->id,
                    'nombre' => $nombre,
                    'descripcion' => $desc,
                    'precio' => $precio,
                    'stock_actual' => $stock,
                    'stock_minimo' => 2,
                    'stock_activo' => $stock > 0,
                    'activo' => true,
                ]
            );
        }
    }
}