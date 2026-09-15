@extends('layouts.app')
@section('title', 'Cierre de Caja — ' . $sede->nombre)
@section('page-icon','🖨️')
@section('page-title','Cierre de Caja')
@section('page-subtitle', $sede->nombre . ' · ' . $periodoLabel)
@section('header-actions')
    <button onclick="window.print()" class="btn-gold">🖨️ Imprimir</button>
    <a href="{{ route('admin.caja.index') }}" class="btn-ghost">← Volver</a>
@endsection

@section('content')
<div class="max-w-2xl pt-2 space-y-4 no-print mx-auto">
    <div class="admin-card p-4 flex gap-3 items-center"
         style="background:rgba(201,168,76,0.05);border-color:rgba(201,168,76,0.15);">
        <span class="text-xl">💡</span>
        <span class="text-sm" style="color:rgba(255,255,255,0.50);">
            Usa el botón <strong class="text-white">🖨️ Imprimir</strong> del header para obtener la factura de cierre.
            Se imprimirá solo el ticket, sin la interfaz del sistema.
        </span>
    </div>
</div>

{{-- TICKET DE CIERRE --}}
<div class="ticket-wrap" id="ticket">

    {{-- Header institucional --}}
    <div class="ticket-header">
        <div class="ticket-logo">ISTPET BAR</div>
        <div class="ticket-inst">Instituto Superior Tecnológico Mayor Pedro Traversari</div>
        <div class="ticket-sede">{{ $sede->nombre }}</div>
        <div class="ticket-divider-dots">· · · · · · · · · · · · · · · · · · · · · · · · · ·</div>
        <div class="ticket-doc-title">CIERRE DE CAJA</div>
        <div class="ticket-periodo">{{ $periodoLabel }}</div>
        <div class="ticket-fecha" style="color:rgba(255,255,255,0.50);">Impreso: {{ now()->format('d/m/Y — H:i') }}</div>
        <div class="ticket-divider-dots">· · · · · · · · · · · · · · · · · · · · · · · · · ·</div>
    </div>

    {{-- Resumen financiero --}}
    @php
        $efectivo = $pedidos->where('metodo_pago','efectivo')->sum('total');
        $qr       = $pedidos->where('metodo_pago','qr_deuna')->sum('total');
        $total    = $pedidos->sum('total');
    @endphp

    <div class="ticket-section">
        <div class="ticket-row">
            <span >Total de pedidos</span>
            <span class="ticket-val">{{ $pedidos->count() }}</span>
        </div>
        <div class="ticket-divider-thin"></div>
        <div class="ticket-row">
            <span>💵 Efectivo cobrado</span>
            <span class="ticket-val">${{ number_format($efectivo, 2) }}</span>
        </div>
        <div class="ticket-row">
            <span>📱 QR DeUna cobrado</span>
            <span class="ticket-val">${{ number_format($qr, 2) }}</span>
        </div>
        @if($total > 0)
        <div class="ticket-bar-wrap">
            <div class="ticket-bar">
                <div class="ticket-bar-efectivo" style="width:{{ round($efectivo/$total*100) }}%"></div>
                <div class="ticket-bar-qr"       style="width:{{ round($qr/$total*100) }}%"></div>
            </div>
            <div class="ticket-bar-labels">
                <span>Efectivo {{ round($efectivo/$total*100) }}%</span>
                <span>QR {{ round($qr/$total*100) }}%</span>
            </div>
        </div>
        @endif
        <div class="ticket-divider-thick"></div>
        <div class="ticket-row ticket-total-row">
            <span>TOTAL</span>
            <span class="ticket-total-val">${{ number_format($total, 2) }}</span>
        </div>
    </div>

    {{-- Detalle de pedidos --}}
    @if($pedidos->isNotEmpty())
    <div class="ticket-section">
        <div class="ticket-section-title">DETALLE DE PEDIDOS</div>
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Método</th>
                    <th>Ítem</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedidos as $pedido)
                <tr>
                    <td class="ticket-code">{{ $pedido->codigo }}</td>
                    <td>{{ $pedido->created_at->format('d/m H:i') }}</td>
                    <td>{{ $pedido->metodo_pago === 'qr_deuna' ? 'QR' : 'Efect.' }}</td>
                    <td>{{ $pedido->items->count() }}</td>
                    <td class="text-right ticket-price">${{ number_format($pedido->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="ticket-empty" style="color:rgba(255,255,255,0.50);">Sin pedidos en este período.</div>
    @endif

    {{-- Firmas --}}
    <div class="ticket-divider-dots">· · · · · · · · · · · · · · · · · · · · · · · · · ·</div>
    <div class="ticket-firmas">
        <div class="ticket-firma">
            <div class="ticket-firma-line"></div>
            <div class="ticket-firma-label" style="color:rgba(255,255,255,0.50);">Cajero/a responsable</div>
        </div>
        <div class="ticket-firma">
            <div class="ticket-firma-line"></div>
            <div class="ticket-firma-label" style="color:rgba(255,255,255,0.50);">Administración</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="ticket-footer" style="color:rgba(255,255,255,0.45);">
        ISTPET BAR · Sistema de Pedidos v2.0<br>
        {{ now()->format('d/m/Y H:i:s') }}
    </div>

</div>

<style>
/* ── PANTALLA ── */
.ticket-wrap {
    max-width: 580px;
    margin: 1.5rem auto 0;
    background: #1a1f32;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1rem;
    overflow: hidden;
    font-family: 'Courier New', Courier, monospace;
}
.ticket-header {
    background: linear-gradient(135deg, #111b4a, #1b2a6b);
    padding: 1.75rem 2rem 1.25rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.ticket-logo      { font-family: var(--font-display); font-weight: 900; font-size: 1.8rem; color: #c9a84c; letter-spacing: 0.1em; }
.ticket-inst      { font-size: 0.72rem; color: rgba(255,255,255,0.55); margin-top: 3px; letter-spacing: 0.03em; }
.ticket-sede      { font-size: 0.78rem; color: rgba(255,255,255,0.4); margin-top: 2px; }
.ticket-doc-title { font-family: var(--font-display); font-weight: 900; font-size: 1.1rem; color: white; text-transform: uppercase; letter-spacing: 0.12em; margin-top: 0.75rem; }
.ticket-periodo   { font-size: 0.78rem; color: #c9a84c; font-weight: 700; margin-top: 2px; }
.ticket-fecha     { font-size: 0.68rem; color: rgba(255,255,255,0.3); margin-top: 2px; }
.ticket-divider-dots { font-size: 0.7rem; color: rgba(255,255,255,0.12); margin: 0.6rem 0 0; letter-spacing: 0.05em; }

.ticket-section       { padding: 1rem 2rem; }
.ticket-section-title { font-size: 0.6rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(255,255,255,0.3); margin-bottom: 0.75rem; font-family: var(--font-display); }
.ticket-row           { display: flex; justify-content: space-between; align-items: center; padding: 0.35rem 0; font-size: 0.83rem; color: rgba(255,255,255,0.65); }
.ticket-val           { font-weight: 700; color: white; }
.ticket-divider-thin  { height: 1px; background: rgba(255,255,255,0.06); margin: 0.3rem 0; }
.ticket-divider-thick { height: 2px; background: rgba(255,255,255,0.1); margin: 0.5rem 0; }

.ticket-bar-wrap   { margin: 0.5rem 0; }
.ticket-bar        { height: 6px; background: rgba(255,255,255,0.05); border-radius: 999px; overflow: hidden; display: flex; }
.ticket-bar-efectivo { height: 100%; background: #facc15; }
.ticket-bar-qr       { height: 100%; background: #60a5fa; }
.ticket-bar-labels   { display: flex; justify-content: space-between; font-size: 0.62rem; color: rgba(255,255,255,0.25); margin-top: 3px; }

.ticket-total-row  { padding: 0.5rem 0; }
.ticket-total-row span:first-child { font-family: var(--font-display); font-weight: 900; font-size: 0.9rem; color: white; text-transform: uppercase; letter-spacing: 0.08em; }
.ticket-total-val  { font-family: var(--font-display); font-weight: 900; font-size: 1.8rem; color: #c9a84c; }

.ticket-table      { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
.ticket-table th   { text-align: left; font-size: 0.58rem; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; color: rgba(255,255,255,0.25); padding: 0.3rem 0.4rem 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.07); font-family: var(--font-display); }
.ticket-table td   { padding: 0.4rem 0.4rem; color: rgba(255,255,255,0.6); border-bottom: 1px solid rgba(255,255,255,0.03); vertical-align: middle; }
.ticket-table tr:last-child td { border-bottom: none; }
.ticket-table tr:hover td { background: rgba(255,255,255,0.02); }
.ticket-code  { font-family: var(--font-display); font-weight: 800; color: white; font-size: 0.77rem; }
.ticket-price { font-family: var(--font-display); font-weight: 800; color: #c9a84c; }
.text-right   { text-align: right; }

.ticket-empty  { text-align: center; padding: 2rem; font-size: 0.8rem; color: rgba(255,255,255,0.18); }

.ticket-firmas { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; padding: 1.5rem 2rem 1rem; }
.ticket-firma-line  { height: 1px; background: rgba(255,255,255,0.15); margin-bottom: 0.4rem; }
.ticket-firma-label { font-size: 0.62rem; color: rgba(255,255,255,0.25); text-align: center; text-transform: uppercase; letter-spacing: 0.1em; font-family: var(--font-display); }

.ticket-footer { text-align: center; padding: 0.75rem 2rem 1.25rem; font-size: 0.6rem; color: rgba(255,255,255,0.12); line-height: 1.8; font-family: var(--font-display); letter-spacing: 0.06em; }

/* ══════════════════════════════
   IMPRESIÓN — ticket profesional
══════════════════════════════ */
@media print {
    @page { size: A4; margin: 15mm 20mm; }

    body, html { background: white !important; color: black !important; }

    /* Ocultar todo el sistema */
    aside, header, .no-print, .admin-card { display: none !important; }
    div[style*="margin-left"] { margin-left: 0 !important; }
    main { padding: 0 !important; }

    /* Ticket ocupa toda la página */
    .ticket-wrap {
        max-width: 100%;
        margin: 0;
        background: white !important;
        border: none !important;
        border-radius: 0 !important;
        font-family: 'Courier New', Courier, monospace;
    }

    /* Header */
    .ticket-header {
        background: #1a237e !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        padding: 20px 24px 14px;
    }
    .ticket-logo      { color: #f0c040 !important; font-size: 22pt; }
    .ticket-inst      { color: rgba(255,255,255,0.75) !important; font-size: 8pt; }
    .ticket-sede      { color: rgba(255,255,255,0.6) !important; font-size: 8.5pt; }
    .ticket-doc-title { color: white !important; font-size: 13pt; }
    .ticket-periodo   { color: #f0c040 !important; font-size: 9pt; }
    .ticket-fecha     { color: rgba(255,255,255,0.5) !important; font-size: 7.5pt; }
    .ticket-divider-dots { color: rgba(255,255,255,0.2) !important; }

    /* Sección */
    .ticket-section { padding: 10px 24px; }
    .ticket-section-title { color: #666 !important; font-size: 7pt; }
    .ticket-row   { font-size: 9.5pt; color: #333 !important; }
    .ticket-val   { color: #000 !important; }
    .ticket-divider-thin  { background: #ddd !important; }
    .ticket-divider-thick { background: #999 !important; }

    /* Barra */
    .ticket-bar        { background: #eee !important; }
    .ticket-bar-efectivo { background: #f59e0b !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ticket-bar-qr       { background: #3b82f6 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ticket-bar-labels { color: #888 !important; font-size: 7pt; }

    /* Total */
    .ticket-total-row span:first-child { color: #000 !important; font-size: 11pt; }
    .ticket-total-val { color: #1a237e !important; font-size: 20pt; }

    /* Tabla */
    .ticket-table th  { color: #666 !important; font-size: 7pt; border-bottom: 1px solid #ccc !important; }
    .ticket-table td  { color: #333 !important; font-size: 8.5pt; border-bottom: 1px solid #eee !important; }
    .ticket-code      { color: #000 !important; }
    .ticket-price     { color: #1a237e !important; }

    /* Firmas */
    .ticket-firma-line  { background: #aaa !important; }
    .ticket-firma-label { color: #666 !important; font-size: 7pt; }

    /* Footer */
    .ticket-footer { color: #aaa !important; font-size: 7pt; }
    .ticket-divider-dots { color: #bbb !important; }
}
</style>
@endsection