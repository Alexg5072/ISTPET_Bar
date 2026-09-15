<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin principal
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@istpet.edu.ec'],
            [
                'name'     => 'Administrador ISTPET',
                'password' => Hash::make('Admin2026!'),
                'activo'   => true,
            ]
        );
        $superadmin->assignRole('superadmin');

        // Cajero de prueba
        $cajero = User::firstOrCreate(
            ['email' => 'cajero@istpet.edu.ec'],
            [
                'name'     => 'Cajera del Bar',
                'password' => Hash::make('Cajero2026!'),
                'activo'   => true,
            ]
        );
        $cajero->assignRole('cajero');

        // Visor de prueba
        $visor = User::firstOrCreate(
            ['email' => 'director@istpet.edu.ec'],
            [
                'name'     => 'Director Académico',
                'password' => Hash::make('Visor2026!'),
                'activo'   => true,
            ]
        );
        $visor->assignRole('visor');
    }
}