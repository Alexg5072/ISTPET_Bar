@extends('layouts.app')
@section('title','Stock')
@section('page-icon')
<x-admin.icon name="stock" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Control de Stock')
@section('page-subtitle','Activa · Desactiva · Ajusta cantidades en tiempo real')
@section('header-actions')
    <a href="{{ route('admin.stock.movimientos') }}" class="btn-ghost">Movimientos</a>
@endsection

@section('content')
<div class="space-y-5 pt-2">

    @php
        $agotados = $productos->filter(fn($p) => !$p->stock_activo || $p->stock_actual == 0);
        $bajos    = $productos->filter(fn($p) => $p->stock_activo && $p->stock_actual > 0 && $p->stock_actual <= $p->stock_minimo);
    @endphp

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="admin-card p-4 flex items-center gap-3">
            <div class="stock-kpi-icon flex items-center justify-center" style="background:rgba(201,168,76,0.1);color:#c9a84c;"><x-admin.icon name="stock" class="w-5 h-5" /></div>
            <div>
                <div class="stock-kpi-val">{{ $productos->count() }}</div>
                <div class="stock-kpi-label">Productos</div>
            </div>
        </div>
        <div class="admin-card p-4 flex items-center gap-3">
            <div class="stock-kpi-icon flex items-center justify-center" style="background:rgba(16,185,129,0.1);color:#10b981;"><x-admin.icon name="check-circle" class="w-5 h-5" /></div>
            <div>
                <div class="stock-kpi-val" style="color:#6ee7b7;">{{ $productos->where('stock_activo',true)->count() }}</div>
                <div class="stock-kpi-label">Activos</div>
            </div>
        </div>
        <div class="admin-card p-4 flex items-center gap-3">
            <div class="stock-kpi-icon flex items-center justify-center" style="background:rgba(250,204,21,0.1);color:#facc15;"><x-admin.icon name="alert" class="w-5 h-5" /></div>
            <div>
                <div class="stock-kpi-val" style="color:{{ $bajos->count() > 0 ? '#facc15' : 'rgba(255,255,255,0.35)' }};">{{ $bajos->count() }}</div>
                <div class="stock-kpi-label">Stock bajo</div>
            </div>
        </div>
        <div class="admin-card p-4 flex items-center gap-3">
            <div class="stock-kpi-icon flex items-center justify-center" style="background:rgba(239,68,68,0.1);color:#ef4444;"><x-admin.icon name="ban" class="w-5 h-5" /></div>
            <div>
                <div class="stock-kpi-val" style="color:{{ $agotados->count() > 0 ? '#f87171' : 'rgba(255,255,255,0.35)' }};">{{ $agotados->count() }}</div>
                <div class="stock-kpi-label">Agotados</div>
            </div>
        </div>
    </div>

    {{-- Alerta agotados --}}
    @if($agotados->isNotEmpty())
    <div class="admin-card p-4 flex gap-3 items-start"
         style="background:rgba(239,68,68,0.05);border-color:rgba(239,68,68,0.2);">
        <x-admin.icon name="ban" class="w-5 h-5 text-rose-400 flex-shrink-0" />
        <div>
            <div class="text-sm font-bold mb-0.5" style="color:#fca5a5;">{{ $agotados->count() }} producto(s) agotado(s)</div>
            <div class="text-xs" style="color:rgba(239,68,68,0.7);">
                {{ $agotados->take(6)->pluck('nombre')->join(', ') }}{{ $agotados->count() > 6 ? '...' : '' }}
            </div>
        </div>
    </div>
    @endif

    {{-- Tabla de stock --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Inventario de productos</div>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="color:rgba(255,255,255,0.50)">Producto</th>
                        <th style="color:rgba(255,255,255,0.50)">Categoría</th>
                        <th style="text-align:center; color:rgba(255,255,255,0.50)">Stock actual</th>
                        <th style="text-align:center; color:rgba(255,255,255,0.50)">Mínimo</th>
                        <th style="text-align:center; color:rgba(255,255,255,0.50)">Estado visual</th>
                        <th style="text-align:center; color:rgba(255,255,255,0.50)">Disponible</th>
                        <th style="color:rgba(255,255,255,0.50)">Ajustar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    @php
                        $pct = $producto->stock_minimo > 0
                            ? min(100, round($producto->stock_actual / max($producto->stock_minimo * 3, 1) * 100))
                            : ($producto->stock_actual > 0 ? 100 : 0);
                        $color = $producto->stock_actual <= 0
                            ? '#f87171'
                            : ($producto->stock_actual <= $producto->stock_minimo ? '#facc15' : '#6ee7b7');
                    @endphp
                    <tr class="{{ !$producto->stock_activo ? 'opacity-50' : '' }}">
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="prod-thumb">
                                    <img src="{{ $producto->imagen_url }}" alt="" class="prod-img">
                                </div>
                                <span class="prod-nombre" style="font-size:0.82rem;">{{ $producto->nombre }}</span>
                            </div>
                        </td>
                        <td><span class="badge badge-gray">{{ $producto->categoria?->icono }} {{ $producto->categoria?->nombre }}</span></td>
                        <td style="text-align:center;">
                            <span style="font-family:var(--font-display);font-weight:900;font-size:1.3rem;color:{{ $color }};">
                                {{ $producto->stock_actual }}
                            </span>
                        </td>
                        <td style="text-align:center;color:rgba(255,255,255,0.50);font-size:0.8rem;">
                            {{ $producto->stock_minimo }}
                        </td>
                        <td style="padding:0.75rem 1rem;">
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div style="flex:1;height:6px;background:rgba(255,255,255,0.06);border-radius:999px;overflow:hidden;min-width:60px;">
                                    <div style="height:100%;border-radius:999px;width:{{ $pct }}%;background:{{ $color }};transition:width 0.5s ease;"></div>
                                </div>
                                @if($producto->stock_actual <= 0)
                                    <span class="badge badge-danger" style="font-size:0.55rem;padding:2px 6px;">agot.</span>
                                @elseif($producto->stock_actual <= $producto->stock_minimo)
                                    <span class="badge badge-pending" style="font-size:0.55rem;padding:2px 6px;">bajo</span>
                                @else
                                    <span class="badge badge-success" style="font-size:0.55rem;padding:2px 6px;">ok</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.productos.toggle-stock', $producto) }}">
                                @csrf @method('PATCH')
                                <label class="toggle-wrapper">
                                    <input type="checkbox" class="toggle-input"
                                           {{ $producto->stock_activo ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                        </td>
                        <td>
                            <button onclick="abrirAjuste({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->stock_actual }})"
                                    class="btn-blue text-xs py-1.5 px-3">
                                Ajustar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-12" style="color:rgba(255,255,255,0.40);">Sin productos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══ MODAL AJUSTE DE STOCK ══ --}}
<div id="modal-stock"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);
            backdrop-filter:blur(6px);align-items:center;justify-content:center;">

    <div onclick="event.stopPropagation()"
         style="background:#1a1f2e;border:1px solid rgba(255,255,255,0.1);border-radius:1.25rem;
                padding:1.75rem;width:100%;max-width:420px;margin:1rem;
                box-shadow:0 32px 70px rgba(0,0,0,0.6);animation:modalIn 0.25s ease both;">

        {{-- Header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:2.5rem;height:2.5rem;border-radius:0.6rem;background:rgba(201,168,76,0.12);
                            border:1px solid rgba(201,168,76,0.25);display:flex;align-items:center;
                            justify-content:center;"><x-admin.icon name="stock" class="w-6 h-6 text-amber-400" /></div>
                <div>
                    <div style="font-family:var(--font-display);font-weight:900;font-size:1.1rem;color:white;line-height:1.2;"
                         id="modal-stock-title">Ajustar Stock</div>
                    <div style="font-size:0.7rem;color:rgba(255,255,255,0.40);margin-top:2px;text-transform:uppercase;letter-spacing:0.06em;">
                        Control de inventario
                    </div>
                </div>
            </div>
            <button onclick="cerrarStock()"
                    style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);
                           border-radius:0.5rem;width:2rem;height:2rem;color:rgba(255,255,255,0.45);
                           cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;
                           transition:all 0.15s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='#f87171'"
                    onmouseout="this.style.background='rgba(255,255,255,0.06)';this.style.color='rgba(255,255,255,0.45)'">✕</button>
        </div>

        {{-- Stock actual display --}}
        <div style="display:flex;align-items:center;justify-content:space-between;
                    padding:1rem 1.25rem;border-radius:0.875rem;margin-bottom:1.25rem;
                    background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);">
            <div>
                <div style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
                             color:rgba(255,255,255,0.35);margin-bottom:0.25rem;">Stock actual</div>
                <div style="font-family:var(--font-display);font-weight:900;font-size:2rem;line-height:1;"
                     id="modal-stock-actual-num">0</div>
                <div style="font-size:0.7rem;color:rgba(255,255,255,0.35);margin-top:2px;">unidades en inventario</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
                             color:rgba(255,255,255,0.35);margin-bottom:0.25rem;">Resultado</div>
                <div style="font-family:var(--font-display);font-weight:900;font-size:2rem;line-height:1;color:#c9a84c;"
                     id="modal-stock-resultado">0</div>
                <div style="font-size:0.7rem;color:rgba(201,168,76,0.5);margin-top:2px;">nuevo total</div>
            </div>
        </div>

        <form action="{{ route('admin.stock.ajuste') }}" method="POST">
            @csrf
            <input type="hidden" name="producto_id" id="stock-producto-id">
            {{-- Siempre enviamos tipo=entrada para sumar, o ajuste para valor exacto --}}
            <input type="hidden" name="tipo" id="stock-tipo" value="entrada">

            {{-- Tabs: Agregar / Establecer --}}
            <div style="display:flex;gap:0.5rem;margin-bottom:1.25rem;
                        background:rgba(255,255,255,0.04);border-radius:0.6rem;padding:0.25rem;">
                <button type="button" id="tab-agregar"
                        onclick="setModo('agregar')"
                        style="flex:1;padding:0.55rem;border-radius:0.4rem;border:none;cursor:pointer;
                               font-family:var(--font-display);font-weight:800;font-size:0.72rem;
                               text-transform:uppercase;letter-spacing:0.06em;transition:all 0.2s;
                               background:linear-gradient(135deg,#a07d2e,#c9a84c);color:#0b1133;">
                    Agregar
                </button>
                <button type="button" id="tab-establecer"
                        onclick="setModo('establecer')"
                        style="flex:1;padding:0.55rem;border-radius:0.4rem;border:none;cursor:pointer;
                               font-family:var(--font-display);font-weight:800;font-size:0.72rem;
                               text-transform:uppercase;letter-spacing:0.06em;transition:all 0.2s;
                               background:transparent;color:rgba(255,255,255,0.4);">
                    Establecer
                </button>
            </div>

            {{-- Hint del modo activo --}}
            <div id="hint-agregar"
                 style="padding:0.5rem 0.75rem;border-radius:0.6rem;margin-bottom:1rem;
                        background:rgba(201,168,76,0.07);border:1px solid rgba(201,168,76,0.18);">
                <span style="font-size:0.72rem;color:rgba(201,168,76,0.85);">
                    Ingresa cuántas unidades quieres <strong>agregar</strong> al stock actual.
                </span>
            </div>
            <div id="hint-establecer"
                 style="display:none;padding:0.5rem 0.75rem;border-radius:0.6rem;margin-bottom:1rem;
                        background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.18);">
                <span style="font-size:0.72rem;color:rgba(147,197,253,0.85);">
                    Ingresa la <strong>cantidad exacta</strong> que debe quedar en inventario.
                </span>
            </div>

            {{-- Campo cantidad --}}
            <div style="margin-bottom:1rem;">
                <label style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;
                              letter-spacing:0.08em;color:rgba(255,255,255,0.50);margin-bottom:0.4rem;"
                       id="label-cantidad">Cantidad a agregar</label>
                <div style="position:relative;">
                    <input name="cantidad" id="input-cantidad" type="number" min="0"
                           class="admin-input" placeholder="Ej: 10" required
                           oninput="actualizarResultado()"
                           style="padding-right:3.5rem;font-family:var(--font-display);font-weight:800;font-size:1.1rem;">
                    <div style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);
                                font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.25);
                                text-transform:uppercase;letter-spacing:0.06em;">uds.</div>
                </div>
            </div>

            {{-- Campo motivo --}}
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:0.7rem;font-weight:700;text-transform:uppercase;
                              letter-spacing:0.08em;color:rgba(255,255,255,0.50);margin-bottom:0.4rem;">Motivo <span style="color:rgba(255,255,255,0.25);font-weight:400;">(opcional)</span></label>
                <input name="motivo" class="admin-input" placeholder="Ej: Compra de insumos, Conteo físico...">
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="btn-gold flex-1 justify-center py-3">Guardar ajuste</button>
                <button type="button" onclick="cerrarStock()" class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalIn { from{opacity:0;transform:scale(0.94) translateY(8px);} to{opacity:1;transform:scale(1) translateY(0);} }
.stock-kpi-icon { width:2.5rem;height:2.5rem;border-radius:0.6rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0; }
.stock-kpi-val  { font-family:var(--font-display);font-weight:900;font-size:1.5rem;color:white;line-height:1; }
.stock-kpi-label{ font-size:0.62rem;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-top:1px; }
.prod-thumb { width:36px;height:36px;border-radius:0.5rem;overflow:hidden;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);flex-shrink:0; }
.prod-img   { width:100%;height:100%;object-fit:cover; }
.prod-nombre{ font-weight:700;color:white; }
</style>

@push('scripts')
<script>
let _stockActual = 0;
let _modo = 'agregar'; // 'agregar' | 'establecer'

function abrirAjuste(id, nombre, stock) {
    _stockActual = parseInt(stock) || 0;
    document.getElementById('stock-producto-id').value = id;
    document.getElementById('modal-stock-title').textContent = nombre;

    // Color del stock actual
    const colorStock = _stockActual <= 0 ? '#f87171' : (_stockActual <= 2 ? '#facc15' : '#6ee7b7');
    const numEl = document.getElementById('modal-stock-actual-num');
    numEl.textContent  = _stockActual;
    numEl.style.color  = colorStock;

    // Reset
    document.getElementById('input-cantidad').value = '';
    document.getElementById('modal-stock-resultado').textContent = _stockActual;
    setModo('agregar');

    document.getElementById('modal-stock').style.display = 'flex';
    setTimeout(() => document.getElementById('input-cantidad').focus(), 120);
}

function cerrarStock() {
    document.getElementById('modal-stock').style.display = 'none';
}

function setModo(modo) {
    _modo = modo;

    const tabAgregar    = document.getElementById('tab-agregar');
    const tabEstablecer = document.getElementById('tab-establecer');
    const hintAgregar   = document.getElementById('hint-agregar');
    const hintEst       = document.getElementById('hint-establecer');
    const labelCant     = document.getElementById('label-cantidad');
    const inputCant     = document.getElementById('input-cantidad');
    const tipoInput     = document.getElementById('stock-tipo');

    if (modo === 'agregar') {
        tabAgregar.style.background = 'linear-gradient(135deg,#a07d2e,#c9a84c)';
        tabAgregar.style.color      = '#0b1133';
        tabEstablecer.style.background = 'transparent';
        tabEstablecer.style.color      = 'rgba(255,255,255,0.4)';
        hintAgregar.style.display   = 'block';
        hintEst.style.display       = 'none';
        labelCant.textContent       = 'Cantidad a agregar';
        inputCant.placeholder       = 'Ej: 10';
        inputCant.min               = '1';
        tipoInput.value             = 'entrada';
    } else {
        tabEstablecer.style.background = 'linear-gradient(135deg,#1e3a8a,#3b82f6)';
        tabEstablecer.style.color      = 'white';
        tabAgregar.style.background    = 'transparent';
        tabAgregar.style.color         = 'rgba(255,255,255,0.4)';
        hintAgregar.style.display   = 'none';
        hintEst.style.display       = 'block';
        labelCant.textContent       = 'Nueva cantidad exacta';
        inputCant.placeholder       = 'Ej: 25';
        inputCant.min               = '0';
        tipoInput.value             = 'ajuste';
    }

    inputCant.value = '';
    actualizarResultado();
}

function actualizarResultado() {
    const val = parseInt(document.getElementById('input-cantidad').value) || 0;
    const resultado = _modo === 'agregar' ? _stockActual + val : val;
    const el = document.getElementById('modal-stock-resultado');
    el.textContent = resultado;
    el.style.color = resultado <= 0 ? '#f87171' : (resultado <= 2 ? '#facc15' : '#c9a84c');
}

document.getElementById('modal-stock').addEventListener('click', function(e) {
    if (e.target === this) cerrarStock();
});

/* ── Auto-actualización: si el stock cambia desde otro lugar (kiosco,
   otra pestaña de admin, etc.), esta pantalla se refresca sola.
   No interrumpe si el admin tiene el modal de ajuste abierto. ── */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-stock').style.display === 'flex';
    if (!modalAbierto) {
        location.reload();
    }
});
</script>
<script src="{{ asset('js/kiosco-realtime.js') }}"></script>
@endpush
@endsection