<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SedesSeeder::class,
            RolesPermisosSeeder::class,
            AdminUserSeeder::class,
            TiposClienteSeeder::class,
            AreasVentaSeeder::class,
            CategoriasSeeder::class,
            ProductosSeeder::class,
            CombosSeeder::class,
            QrCuentaSeeder::class,
            PromocionesSeeder::class,
            ConfiguracionesSeeder::class,
        ]);
    }
}