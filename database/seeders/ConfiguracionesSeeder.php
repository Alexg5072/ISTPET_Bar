<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionesSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // ── Kiosco ──────────────────────────────────────────
            ['clave' => 'kiosco.tiempo_inactividad',   'valor' => '60',   'tipo' => 'integer', 'grupo' => 'kiosco',    'descripcion' => 'Segundos de inactividad antes de modo reposo'],
            ['clave' => 'kiosco.tiempo_reposo',        'valor' => '5',    'tipo' => 'integer', 'grupo' => 'kiosco',    'descripcion' => 'Segundos que dura cada slide de promoción en pantalla'],
            ['clave' => 'kiosco.reiniciar_tras_pedido','valor' => 'true', 'tipo' => 'boolean', 'grupo' => 'kiosco',    'descripcion' => 'Volver al inicio tras completar un pedido'],
            ['clave' => 'kiosco.resolucion',           'valor' => '1920x1080','tipo' => 'string','grupo' => 'kiosco', 'descripcion' => 'Resolución de la pantalla táctil'],

            // ── Pagos ────────────────────────────────────────────
            ['clave' => 'pago.efectivo_habilitado',    'valor' => 'true', 'tipo' => 'boolean', 'grupo' => 'pagos',    'descripcion' => 'Habilitar pago en efectivo en el kiosco'],
            ['clave' => 'pago.qr_deuna_habilitado',    'valor' => 'true', 'tipo' => 'boolean', 'grupo' => 'pagos',    'descripcion' => 'Habilitar pago por QR DeUna en el kiosco'],
            ['clave' => 'pago.titular_deuna',          'valor' => 'Bar ISTPET','tipo' => 'string','grupo' => 'pagos', 'descripcion' => 'Nombre del titular de la cuenta DeUna'],

            // ── Impresora ────────────────────────────────────────
            ['clave' => 'impresora.habilitada',        'valor' => 'false','tipo' => 'boolean', 'grupo' => 'impresora','descripcion' => 'Habilitar impresión automática de tickets'],
            ['clave' => 'impresora.tipo_conexion',     'valor' => 'usb',  'tipo' => 'string',  'grupo' => 'impresora','descripcion' => 'Tipo de conexión: usb, red, bluetooth'],
            ['clave' => 'impresora.ip',                'valor' => '192.168.1.100','tipo' => 'string','grupo' => 'impresora','descripcion' => 'IP de la impresora (si usa red local)'],
            ['clave' => 'impresora.ancho_papel',       'valor' => '80mm', 'tipo' => 'string',  'grupo' => 'impresora','descripcion' => 'Ancho del papel térmico'],

            // ── Seguridad ────────────────────────────────────────
            ['clave' => 'seguridad.intentos_bloqueo',  'valor' => '5',   'tipo' => 'integer', 'grupo' => 'seguridad','descripcion' => 'Intentos fallidos antes de bloquear cuenta'],
            ['clave' => 'seguridad.sesion_expira_min', 'valor' => '30',  'tipo' => 'integer', 'grupo' => 'seguridad','descripcion' => 'Minutos de inactividad para cerrar sesión admin'],

            // ── Módulos extra ────────────────────────────────────
            ['clave' => 'modulo.fiado',                'valor' => 'false','tipo' => 'boolean', 'grupo' => 'modulos',  'descripcion' => 'Habilitar módulo de fiado / cuentas por cobrar'],
            ['clave' => 'modulo.areas_venta',          'valor' => 'false','tipo' => 'boolean', 'grupo' => 'modulos',  'descripcion' => 'Habilitar separación por bar/cocina/pastelería'],
            ['clave' => 'modulo.invitados',            'valor' => 'false','tipo' => 'boolean', 'grupo' => 'modulos',  'descripcion' => 'Habilitar módulo de invitados/externos'],
            ['clave' => 'modulo.inventario_avanzado',  'valor' => 'false','tipo' => 'boolean', 'grupo' => 'modulos',  'descripcion' => 'Habilitar inventario avanzado por lotes'],
            ['clave' => 'modulo.qr_multiple',          'valor' => 'false','tipo' => 'boolean', 'grupo' => 'modulos',  'descripcion' => 'Habilitar múltiples cuentas QR'],
        ];

        foreach ($configs as $config) {
            Configuracion::firstOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}