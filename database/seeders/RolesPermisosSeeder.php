<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos del sistema base ────────────────────────────
        $permisosBase = [
            'ver-dashboard',
            'ver-pedidos',        'gestionar-pedidos',
            'ver-productos',      'gestionar-productos',
            'ver-categorias',     'gestionar-categorias',
            'ver-combos',         'gestionar-combos',
            'ver-stock',          'gestionar-stock',
            'ver-reportes',       'ver-caja',     'imprimir-caja',
            'ver-promociones',    'gestionar-promociones',
            'ver-usuarios',       'gestionar-usuarios',
            'ver-configuracion',  'gestionar-configuracion',
        ];

        // ── Permisos de módulos extra (desactivados por middleware) ─
        $permisosExtra = [
            'ver-fiado',          'gestionar-fiado',
            'ver-areas-venta',    'gestionar-areas-venta',
            'ver-inventario-avanzado', 'gestionar-inventario-avanzado',
            'ver-qr-multiples',   'gestionar-qr-multiples',
            'ver-invitados',      'gestionar-invitados',
        ];

        foreach ([...$permisosBase, ...$permisosExtra] as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // ── Roles ────────────────────────────────────────────────

        // Superadmin — todo sin restricción
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        // Admin — todo excepto usuarios y config global
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'ver-dashboard',
            'ver-pedidos', 'gestionar-pedidos',
            'ver-productos', 'gestionar-productos',
            'ver-categorias', 'gestionar-categorias',
            'ver-combos', 'gestionar-combos',
            'ver-stock', 'gestionar-stock',
            'ver-reportes', 'ver-caja', 'imprimir-caja',
            'ver-promociones', 'gestionar-promociones',
        ]);

        // Cajero — pedidos, caja y stock básico
        $cajero = Role::firstOrCreate(['name' => 'cajero']);
        $cajero->givePermissionTo([
            'ver-dashboard',
            'ver-pedidos', 'gestionar-pedidos',
            'ver-productos',
            'ver-stock', 'gestionar-stock',
            'ver-caja', 'imprimir-caja',
        ]);

        // Visor — solo lectura
        $visor = Role::firstOrCreate(['name' => 'visor']);
        $visor->givePermissionTo([
            'ver-dashboard',
            'ver-pedidos',
            'ver-productos',
            'ver-reportes',
            'ver-caja',
        ]);
    }
}