@extends('layouts.app')
@section('title','Reportes')
@section('page-title','Reportes y Estadísticas')
@section('page-subtitle','Ventas por sede · Productos más vendidos')

@section('header-actions')
    <div style="position:relative;display:inline-block;" x-data="{open:false}">
        <button @click="open=!open" class="btn-ghost" style="display:flex;align-items:center;gap:0.4rem;">
            <x-admin.icon name="reportes" class="w-4 h-4" />
            <span>Exportar</span>
            <span style="font-size:0.6rem;opacity:.5;">▼</span>
        </button>
        <div x-show="open" @click.outside="open=false"
             style="position:absolute;right:0;top:calc(100% + 6px);z-index:50;
                    background:#1a1f32;border:1px solid rgba(255,255,255,0.1);
                    border-radius:0.75rem;overflow:hidden;min-width:180px;
                    box-shadow:0 16px 40px rgba(0,0,0,0.5);">
            <a href="{{ route('admin.reportes.exportar') }}?tipo=excel&periodo={{ request('periodo','hoy') }}&fecha={{ request('fecha',today()->toDateString()) }}"
               style="display:flex;align-items:center;gap:0.65rem;padding:0.7rem 1rem;
                      font-size:0.78rem;font-weight:700;color:rgba(255,255,255,0.7);
                      text-decoration:none;transition:background 0.15s ease;font-family:var(--font-display);"
               onmouseover="this.style.background='rgba(255,255,255,0.06)'"
               onmouseout="this.style.background=''">
                <x-admin.icon name="reportes" class="w-4 h-4 text-emerald-400" />
                <div>
                    <div>Exportar CSV</div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.50);font-weight:600;">Abrir en Excel</div>
                </div>
            </a>
            <div style="height:1px;background:rgba(255,255,255,0.05);margin:0 10px;"></div>
            <a href="{{ route('admin.reportes.exportar') }}?tipo=pdf&periodo={{ request('periodo','hoy') }}&fecha={{ request('fecha',today()->toDateString()) }}"
               target="_blank"
               style="display:flex;align-items:center;gap:0.65rem;padding:0.7rem 1rem;
                      font-size:0.78rem;font-weight:700;color:rgba(255,255,255,0.7);
                      text-decoration:none;transition:background 0.15s ease;font-family:var(--font-display);"
               onmouseover="this.style.background='rgba(255,255,255,0.06)'"
               onmouseout="this.style.background=''">
                <x-admin.icon name="receipt" class="w-4 h-4 text-blue-400" />
                <div>
                    <div>Descargar PDF</div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.50);font-weight:600;">Abre diálogo · Guardar como PDF</div>
                </div>
            </a>
        </div>
    </div>
    <a href="{{ route('admin.caja.index') }}" class="btn-gold flex items-center gap-1.5">
        <x-admin.icon name="caja" class="w-4 h-4" />
        <span>Ver Caja</span>
    </a>
@endsection

@section('content')
<div class="space-y-6 pt-2">

    {{-- ── Selector de período ── --}}
    <x-admin.periodo-selector :periodo="$periodo" :fecha="$fecha" :periodoLabel="$periodoLabel" />

    {{-- ── Cajas por sede ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($datos as $item)
        <div class="admin-card rpt-sede-card animate-fade-in" style="animation-delay:{{ $loop->index * 0.1 }}s">

            {{-- Header sede --}}
            <div class="rpt-sede-header">
                <div class="rpt-sede-glow"></div>
                <span class="rpt-sede-icon text-amber-400/90">
                    <x-admin.icon :name="$item['sede']->slug === 'instituto' ? 'instituto' : 'car'" class="w-6 h-6" />
                </span>
                <div>
                    <div class="rpt-sede-name">{{ $item['sede']->nombre }}</div>
                    <div class="rpt-sede-sub" style="color:rgba(255,255,255,0.50);">Reporte del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
                </div>
                <div class="ml-auto">
                    <div class="rpt-total-badge">${{ number_format($item['total'], 2) }}</div>
                    <div class="rpt-total-label" style="color:rgba(255,255,255,0.50);">Total facturado</div>
                </div>
            </div>

            {{-- Grid de métricas --}}
            <div class="grid grid-cols-3 gap-3 p-4">
                <div class="rpt-metric rpt-metric-gold">
                    <div class="rpt-metric-label" style="color:rgba(255,255,255,0.50);">Cobrado</div>
                    <div class="rpt-metric-value" style="color:#6ee7b7;">${{ number_format($item['cobrado'], 2) }}</div>
                    @php $pct = $item['total'] > 0 ? round(($item['cobrado']/$item['total'])*100) : 0; @endphp
                    <div class="rpt-mini-bar"><div style="width:{{ $pct }}%;background:linear-gradient(90deg,#064e3b,#6ee7b7);"></div></div>
                    <div class="rpt-metric-pct" style="color:rgba(255,255,255,0.50);">{{ $pct }}% cobrado</div>
                </div>
                <div class="rpt-metric rpt-metric-blue">
                    <div class="rpt-metric-label" style="color:rgba(255,255,255,0.50);">Pedidos</div>
                    <div class="rpt-metric-value" style="color:#60a5fa;">{{ $item['count'] }}</div>
                    <div class="rpt-metric-sub" style="color:rgba(255,255,255,0.50);">del día</div>
                </div>
                <div class="rpt-metric rpt-metric-gray">
                    <div class="rpt-metric-label" style="color:rgba(255,255,255,0.50);">Ticket prom.</div>
                    <div class="rpt-metric-value" style="color:white;">${{ number_format($item['ticket_prom'], 2) }}</div>
                    <div class="rpt-metric-sub" style="color:rgba(255,255,255,0.50);">por pedido</div>
                </div>
            </div>

            {{-- CTA cierre --}}
            <div class="px-4 pb-4">
                <a href="{{ route('admin.caja.cierre', $item['sede']) }}?periodo={{ $periodo }}&fecha={{ $fecha }}"
                   class="btn-ghost w-full justify-center py-2.5 text-xs">
                    Imprimir cierre de caja
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Más vendidos ── --}}
    <div class="admin-card animate-fade-in" style="animation-delay:.25s">
        <div class="admin-card-header">
            <div class="admin-card-title flex items-center gap-2">
                <x-admin.icon name="star" class="w-4 h-4 text-amber-400" />
                <span>Productos más vendidos</span>
            </div>
            <span class="rpt-label" style="margin:0; color:rgba(255,255,255,0.50);">Top 10 del día</span>
        </div>

        @if($masVendidos->isEmpty())
        <div class="text-center py-14" style="color:rgba(255,255,255,0.18);font-size:0.9rem;">
            Sin ventas registradas para esta fecha.
        </div>
        @else

        {{-- Barras visuales --}}
        @php $maxVentas = $masVendidos->first()?->total_unidades ?? 1; @endphp
        <div class="p-5 space-y-3 border-b" style="border-color:rgba(255,255,255,0.05);">
            @foreach($masVendidos->take(5) as $i => $prod)
            <div class="flex items-center gap-3">
                <div class="rpt-rank font-black font-display" style="color:{{ $i < 3 ? '#c9a84c' : 'rgba(255,255,255,0.2)' }}">
                    #{{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="rpt-prod-name">{{ $prod->nombre_snapshot }}</span>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-2">
                            <span class="rpt-prod-units" style="color:rgba(255,255,255,0.50)">{{ $prod->total_unidades }} uds.</span>
                            <span class="rpt-prod-price">${{ number_format($prod->total_ingresos, 2) }}</span>
                        </div>
                    </div>
                    <div class="rpt-bar-track">
                        <div class="rpt-bar-fill" style="
                            width:{{ round($prod->total_unidades / $maxVentas * 100) }}%;
                            background:{{ $i === 0 ? 'linear-gradient(90deg,#9a7328,#c9a84c)' : ($i < 3 ? 'linear-gradient(90deg,#1e3a8a,#3b82f6)' : 'rgba(255,255,255,0.12)') }};">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Tabla completa --}}
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr><th style="color:rgba(255,255,255,0.50)">#</th><th style="color:rgba(255,255,255,0.50)">Producto</th><th style="color:rgba(255,255,255,0.50)">Unidades</th><th style="color:rgba(255,255,255,0.50)">Ingresos</th><th style="color:rgba(255,255,255,0.50)">Participación</th></tr>
                </thead>
                <tbody>
                    @foreach($masVendidos as $i => $prod)
                    <tr>
                        <td style="font-family:var(--font-display);font-weight:900;
                                   color:{{ $i < 3 ? '#c9a84c' : 'rgba(255,255,255,0.25)' }};">
                            #{{ $i + 1 }}
                        </td>
                        <td class="col-name">{{ $prod->nombre_snapshot }}</td>
                        <td><span class="badge badge-blue">{{ $prod->total_unidades }} uds.</span></td>
                        <td class="col-price">${{ number_format($prod->total_ingresos, 2) }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="rpt-bar-track" style="flex:1;">
                                    <div class="rpt-bar-fill"
                                         style="width:{{ round($prod->total_unidades / $maxVentas * 100) }}%;
                                                background:linear-gradient(90deg,#9a7328,#c9a84c);">
                                    </div>
                                </div>
                                <span style="font-size:0.7rem;color:rgba(255,255,255,0.50);width:2.5rem;text-align:right;">
                                    {{ round($prod->total_unidades / $maxVentas * 100) }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

<style>
/* ── Labels ── */
.rpt-label {
    display: block;
    font-size: 0.6rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: rgba(255,255,255,0.28);
    font-family: var(--font-display);
    margin-bottom: 0.3rem;
}
.rpt-date-display {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.3);
    font-weight: 600;
    align-self: center;
    margin-left: 0.25rem;
}

/* ── Card sede ── */
.rpt-sede-card { overflow: hidden; }
.rpt-sede-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: linear-gradient(135deg, rgba(27,42,107,0.5), rgba(27,42,107,0.2));
    position: relative;
    overflow: hidden;
}
.rpt-sede-glow {
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(201,168,76,0.12), transparent 70%);
    pointer-events: none;
}
.rpt-sede-icon { font-size: 1.8rem; flex-shrink: 0; }
.rpt-sede-name {
    font-family: var(--font-display);
    font-weight: 900;
    font-size: 0.88rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.rpt-sede-sub { font-size: 0.68rem; color: rgba(255,255,255,0.3); margin-top: 1px; }
.rpt-total-badge {
    font-family: var(--font-display);
    font-weight: 900;
    font-size: 1.5rem;
    color: #c9a84c;
    text-align: right;
    line-height: 1;
}
.rpt-total-label { font-size: 0.6rem; color: rgba(255,255,255,0.25); text-align: right; margin-top: 2px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }

/* ── Métricas ── */
.rpt-metric {
    border-radius: 0.75rem;
    padding: 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.rpt-metric-gold { background: rgba(201,168,76,0.06); border: 1px solid rgba(201,168,76,0.12); }
.rpt-metric-blue { background: rgba(96,165,250,0.06); border: 1px solid rgba(96,165,250,0.12); }
.rpt-metric-gray { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); }
.rpt-metric-label { font-size: 0.58rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.28); font-family: var(--font-display); }
.rpt-metric-value { font-family: var(--font-display); font-weight: 900; font-size: 1.35rem; line-height: 1; }
.rpt-metric-sub { font-size: 0.65rem; color: rgba(255,255,255,0.2); }
.rpt-metric-pct { font-size: 0.62rem; color: rgba(255,255,255,0.2); margin-top: 1px; }
.rpt-mini-bar { height: 3px; background: rgba(255,255,255,0.05); border-radius: 999px; overflow: hidden; margin-top: 0.4rem; }
.rpt-mini-bar > div { height: 100%; border-radius: 999px; }

/* ── Barras top productos ── */
.rpt-rank { width: 1.75rem; flex-shrink: 0; font-size: 0.95rem; text-align: center; }
.rpt-prod-name { font-size: 0.82rem; font-weight: 600; color: rgba(255,255,255,0.75); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px; }
.rpt-prod-units { font-size: 0.7rem; color: rgba(255,255,255,0.28); }
.rpt-prod-price { font-family: var(--font-display); font-weight: 800; font-size: 0.82rem; color: #c9a84c; }
.rpt-bar-track { height: 5px; background: rgba(255,255,255,0.05); border-radius: 999px; overflow: hidden; }
.rpt-bar-fill { height: 100%; border-radius: 999px; transition: width 0.7s cubic-bezier(0.22,1,0.36,1); }
</style>
@endsection