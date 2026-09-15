<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());
        $sedes   = Sede::activos()->get();

        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);

        $datos = $sedes->map(function ($sede) use ($desde, $hasta) {
            $pedidos = Pedido::deSede($sede->id)
                ->whereBetween('created_at', [$desde->startOfDay()->copy(), $hasta->endOfDay()->copy()])
                ->whereNotIn('estado', ['cancelado'])
                ->get();

            return [
                'sede'        => $sede,
                'total'       => $pedidos->sum('total'),
                'cobrado'     => $pedidos->whereIn('estado', ['pagado', 'entregado'])->sum('total'),
                'count'       => $pedidos->count(),
                'ticket_prom' => $pedidos->count() > 0 ? $pedidos->avg('total') : 0,
            ];
        });

        $masVendidos = DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->whereBetween('pedidos.created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->whereNotNull('pedido_items.producto_id')
            ->whereNotIn('pedidos.estado', ['cancelado'])
            ->groupBy('pedido_items.producto_id', 'pedido_items.nombre_snapshot')
            ->selectRaw('pedido_items.producto_id, pedido_items.nombre_snapshot, SUM(pedido_items.cantidad) as total_unidades, SUM(pedido_items.subtotal) as total_ingresos')
            ->orderByDesc('total_unidades')
            ->take(10)
            ->get();

        // Ventas por día para el gráfico (solo cuando hay más de 1 día)
        $ventasPorDia = collect();
        if ($desde->diffInDays($hasta) > 0) {
            $ventasPorDia = DB::table('pedidos')
                ->selectRaw('DATE(created_at) as dia, SUM(total) as total_dia, COUNT(*) as pedidos_dia')
                ->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
                ->whereNotIn('estado', ['cancelado'])
                ->groupBy('dia')
                ->orderBy('dia')
                ->get();
        }

        return view('admin.reportes.index', compact(
            'datos', 'fecha', 'masVendidos', 'sedes',
            'periodo', 'periodoLabel', 'desde', 'hasta', 'ventasPorDia'
        ));
    }

    private function resolverPeriodo(string $periodo, string $fecha): array
    {
        return match($periodo) {
            'hoy'       => [today(),                              today(),                              'Hoy — ' . today()->translatedFormat('d \d\e F, Y')],
            'ayer'      => [today()->subDay(),                    today()->subDay(),                    'Ayer — ' . today()->subDay()->translatedFormat('d \d\e F, Y')],
            'semana'    => [today()->startOfWeek(),               today(),                              'Esta semana'],
            'ult7'      => [today()->subDays(6),                  today(),                              'Últimos 7 días'],
            'mes'       => [today()->startOfMonth(),              today(),                              'Este mes — ' . today()->translatedFormat('F Y')],
            'ult30'     => [today()->subDays(29),                 today(),                              'Últimos 30 días'],
            'ult6meses' => [today()->subMonths(6)->startOfMonth(),today()->endOfMonth(),                'Últimos 6 meses'],
            'ult12'     => [today()->subMonths(12)->startOfMonth(),today()->endOfMonth(),               'Últimos 12 meses'],
            'todo'      => [Carbon::create(2020,1,1),             today(),                              'Todo el tiempo'],
            'fecha'     => [Carbon::parse($fecha),               Carbon::parse($fecha),                'Fecha: ' . Carbon::parse($fecha)->translatedFormat('d \d\e F, Y')],
            default     => [today(),                              today(),                              'Hoy'],
        };
    }

    public function exportar(Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());
        $tipo    = $request->input('tipo', 'excel'); // excel o pdf
        $sedes   = Sede::activos()->get();

        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);

        $datos = $sedes->map(function ($sede) use ($desde, $hasta) {
            $pedidos = Pedido::deSede($sede->id)
                ->whereBetween('created_at', [$desde->startOfDay()->copy(), $hasta->endOfDay()->copy()])
                ->whereNotIn('estado', ['cancelado'])
                ->get();
            return [
                'sede'        => $sede,
                'total'       => $pedidos->sum('total'),
                'cobrado'     => $pedidos->whereIn('estado', ['pagado','entregado'])->sum('total'),
                'count'       => $pedidos->count(),
                'ticket_prom' => $pedidos->count() > 0 ? $pedidos->avg('total') : 0,
            ];
        });

        $masVendidos = \DB::table('pedido_items')
            ->join('pedidos', 'pedidos.id', '=', 'pedido_items.pedido_id')
            ->whereBetween('pedidos.created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->whereNotNull('pedido_items.producto_id')
            ->whereNotIn('pedidos.estado', ['cancelado'])
            ->groupBy('pedido_items.producto_id', 'pedido_items.nombre_snapshot')
            ->selectRaw('pedido_items.nombre_snapshot, SUM(pedido_items.cantidad) as total_unidades, SUM(pedido_items.subtotal) as total_ingresos')
            ->orderByDesc('total_unidades')
            ->take(20)
            ->get();

        if ($tipo === 'pdf') {
            return $this->exportarPdf($datos, $masVendidos, $periodoLabel, $desde, $hasta);
        }
        return $this->exportarExcel($datos, $masVendidos, $periodoLabel, $desde, $hasta);
    }

    private function exportarPdf($datos, $masVendidos, $periodoLabel, $desde, $hasta)
    {
        $html     = view('admin.reportes.export-pdf', compact('datos','masVendidos','periodoLabel','desde','hasta'))->render();
        $filename = 'reporte-istpet-' . now()->format('Y-m-d') . '.html';
        // Se descarga como HTML con instrucción de impresión automática para guardar como PDF
        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportarExcel($datos, $masVendidos, $periodoLabel, $desde, $hasta)
    {
        $filename = 'reporte-istpet-' . now()->format('Y-m-d') . '.csv';
        $sep      = ',';
        $nl       = "\r\n";
        $output   = "\xEF\xBB\xBF"; // BOM UTF-8 para acentos en Excel

        // Cabecera
        $output .= "REPORTE ISTPET BAR{$nl}";
        $output .= '"Instituto Superior Tecnologico Mayor Pedro Traversari"' . "{$nl}{$nl}";
        $output .= "Periodo{$sep}\"" . $periodoLabel . "\"{$nl}";
        $output .= "Desde{$sep}" . $desde->format('d/m/Y') . "{$nl}";
        $output .= "Hasta{$sep}" . $hasta->format('d/m/Y') . "{$nl}";
        $output .= "Generado{$sep}" . now()->format('d/m/Y H:i') . "{$nl}{$nl}";

        // Ventas por sede
        $output .= "VENTAS POR SEDE{$nl}";
        $output .= implode($sep, ['Sede','Total Facturado ($)','Cobrado ($)','Pedidos','Ticket Prom ($)','% Cobrado']) . "{$nl}";
        foreach ($datos as $d) {
            $pct = $d['total'] > 0 ? round(($d['cobrado'] / $d['total']) * 100) . '%' : '0%';
            $output .= implode($sep, [
                '"' . $d['sede']->nombre . '"',
                number_format($d['total'], 2),
                number_format($d['cobrado'], 2),
                $d['count'],
                number_format($d['ticket_prom'], 2),
                $pct,
            ]) . "{$nl}";
        }
        $output .= implode($sep, [
            'TOTAL GENERAL',
            number_format($datos->sum('total'), 2),
            number_format($datos->sum('cobrado'), 2),
            $datos->sum('count'),
            '', ''
        ]) . "{$nl}{$nl}";

        // Productos mas vendidos
        $output .= "PRODUCTOS MAS VENDIDOS{$nl}";
        $output .= implode($sep, ['#','Producto','Unidades Vendidas','Total Ingresos ($)','Participacion %']) . "{$nl}";
        $max = $masVendidos->max('total_unidades') ?: 1;
        foreach ($masVendidos as $i => $p) {
            $output .= implode($sep, [
                $i + 1,
                '"' . $p->nombre_snapshot . '"',
                $p->total_unidades,
                number_format($p->total_ingresos, 2),
                round($p->total_unidades / $max * 100) . '%',
            ]) . "{$nl}";
        }

        return response($output, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}