<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Sede;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $sedes   = Sede::activos()->get();
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());

        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);

        // Base query para el período
        $baseQuery = fn($sedeId) => Pedido::deSede($sedeId)
            ->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->whereNotIn('estado', ['cancelado']);

        $resumen = $sedes->map(fn ($sede) => [
            'sede'     => $sede,
            'efectivo' => $baseQuery($sede->id)->where('metodo_pago', 'efectivo')->sum('total'),
            'qr'       => $baseQuery($sede->id)->where('metodo_pago', 'qr_deuna')->sum('total'),
            'cobrado'  => $baseQuery($sede->id)->whereIn('estado', ['pagado','entregado'])->sum('total'),
            'total'    => $baseQuery($sede->id)->sum('total'),
            'pedidos'  => $baseQuery($sede->id)->count(),
        ]);

        return view('admin.caja.index', compact('sedes', 'resumen', 'periodo', 'periodoLabel', 'desde', 'hasta', 'fecha'));
    }

    private function resolverPeriodo(string $periodo, string $fecha): array
    {
        return match($periodo) {
            'hoy'       => [today(),                               today(),              'Hoy — ' . today()->translatedFormat('d \de F')],
            'ayer'      => [today()->subDay(),                     today()->subDay(),    'Ayer — ' . today()->subDay()->translatedFormat('d \de F')],
            'semana'    => [today()->startOfWeek(),                today(),              'Esta semana'],
            'ult7'      => [today()->subDays(6),                   today(),              'Últimos 7 días'],
            'mes'       => [today()->startOfMonth(),               today(),              'Este mes — ' . today()->translatedFormat('F Y')],
            'ult30'     => [today()->subDays(29),                  today(),              'Últimos 30 días'],
            'ult6meses' => [today()->subMonths(6)->startOfMonth(), today()->endOfMonth(),'Últimos 6 meses'],
            'ult12'     => [today()->subMonths(12)->startOfMonth(),today()->endOfMonth(),'Últimos 12 meses'],
            'todo'      => [\Carbon\Carbon::create(2020,1,1),    today(),              'Todo el tiempo'],
            'fecha'     => (function() use ($fecha) {
                try {
                    $d = \Carbon\Carbon::parse($fecha);
                    if ($d->year < 2020 || $d->isFuture()) {
                        return [today(), today(), 'Hoy'];
                    }
                    return [$d, $d, 'Fecha: ' . $d->format('Y-m-d')];
                } catch (\Throwable) {
                    return [today(), today(), 'Hoy'];
                }
            })(),
            default     => [today(),                               today(),              'Hoy'],
        };
    }

    public function cierre(Request $request, Sede $sede)
    {
        // Respetar el período enviado desde la vista de caja o reportes
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());

        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);

        $pedidos = Pedido::deSede($sede->id)
            ->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->whereIn('estado', ['pagado', 'entregado'])
            ->with('items')
            ->orderBy('created_at')
            ->get();

        return view('admin.caja.cierre', compact('sede', 'pedidos', 'periodoLabel', 'desde', 'hasta'));
    }
}