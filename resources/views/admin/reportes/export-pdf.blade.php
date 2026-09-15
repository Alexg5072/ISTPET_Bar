<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte ISTPET Bar — {{ $periodoLabel }}</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 10pt;
    color: #1a1a2e;
    background: #f5f6fa;
    padding: 0;
}

/* ── Botón imprimir (solo en pantalla) ── */
.print-bar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 999;
    background: #1a237e; color: white;
    padding: 10px 24px;
    display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.3);
}
.print-bar-title { font-size: 0.85rem; font-weight: 700; opacity: .8; }
.print-btn {
    background: #f0c040; color: #1a237e; border: none; cursor: pointer;
    padding: 8px 22px; border-radius: 6px;
    font-weight: 900; font-size: 0.85rem; letter-spacing: 0.05em;
}
.print-btn:hover { background: #fbbf24; }

/* ── Página ── */
.page {
    max-width: 900px; margin: 60px auto 40px;
    background: white; border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}

/* ── Header ── */
.rpt-header {
    background: linear-gradient(135deg, #1a237e, #283593);
    color: white; padding: 28px 32px 22px;
}
.rpt-header-top { display: flex; justify-content: space-between; align-items: flex-start; }
.rpt-logo { font-size: 28pt; font-weight: 900; letter-spacing: 0.12em; color: #f0c040; line-height: 1; }
.rpt-inst { font-size: 8.5pt; color: rgba(255,255,255,0.6); margin-top: 4px; }
.rpt-right { text-align: right; font-size: 7.5pt; color: rgba(255,255,255,0.45); line-height: 1.8; }
.rpt-divider { height: 1px; background: rgba(255,255,255,0.15); margin: 16px 0 14px; }
.rpt-doc-title { font-size: 15pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: white; }
.rpt-periodo   { font-size: 11pt; color: #f0c040; font-weight: 700; margin-top: 3px; }

/* ── Contenido ── */
.content { padding: 28px 32px 32px; }

/* ── Sección título ── */
.sec-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 7.5pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.18em;
    color: #1a237e; margin-bottom: 14px; margin-top: 24px;
}
.sec-title:first-child { margin-top: 0; }
.sec-title::after { content:''; flex:1; height:1px; background:#e0e7ff; }

/* ── Cards sede ── */
.sedes-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.sede-card { border: 1px solid #e0e7ff; border-radius: 10px; overflow: hidden; }
.sede-card-hdr {
    background: linear-gradient(135deg, #1a237e, #3949ab);
    color: white; padding: 14px 18px;
    display: flex; justify-content: space-between; align-items: center;
}
.sede-card-nombre { font-size: 8.5pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.04em; }
.sede-card-total  { font-size: 20pt; font-weight: 900; color: #f0c040; line-height: 1; }
.sede-card-body   { padding: 12px 18px; background: #fafbff; }
.sede-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 8.5pt; border-bottom: 1px solid #f0f0f8; }
.sede-row:last-child { border: none; }
.sede-row-label { color: #666; }
.sede-row-val   { font-weight: 700; color: #1a1a2e; }
.verde { color: #047857; }
.bar-outer { margin-top: 8px; height: 7px; background: #e0e7ff; border-radius: 999px; overflow: hidden; }
.bar-inner { height: 100%; background: linear-gradient(90deg, #1a237e, #3949ab); border-radius: 999px; }
.bar-label { font-size: 6.5pt; color: #888; margin-top: 3px; }

/* ── Total global ── */
.total-global {
    background: linear-gradient(135deg, #f0f4ff, #e8eeff);
    border: 1px solid #c7d2fe; border-radius: 8px;
    padding: 12px 18px;
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 4px;
}
.total-global-label { font-size: 10pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; color: #1a237e; }
.total-global-sub   { font-size: 8pt; color: #666; margin-top: 2px; }
.total-global-val   { font-size: 22pt; font-weight: 900; color: #1a237e; line-height: 1; }

/* ── Gráfico de barras SVG ── */
.chart-wrap { margin: 12px 0 4px; }
.chart-title { font-size: 7pt; color: #888; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 6px; font-weight: 700; }

/* ── Tabla ── */
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
thead tr { background: linear-gradient(135deg, #1a237e, #283593); }
th { color: white; text-align: left; padding: 8px 10px; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 900; }
td { padding: 7px 10px; font-size: 8.5pt; border-bottom: 1px solid #f0f0f8; vertical-align: middle; }
tr:hover td { background: #f5f7ff; }
.medal { font-size: 11pt; }
.prod-name { font-weight: 700; }
.td-right  { text-align: right; }
.td-center { text-align: center; }
.gold-val  { color: #92400e; font-weight: 900; }

/* Barra participación */
.part-bar { height: 6px; background: #e0e7ff; border-radius: 999px; overflow: hidden; margin-bottom: 2px; min-width: 80px; }
.part-fill { height: 100%; border-radius: 999px; }
.part-pct  { font-size: 6.5pt; color: #888; }

/* ── Firmas ── */
.firmas { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; margin-top: 36px; padding-top: 16px; }
.firma-line  { height: 1px; background: #bbb; margin-bottom: 5px; }
.firma-label { font-size: 7pt; color: #888; text-align: center; text-transform: uppercase; letter-spacing: 0.1em; }

/* ── Footer ── */
.rpt-footer {
    margin-top: 20px; padding-top: 12px;
    border-top: 1px solid #e0e7ff;
    display: flex; justify-content: space-between;
    font-size: 7pt; color: #aaa;
}

/* ── PRINT ── */
@media print {
    @page { size: A4; margin: 12mm 14mm; }
    body { background: white; }
    .print-bar { display: none !important; }
    .page { margin: 0; box-shadow: none; border-radius: 0; max-width: 100%; }
    .content { padding: 20px 24px 24px; }
    .rpt-header { padding: 20px 24px 16px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .sede-card-hdr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .total-global { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .bar-inner, .part-fill { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>

{{-- Barra solo en pantalla --}}
<div class="print-bar">
    <span class="print-bar-title">📄 Reporte ISTPET Bar — {{ $periodoLabel }}</span>
    <button class="print-btn" onclick="window.print()">🖨️ Descargar / Guardar PDF</button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="rpt-header">
        <div class="rpt-header-top">
            <div>
                <div class="rpt-logo">ISTPET BAR</div>
                <div class="rpt-inst">Instituto Superior Tecnológico Mayor Pedro Traversari</div>
            </div>
            <div class="rpt-right">
                <div>Generado: {{ now()->format('d/m/Y H:i') }}</div>
                <div>{{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</div>
            </div>
        </div>
        <div class="rpt-divider"></div>
        <div class="rpt-doc-title">Reporte de Ventas</div>
        <div class="rpt-periodo">{{ $periodoLabel }}</div>
    </div>

    <div class="content">

        {{-- Ventas por sede --}}
        <div class="sec-title">📊 Ventas por Sede</div>
        <div class="sedes-grid">
            @foreach($datos as $d)
            <div class="sede-card">
                <div class="sede-card-hdr">
                    <div class="sede-card-nombre">
                        {{ $d['sede']->slug === 'instituto' ? '🏛️' : '🚗' }} {{ $d['sede']->nombre }}
                    </div>
                    <div class="sede-card-total">${{ number_format($d['total'], 2) }}</div>
                </div>
                <div class="sede-card-body">
                    <div class="sede-row">
                        <span class="sede-row-label">Cobrado</span>
                        <span class="sede-row-val verde">${{ number_format($d['cobrado'], 2) }}</span>
                    </div>
                    <div class="sede-row">
                        <span class="sede-row-label">Pedidos</span>
                        <span class="sede-row-val">{{ $d['count'] }}</span>
                    </div>
                    <div class="sede-row">
                        <span class="sede-row-label">Ticket promedio</span>
                        <span class="sede-row-val">${{ number_format($d['ticket_prom'], 2) }}</span>
                    </div>
                    @if($d['total'] > 0)
                    @php $pct = round(($d['cobrado']/$d['total'])*100); @endphp
                    <div class="bar-outer"><div class="bar-inner" style="width:{{ $pct }}%;"></div></div>
                    <div class="bar-label">{{ $pct }}% cobrado</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Total global --}}
        <div class="total-global">
            <div>
                <div class="total-global-label">Total General</div>
                <div class="total-global-sub">Suma de todas las sedes · {{ $periodoLabel }}</div>
            </div>
            <div class="total-global-val">${{ number_format($datos->sum('total'), 2) }}</div>
        </div>

        {{-- Gráfico SVG de ventas por sede --}}
        @if($datos->sum('total') > 0)
        @php
            $totalG = $datos->sum('total');
            $colors = ['#1a237e','#3949ab','#5c6bc0','#7986cb'];
            $chartW = 760; $chartH = 80; $barH = 36; $barY = 22;
            $x = 0;
        @endphp
        <div class="chart-wrap">
            <div class="chart-title">Distribución de ventas por sede</div>
            <svg width="100%" viewBox="0 0 {{ $chartW }} {{ $chartH }}" xmlns="http://www.w3.org/2000/svg">
                <rect width="{{ $chartW }}" height="{{ $barH }}" y="{{ $barY }}" rx="6" fill="#e0e7ff"/>
                @foreach($datos as $i => $d)
                @php
                    $w = $totalG > 0 ? round(($d['total'] / $totalG) * $chartW) : 0;
                    $color = $colors[$i % count($colors)];
                @endphp
                @if($w > 0)
                <rect x="{{ $x }}" y="{{ $barY }}" width="{{ $w }}" height="{{ $barH }}"
                      rx="{{ $i === 0 ? 6 : 0 }}" fill="{{ $color }}"/>
                @if($w > 40)
                <text x="{{ $x + $w/2 }}" y="{{ $barY + $barH/2 + 4 }}"
                      text-anchor="middle" fill="white" font-size="9" font-weight="bold"
                      font-family="Arial">{{ round(($d['total']/$totalG)*100) }}%</text>
                @endif
                @php $x += $w; @endphp
                @endif
                @endforeach
                {{-- Leyenda --}}
                @php $lx = 0; @endphp
                @foreach($datos as $i => $d)
                <rect x="{{ $lx }}" y="2" width="10" height="10" rx="2" fill="{{ $colors[$i % count($colors)] }}"/>
                <text x="{{ $lx + 13 }}" y="11" font-size="8" fill="#555" font-family="Arial">
                    {{ $d['sede']->slug === 'instituto' ? 'Instituto' : 'Conducción' }} ${{ number_format($d['total'],2) }}
                </text>
                @php $lx += 200; @endphp
                @endforeach
            </svg>
        </div>
        @endif

        {{-- Productos más vendidos --}}
        @if($masVendidos->isNotEmpty())
        <div class="sec-title">🏆 Top {{ $masVendidos->count() }} Productos Más Vendidos</div>

        {{-- Gráfico de barras horizontal SVG --}}
        @php
            $maxUds  = $masVendidos->max('total_unidades') ?: 1;
            $top5    = $masVendidos->take(5);
            $svgH    = $top5->count() * 26 + 8;
            $barColors = ['#b45309','#1a237e','#3949ab','#5c6bc0','#7986cb'];
            $maxBarW = 400;
        @endphp
        <div class="chart-wrap">
            <div class="chart-title">Unidades vendidas — Top 5</div>
            <svg width="100%" viewBox="0 0 680 {{ $svgH }}" xmlns="http://www.w3.org/2000/svg">
                @foreach($top5 as $i => $p)
                @php
                    $bw  = round(($p->total_unidades / $maxUds) * $maxBarW);
                    $by  = $i * 26 + 4;
                    $col = $barColors[$i % count($barColors)];
                @endphp
                {{-- Label --}}
                <text x="0" y="{{ $by + 14 }}" font-size="8.5" fill="#333" font-family="Arial" font-weight="{{ $i === 0 ? 'bold' : 'normal' }}">
                    {{ Str::limit($p->nombre_snapshot, 28) }}
                </text>
                {{-- Barra fondo --}}
                <rect x="240" y="{{ $by + 2 }}" width="{{ $maxBarW }}" height="16" rx="4" fill="#e0e7ff"/>
                {{-- Barra valor --}}
                <rect x="240" y="{{ $by + 2 }}" width="{{ $bw }}" height="16" rx="4" fill="{{ $col }}"/>
                {{-- Valor --}}
                <text x="{{ 240 + $bw + 6 }}" y="{{ $by + 14 }}" font-size="8" fill="#555" font-family="Arial">
                    {{ $p->total_unidades }} uds · ${{ number_format($p->total_ingresos, 2) }}
                </text>
                @endforeach
            </svg>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th>Producto</th>
                    <th width="12%" class="td-right">Unidades</th>
                    <th width="14%" class="td-right">Ingresos</th>
                    <th width="25%">Participación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masVendidos as $i => $p)
                <tr>
                    <td class="medal td-center">{{ $i < 3 ? ['🥇','🥈','🥉'][$i] : ($i+1) }}</td>
                    <td class="prod-name">{{ $p->nombre_snapshot }}</td>
                    <td class="td-right td-center">{{ $p->total_unidades }}</td>
                    <td class="td-right gold-val">${{ number_format($p->total_ingresos, 2) }}</td>
                    <td>
                        @php $pct = round($p->total_unidades / $maxUds * 100); @endphp
                        <div class="part-bar">
                            <div class="part-fill" style="width:{{ $pct }}%;background:{{ $i === 0 ? '#b45309' : '#3949ab' }};"></div>
                        </div>
                        <div class="part-pct">{{ $pct }}% del máximo</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Firmas --}}
        <div class="firmas">
            <div>
                <div class="firma-line"></div>
                <div class="firma-label">Cajero/a responsable</div>
            </div>
            <div>
                <div class="firma-line"></div>
                <div class="firma-label">Administración ISTPET</div>
            </div>
        </div>

        <div class="rpt-footer">
            <span>ISTPET Bar · Sistema de Pedidos v2.0</span>
            <span>{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>

    </div>{{-- /content --}}
</div>{{-- /page --}}

<script>
// Auto-abrir diálogo de impresión si viene de descarga directa
if (window.location.href.includes('exportar')) {
    setTimeout(() => window.print(), 800);
}
</script>
</body>
</html>