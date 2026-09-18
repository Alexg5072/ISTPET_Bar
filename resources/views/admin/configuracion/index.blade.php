@extends('layouts.app')
@section('title','Configuración')
@section('page-icon')
<x-admin.icon name="configuracion" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Configuración del Sistema')
@section('page-subtitle','Ajustes generales · Impresora · Pagos · Seguridad · Módulos')

@section('header-actions')
    <button form="config-form" type="submit" class="btn-gold">Guardar cambios</button>
@endsection

@section('content')

@php
$iconosGrupo = [
    'kiosco'    => ['ico' => '', 'label' => 'Kiosco',    'desc' => 'Comportamiento general de la pantalla táctil'],
    'pagos'     => ['ico' => '', 'label' => 'Pagos',     'desc' => 'Métodos de pago aceptados en el kiosco'],
    'impresora' => ['ico' => '', 'label' => 'Impresora', 'desc' => 'Configuración de la impresora térmica de tickets'],
    'seguridad' => ['ico' => '', 'label' => 'Seguridad', 'desc' => 'Sesiones y protección de acceso al panel admin'],
    'modulos'   => ['ico' => '', 'label' => 'Módulos',   'desc' => 'Funcionalidades extra — actívalas cuando estén listas'],
    'general'   => ['ico' => '', 'label' => 'General',   'desc' => 'Configuración general del sistema'],
];

$labels = [
    'kiosco.tiempo_inactividad'    => ['label' => 'Tiempo de inactividad',    'desc' => 'Segundos sin actividad antes de activar el modo reposo'],
    'kiosco.reiniciar_tras_pedido' => ['label' => 'Reiniciar tras pedido',    'desc' => 'Volver automáticamente al inicio después de completar un pedido'],
    'kiosco.resolucion'            => ['label' => 'Resolución de pantalla',   'desc' => 'Resolución configurada en la pantalla táctil (ej: 1920x1080)'],
    'kiosco.tiempo_reposo'         => ['label' => 'Tiempo del modo reposo',   'desc' => 'Segundos que dura cada slide de promoción en pantalla antes de pasar a la siguiente'],

    'pago.efectivo_habilitado'     => ['label' => 'Pago en efectivo',         'desc' => 'Permitir que los clientes paguen en efectivo en el kiosco'],
    'pago.qr_deuna_habilitado'     => ['label' => 'Pago QR DeUna',            'desc' => 'Permitir pagos escaneando el código QR de DeUna'],
    'pago.titular_deuna'           => ['label' => 'Titular cuenta DeUna',     'desc' => 'Nombre del titular que aparece en el QR de cobro'],

    'impresora.habilitada'         => ['label' => 'Impresora habilitada',     'desc' => 'Activar impresión automática del ticket al confirmar un pedido'],
    'impresora.tipo_conexion'      => ['label' => 'Tipo de conexión',         'desc' => 'Cómo está conectada la impresora al sistema'],
    'impresora.ip'                 => ['label' => 'IP de la impresora',       'desc' => 'Dirección IP si la impresora usa conexión por red local (Wi-Fi o cable)'],
    'impresora.ancho_papel'        => ['label' => 'Ancho del papel',          'desc' => 'Ancho del rollo de papel térmico instalado en la impresora'],

    'seguridad.intentos_bloqueo'   => ['label' => 'Intentos antes de bloqueo','desc' => 'Número de contraseñas incorrectas antes de bloquear la cuenta'],
    'seguridad.sesion_expira_min'  => ['label' => 'Expiración de sesión',     'desc' => 'Minutos de inactividad en el panel admin antes de cerrar sesión automáticamente'],

    'modulo.fiado'                 => ['label' => 'Módulo: Fiado',            'desc' => 'Habilita el registro de cuentas por cobrar y fiados a clientes frecuentes'],
    'modulo.areas_venta'           => ['label' => 'Módulo: Áreas de venta',   'desc' => 'Permite separar pedidos por área (bar, cocina, pastelería)'],
    'modulo.invitados'             => ['label' => 'Módulo: Invitados',        'desc' => 'Permite registrar y gestionar clientes externos o invitados'],
    'modulo.inventario_avanzado'   => ['label' => 'Módulo: Inventario avanzado','desc' => 'Control de stock por lotes con fechas de vencimiento y proveedores'],
    'modulo.qr_multiple'           => ['label' => 'Módulo: QR múltiple',      'desc' => 'Gestiona varias cuentas QR de cobro (DeUna, PayPhone, etc.)'],
];

// Opciones para selects
$opcionesConexion = ['usb' => 'USB', 'red' => 'Red (Wi-Fi / Ethernet)', 'bluetooth' => 'Bluetooth'];
$opcionesPapel    = ['58mm' => '58 mm — Tickets pequeños', '80mm' => '80 mm — Estándar', '112mm' => '112 mm — Tickets grandes'];
@endphp

<form id="config-form" action="{{ route('admin.configuracion.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pt-2">
    @csrf

    @foreach($grupos as $grupo => $configs)
    @php $meta = $iconosGrupo[$grupo] ?? ['ico' => '','label'=>ucfirst($grupo),'desc'=>'']; @endphp

    <div class="admin-card overflow-hidden">

        {{-- Header del grupo --}}
        <div class="admin-card-header flex items-center gap-3"
             style="background:rgba(255,255,255,0.02);border-bottom:1px solid rgba(255,255,255,0.06);padding:1rem 1.5rem;">
            <span class="text-2xl">{{ $meta['ico'] }}</span>
            <div>
                <div class="admin-card-title" style="margin:0;">{{ $meta['label'] }}</div>
                <div class="text-xs" style="color:rgba(255,255,255,0.50);margin-top:2px;">{{ $meta['desc'] }}</div>
            </div>
        </div>

        <div>
            @foreach($configs as $config)
            @php
                $info    = $labels[$config->clave] ?? ['label' => $config->clave, 'desc' => $config->descripcion ?? ''];
                $inputId = 'cfg_' . str_replace('.', '_', $config->clave);
                $name    = 'config_' . $config->clave;
            @endphp

            <div class="flex items-center gap-6 px-6 py-4
                        {{ $grupo === 'modulos' ? 'transition-all duration-200' : '' }}"
                 style="border-bottom:1px solid rgba(99,130,246,0.07);{{ $grupo === 'modulos' && !$config->valor_casteado ? 'opacity:0.65;' : '' }}">

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <label for="{{ $inputId }}"
                           class="font-semibold text-sm text-white cursor-pointer select-none">
                        {{ $info['label'] }}
                    </label>
                    @if($info['desc'])
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.50);line-height:1.5;">
                        {{ $info['desc'] }}
                    </div>
                    @endif
                </div>

                {{-- Control --}}
                <div class="flex-shrink-0 flex items-center gap-2">

                    @if($config->tipo === 'boolean')
                        {{-- Toggle --}}
                        <span class="text-xs font-bold mr-1"
                              style="color:rgba(255,255,255,0.50);"
                              id="{{ $inputId }}_label">
                            {{ $config->valor_casteado ? 'Activado' : 'Desactivado' }}
                        </span>
                        <label class="toggle-wrapper">
                            <input type="checkbox"
                                   id="{{ $inputId }}"
                                   name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                                   value="true"
                                   class="toggle-input"
                                   {{ $config->valor_casteado ? 'checked' : '' }}
                                   onchange="document.getElementById('{{ $inputId }}_label').textContent = this.checked ? 'Activado' : 'Desactivado'">
                            <span class="toggle-slider"></span>
                        </label>

                    @elseif($config->tipo === 'integer')
                        <input type="number"
                               id="{{ $inputId }}"
                               name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                               value="{{ $config->valor }}"
                               min="0"
                               max="99999"
                               class="admin-input text-center"
                               style="width:6rem;font-variant-numeric:tabular-nums;">

                    @elseif($config->clave === 'impresora.tipo_conexion')
                        <select id="{{ $inputId }}"
                                name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                                class="admin-select"
                                style="min-width:13rem;"
                                onchange="toggleCamposImpresora(this.value)">
                            @foreach($opcionesConexion as $val => $etiqueta)
                            <option value="{{ $val }}" {{ $config->valor === $val ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                            @endforeach
                        </select>

                    @elseif($config->clave === 'impresora.ancho_papel')
                        <select id="{{ $inputId }}"
                                name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                                class="admin-select"
                                style="min-width:13rem;">
                            @foreach($opcionesPapel as $val => $etiqueta)
                            <option value="{{ $val }}" {{ $config->valor === $val ? 'selected' : '' }}>
                                {{ $etiqueta }}
                            </option>
                            @endforeach
                        </select>

                    @elseif($config->clave === 'impresora.ip')
                        <input type="text"
                               id="{{ $inputId }}"
                               name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                               value="{{ $config->valor }}"
                               placeholder="192.168.1.100"
                               pattern="^(\d{1,3}\.){3}\d{1,3}$"
                               class="admin-input"
                               style="width:13rem;font-family:monospace;"
                               id="campo-ip-impresora">

                    @else
                        <input type="text"
                               id="{{ $inputId }}"
                               name="cfg[{{ str_replace('.','_',$config->clave) }}]"
                               value="{{ $config->valor }}"
                               class="admin-input"
                               style="width:13rem;">
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Panel extra pagos: Carga directa de Código QR DeUna --}}
            @if($grupo === 'pagos')
            @php
                $qrActual = \App\Models\QrCuenta::activa();
            @endphp
            <div class="px-6 py-5" style="background:rgba(201,168,76,0.03);border-top:1px solid rgba(201,168,76,0.12);">
                <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
                    <div class="flex gap-4 items-center">
                        <div style="width:70px;height:70px;border-radius:0.75rem;background:white;padding:0.4rem;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.3);overflow:hidden;flex-shrink:0;">
                            @if($qrActual && $qrActual->qr_url)
                                <img src="{{ $qrActual->qr_url }}" id="preview-qr-img" alt="QR DeUna" style="width:100%;height:100%;object-fit:contain;">
                            @else
                                <div id="preview-qr-placeholder" style="color:#9ca3af;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">
                                    <x-admin.icon name="qr" class="w-7 h-7 opacity-60 text-gray-400" />
                                    <span style="font-size:0.5rem;font-weight:700;margin-top:2px;">Sin QR</span>
                                </div>
                                <img src="" id="preview-qr-img" alt="QR DeUna" style="display:none;width:100%;height:100%;object-fit:contain;">
                            @endif
                        </div>
                        <div>
                            <div class="font-display font-bold text-sm text-white flex items-center gap-2">
                                <span>Imagen del Código QR DeUna</span>
                                @if($qrActual && $qrActual->qr_url)
                                    <span class="badge badge-success text-[0.65rem] py-0.5">Configurado</span>
                                @else
                                    <span class="badge badge-danger text-[0.65rem] py-0.5">No configurado</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-1" style="line-height:1.5;">
                                Sube la foto o captura del código QR de tu cuenta DeUna para que los clientes lo escaneen en el Kiosco.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input type="file" name="imagen_qr" id="input-imagen-qr" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewQrConfig(this)">
                        <button type="button" onclick="document.getElementById('input-imagen-qr').click()" class="btn-ghost text-xs py-2 px-4 flex items-center gap-2 w-full sm:w-auto justify-center">
                            <x-admin.icon name="qr" class="w-4 h-4 text-amber-400" />
                            <span>{{ ($qrActual && $qrActual->qr_url) ? 'Cambiar imagen QR' : 'Subir imagen QR' }}</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Panel extra impresora: info visual según conexión --}}
            @if($grupo === 'impresora')
            <div id="info-impresora" class="px-6 py-4"
                 style="background:rgba(99,102,241,0.04);border-top:1px solid rgba(99,102,241,0.1);">
                <div class="flex gap-3 items-start">
                    <span class="text-xl flex-shrink-0" id="ico-conexion"></span>
                    <div class="text-xs" style="color:rgba(255,255,255,0.50);line-height:1.7;" id="desc-conexion">
                        <strong class="text-white">USB:</strong>
                        Conecta la impresora directamente al equipo por USB. No se requiere IP.
                        Asegúrate de instalar el driver ESC/POS del fabricante en el servidor.
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
    @endforeach

    {{-- Botón guardar al final --}}
    <div class="flex justify-end gap-3 pb-6">
        <a href="{{ route('admin.dashboard') }}"
           class="btn-ghost px-6 py-2.5">Cancelar</a>
        <button type="submit" class="btn-gold px-10 py-2.5">
            Guardar todos los cambios
        </button>
    </div>

</form>

<script>
const DESC_CONEXION = {
    usb: {
        ico: '',
        texto: '<strong class="text-white">USB:</strong> Conecta la impresora directamente al equipo por USB. No se requiere IP. Asegúrate de instalar el driver ESC/POS del fabricante en el servidor.',
    },
    red: {
        ico: '',
        texto: '<strong class="text-white">Red (Wi-Fi / Ethernet):</strong> La impresora debe tener una IP fija en la red local. Configura la IP exactamente como aparece en el panel de la impresora. Puerto por defecto: 9100.',
    },
    bluetooth: {
        ico: '',
        texto: '<strong class="text-white">Bluetooth:</strong> Empareja la impresora con el equipo antes de habilitar. El sistema detectará el puerto COM asignado automáticamente. No se requiere IP.',
    },
};

function toggleCamposImpresora(tipo) {
    const campoIp  = document.getElementById('cfg_impresora_ip');
    const filaIp   = campoIp?.closest('.flex.items-center');
    const infoBox  = document.getElementById('info-impresora');
    const icoEl    = document.getElementById('ico-conexion');
    const descEl   = document.getElementById('desc-conexion');

    // Mostrar/ocultar campo IP
    if (filaIp) {
        filaIp.style.display = tipo === 'red' ? '' : 'none';
    }

    // Actualizar descripción
    if (icoEl && descEl && DESC_CONEXION[tipo]) {
        icoEl.textContent  = DESC_CONEXION[tipo].ico;
        descEl.innerHTML   = DESC_CONEXION[tipo].texto;
    }
}

// Aplicar estado inicial al cargar
document.addEventListener('DOMContentLoaded', () => {
    const selectConexion = document.getElementById('cfg_impresora_tipo_conexion');
    if (selectConexion) toggleCamposImpresora(selectConexion.value);
});

function previewQrConfig(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('preview-qr-img');
            const ph = document.getElementById('preview-qr-placeholder');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

//recarga automatica
// ── Submit ────────────────────────────────────────────────────────
document.getElementById('config-form').addEventListener('submit', function() {
    document.querySelectorAll('[form="config-form"], #config-form [type="submit"]')
        .forEach(b => { b.disabled = true; b.textContent = 'Guardando...'; });
});

</script>

@endsection