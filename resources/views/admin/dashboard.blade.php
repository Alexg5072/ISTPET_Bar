@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-icon')
<x-admin.icon name="dashboard" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen · ' . $periodoLabel . ' · ' . now()->translatedFormat('d \d\e F, g:i a'))

@section('content')
<div class="space-y-4 pt-1 pb-20">

    {{-- ── Selector de período ── --}}
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <x-admin.periodo-selector :periodo="$periodo" :fecha="$fecha" :periodoLabel="$periodoLabel" />
    </div>

    {{-- ── KPI CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="kpi-card animate-fade-in">
            <div class="kpi-top">
                <span class="kpi-label">Ventas · {{ $periodoLabel }}</span>
                <div class="kpi-icon flex items-center justify-center" style="background:rgba(201,168,76,0.12);color:#c9a84c;"><x-admin.icon name="caja" class="w-6 h-6" /></div>
            </div>
            <div class="kpi-value" style="color:#c9a84c;">${{ number_format($ventasHoy, 2) }}</div>
            <div class="kpi-sub">{{ $pedidosHoy }} pedidos</div>
            <div class="kpi-bar"><div class="kpi-fill" style="width:{{ $pedidosHoy > 0 ? min(100,$pedidosHoy*4) : 6 }}%;background:linear-gradient(90deg,#9a7328,#c9a84c);"></div></div>
        </div>

        <div class="kpi-card animate-fade-in" style="animation-delay:.07s">
            <div class="kpi-top">
                <span class="kpi-label">Pendientes hoy</span>
                <div class="kpi-icon flex items-center justify-center" style="background:rgba(239,68,68,0.12);color:#f87171;"><x-admin.icon name="alert" class="w-6 h-6" /></div>
            </div>
            <div class="kpi-value" style="color:{{ $pendientes > 0 ? '#f87171' : '#6ee7b7' }};">{{ $pendientes }}</div>
            <div class="kpi-sub">{{ $pendientes > 0 ? 'Requieren atención' : 'Todo al día' }}</div>
            @if($pendientes > 0)
            <a href="{{ route('admin.pedidos.index') }}" class="kpi-action-btn">Ver pedidos →</a>
            @endif
        </div>

        @foreach($ventasPorSede as $slug => $info)
        <div class="kpi-card animate-fade-in" style="animation-delay:{{ $loop->index * 0.07 + 0.14 }}s">
            <div class="kpi-top">
                <span class="kpi-label">{{ $slug === 'instituto' ? 'Instituto' : 'Conducción' }}</span>
                <div class="kpi-icon flex items-center justify-center" style="background:rgba(99,102,241,0.12);color:#a5b4fc;">@if($slug === 'instituto')<x-admin.icon name="instituto" class="w-6 h-6" />@else<x-admin.icon name="car" class="w-6 h-6" />@endif</div>
            </div>
            <div class="kpi-value" style="color:#6ee7b7;">${{ number_format($info['ventas'], 2) }}</div>
            <div class="kpi-sub">{{ $info['pedidos'] }} pedidos</div>
            <div class="kpi-bar"><div class="kpi-fill" style="width:{{ $info['pedidos'] > 0 ? min(100,$info['pedidos']*6) : 6 }}%;background:linear-gradient(90deg,#064e3b,#6ee7b7);"></div></div>
        </div>
        @endforeach
    </div>

    {{-- ── ALERTAS + ACTIVIDAD ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

        <div class="admin-card animate-fade-in" style="animation-delay:.25s">
            <div class="admin-card-header">
                <div class="admin-card-title">Requieren atención</div>
            </div>
            <div class="p-3 space-y-2">
                @if($pedidosPendientesCobro->isNotEmpty())
                <div class="alert-row alert-yellow">
                    <div class="alert-dot" style="background:#facc15;"></div>
                    <div class="flex-1 min-w-0">
                        <div class="alert-title">Pendientes de cobro</div>
                        <div class="alert-desc">{{ $pedidosPendientesCobro->pluck('codigo')->join(', ') }}</div>
                    </div>
                    <span class="alert-count" style="color:#facc15;flex-shrink:0;">{{ $pedidosPendientesCobro->count() }}</span>
                </div>
                @endif
                @if($pedidosPendientesQr->isNotEmpty())
                <div class="alert-row alert-blue">
                    <div class="alert-dot" style="background:#60a5fa;"></div>
                    <div class="flex-1 min-w-0">
                        <div class="alert-title">Verificación QR pendiente</div>
                        <div class="alert-desc">{{ $pedidosPendientesQr->pluck('codigo')->join(', ') }}</div>
                    </div>
                    <span class="alert-count" style="color:#60a5fa;flex-shrink:0;">{{ $pedidosPendientesQr->count() }}</span>
                </div>
                @endif
                @if($productosAgotados->isNotEmpty())
                <div class="alert-row alert-red">
                    <div class="alert-dot" style="background:#f87171;"></div>
                    <div class="flex-1 min-w-0">
                        <div class="alert-title">Productos agotados</div>
                        <div class="alert-desc">{{ $productosAgotados->pluck('nombre')->join(', ') }}</div>
                    </div>
                    <span class="alert-count" style="color:#f87171;flex-shrink:0;">{{ $productosAgotados->count() }}</span>
                </div>
                @endif
                @if($pedidosPendientesCobro->isEmpty() && $pedidosPendientesQr->isEmpty() && $productosAgotados->isEmpty())
                <div class="text-center py-6" style="color:rgba(255,255,255,0.42);font-size:0.85rem;">
                    Todo en orden. Sin alertas activas.
                </div>
                @endif
            </div>
        </div>

        <div class="admin-card animate-fade-in" style="animation-delay:.3s">
            <div class="admin-card-header">
                <div class="admin-card-title">Actividad reciente</div>
            </div>
            <div class="divide-y" style="border-color:rgba(255,255,255,0.04);">
                @forelse($actividad as $log)
                <div class="flex items-start gap-3 px-3 py-2.5">
                    <div class="mt-1.5 flex-shrink-0" style="width:6px;height:6px;border-radius:50%;background:#c9a84c;"></div>
                    <div class="flex-1 min-w-0">
                        <div style="font-size:0.78rem;color:rgba(255,255,255,0.75);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->accion }}</div>
                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.45);margin-top:1px;">{{ $log->user?->name ?? 'Sistema' }}</div>
                    </div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.40);white-space:nowrap;flex-shrink:0;">{{ $log->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <div class="text-center py-6" style="color:rgba(255,255,255,0.50);font-size:0.85rem;">Sin actividad registrada.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── ÚLTIMOS PEDIDOS ── --}}
    <div class="admin-card animate-fade-in" style="animation-delay:.38s">
        <div class="admin-card-header">
            <div class="admin-card-title">Últimos pedidos — {{ $periodoLabel }}</div>
            <a href="{{ route('admin.pedidos.index') }}" class="btn-ghost text-xs py-1.5 px-3">Ver todos →</a>
        </div>
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
            <table class="admin-table" style="min-width:480px;">
                <thead><tr>
                    <th style="color:rgba(255,255,255,0.50)">Pedido</th>
                    <th style="color:rgba(255,255,255,0.50)">Sede</th>
                    <th style="color:rgba(255,255,255,0.50)">Método</th>
                    <th style="color:rgba(255,255,255,0.50)">Total</th>
                    <th style="color:rgba(255,255,255,0.50)">Estado</th>
                    <th style="color:rgba(255,255,255,0.50)">Hora</th>
                    <th style="color:rgba(255,255,255,0.50)">Acción</th>
                </tr></thead>
                <tbody>
                    @forelse($ultimosPedidos as $pedido)
                    @php
                        $bc = match($pedido->estado_badge['color']) {
                            'success'=>'badge-success','danger'=>'badge-danger',
                            'pending'=>'badge-pending','blue'=>'badge-blue',
                            'gold'=>'badge-gold',default=>'badge-gray'
                        };
                    @endphp
                    <tr>
                        <td class="col-id">{{ $pedido->codigo }}</td>
                        <td><span class="badge {{ $pedido->sede->slug==='instituto'?'badge-blue':'badge-gold' }}">
                            {{ $pedido->sede->slug==='instituto'?'IST':'Cond.' }}
                        </span></td>
                        <td style="font-size:0.78rem;">{{ $pedido->metodo_pago==='qr_deuna'?'QR':'Efectivo' }}</td>
                        <td class="col-price">{{ $pedido->total_formateado }}</td>
                        <td><span class="badge {{ $bc }}">{{ $pedido->estado_badge['label'] }}</span></td>
                        <td style="font-size:0.72rem;color:rgba(255,255,255,0.50);">{{ $pedido->created_at->format('d/m H:i') }}</td>
                        <td><a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn-ghost" style="font-size:0.7rem;padding:0.3rem 0.6rem;">Ver</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-10" style="color:rgba(255,255,255,0.50);">Sin pedidos en este período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
/* ── KPI Cards ── */
.kpi-card {
    background:#1a1f32;border:1px solid rgba(255,255,255,0.06);
    border-radius:1rem;padding:1.25rem;display:flex;flex-direction:column;
    gap:0.3rem;position:relative;overflow:hidden;
    transition:border-color 0.2s ease,transform 0.2s ease;
}
.kpi-card:hover { border-color:rgba(255,255,255,0.12);transform:translateY(-2px); }
.kpi-card::before { content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(201,168,76,0.4),transparent); }
.kpi-top   { display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem; }
.kpi-label { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.55);font-family:var(--font-display); }
.kpi-icon  { width:2.25rem;height:2.25rem;border-radius:0.6rem;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
.kpi-value { font-family:var(--font-display);font-weight:900;font-size:2rem;line-height:1; }
.kpi-sub   { font-size:0.72rem;color:rgba(255,255,255,0.50);margin-bottom:0.5rem; }
.kpi-bar   { height:3px;background:rgba(255,255,255,0.05);border-radius:999px;overflow:hidden;margin-top:auto; }
.kpi-fill  { height:100%;border-radius:999px; }
.kpi-action-btn { display:inline-block;margin-top:0.3rem;font-size:0.68rem;font-weight:700;color:#c9a84c;text-decoration:none;font-family:var(--font-display); }

/* ── Alertas ── */
.alert-row    { display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;border-radius:0.75rem; }
.alert-yellow { background:rgba(250,204,21,0.05);border:1px solid rgba(250,204,21,0.15); }
.alert-blue   { background:rgba(96,165,250,0.05);border:1px solid rgba(96,165,250,0.15); }
.alert-red    { background:rgba(248,113,113,0.05);border:1px solid rgba(248,113,113,0.15); }
.alert-dot    { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.alert-title  { font-size:0.82rem;font-weight:700;color:white; }
.alert-desc   { font-size:0.7rem;color:rgba(255,255,255,0.50);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.alert-count  { font-family:var(--font-display);font-weight:900;font-size:1.6rem;line-height:1; }

/* ════════════════════════════════════════
   DASHBOARD — TABLET < 1024px
════════════════════════════════════════ */
@media (max-width: 1023px) {
    .space-y-4 {
        overflow-x: hidden !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .space-y-4 > div:first-child {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 0.25rem !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(201,168,76,0.3) transparent;
    }
    .grid.grid-cols-2.lg\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.65rem !important;
    }
    .grid.grid-cols-1.lg\:grid-cols-2 {
        grid-template-columns: 1fr !important;
        gap: 0.65rem !important;
    }
}

/* ════════════════════════════════════════
   DASHBOARD — MÓVIL  < 768px
════════════════════════════════════════ */
@media (max-width: 767px) {
    .grid.grid-cols-2.lg\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.5rem !important;
    }
    .kpi-card {
        padding: 0.75rem !important;
        border-radius: 0.75rem !important;
        gap: 0.15rem !important;
    }
    .kpi-top   { margin-bottom: 0.15rem !important; }
    .kpi-label { font-size: 0.48rem !important; letter-spacing: 0.06em !important; }
    .kpi-icon  {
        width: 1.6rem !important;
        height: 1.6rem !important;
        font-size: 0.8rem !important;
        border-radius: 0.4rem !important;
        flex-shrink: 0 !important;
    }
    .kpi-value {
        font-size: 1.2rem !important;
        line-height: 1 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    .kpi-sub   { font-size: 0.58rem !important; margin-bottom: 0.25rem !important; }
    .kpi-action-btn { font-size: 0.6rem !important; }
    .grid.grid-cols-1.lg\:grid-cols-2 {
        grid-template-columns: 1fr !important;
        gap: 0.6rem !important;
    }
    .admin-card-header {
        padding: 0.6rem 0.875rem !important;
        flex-wrap: wrap;
        gap: 0.3rem;
    }
    .admin-card-title { font-size: 0.58rem !important; }
    .alert-row   { padding: 0.5rem 0.625rem !important; gap: 0.4rem !important; }
    .alert-dot   { width: 6px !important; height: 6px !important; }
    .alert-title { font-size: 0.7rem !important; }
    .alert-desc  { font-size: 0.6rem !important; }
    .alert-count { font-size: 1rem !important; }
    .divide-y > div { padding: 0.45rem 0.75rem !important; gap: 0.5rem !important; }
    .admin-card > div[style*="overflow-x"] {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .admin-table { min-width: 460px !important; }
    .admin-table th,
    .admin-table td {
        padding: 0.45rem 0.6rem !important;
        font-size: 0.7rem !important;
        white-space: nowrap;
    }
    .space-y-4 > * + * { margin-top: 0.6rem !important; }
    .space-y-2 > * + * { margin-top: 0.3rem !important; }
    .p-3 { padding: 0.5rem !important; }
    .py-6 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .py-10 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
    .px-3.py-2\.5 { padding: 0.4rem 0.6rem !important; }
    .gap-3 { gap: 0.5rem !important; }
    .pb-20 { padding-bottom: 5rem !important; }
}
</style>
@endsection