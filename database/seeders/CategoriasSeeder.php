<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Almuerzos',  'slug' => 'almuerzos',  'icono' => 'utensils', 'orden' => 2],
            ['nombre' => 'Snacks',     'slug' => 'snacks',     'icono' => 'utensils', 'orden' => 3],
            ['nombre' => 'Bebidas',    'slug' => 'bebidas',    'icono' => 'utensils', 'orden' => 4],
            ['nombre' => 'Postres',    'slug' => 'postres',    'icono' => 'utensils', 'orden' => 5],
            ['nombre' => 'Desayunos',  'slug' => 'desayunos',  'icono' => 'utensils', 'orden' => 6],
        ];

        foreach ($categorias as $cat) {
            Categoria::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['activo' => true])
            );
        }
    }
}