<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AuditLog, Pedido, Producto, Sede};
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $sedes   = Sede::activos()->get();
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());

        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);

        $query = fn() => Pedido::whereBetween('created_at', [
            $desde->copy()->startOfDay(),
            $hasta->copy()->endOfDay(),
        ])->whereNotIn('estado', ['cancelado']);

        $ventasHoy  = $query()->sum('total');
        $pedidosHoy = $query()->count();
        $pendientes = Pedido::delDia()->pendientes()->count(); // siempre del día

        $ventasPorSede = $sedes->mapWithKeys(fn($sede) => [
            $sede->slug => [
                'sede'    => $sede,
                'ventas'  => $query()->deSede($sede->id)->sum('total'),
                'pedidos' => $query()->deSede($sede->id)->count(),
            ],
        ]);

        $pedidosPendientesCobro = Pedido::delDia()->where('estado','pendiente_pago')->get();
        $pedidosPendientesQr    = Pedido::delDia()->where('estado','pendiente_verificacion')->get();
        $productosAgotados      = Producto::where('stock_activo', false)->where('activo', true)->get();

        $ultimosPedidos = Pedido::with(['sede','items'])
            ->whereBetween('created_at', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->latest()->take(10)->get();

        $actividad = AuditLog::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'sedes','ventasHoy','pedidosHoy','pendientes',
            'ventasPorSede','pedidosPendientesCobro','pedidosPendientesQr',
            'productosAgotados','ultimosPedidos','actividad',
            'periodo','periodoLabel','desde','hasta','fecha'
        ));
    }

    private function resolverPeriodo(string $periodo, string $fecha): array
    {
        return match($periodo) {
            'hoy'       => [today(),                               today(),              'Hoy'],
            'ayer'      => [today()->subDay(),                     today()->subDay(),    'Ayer'],
            'semana'    => [today()->startOfWeek(),                today(),              'Esta semana'],
            'ult7'      => [today()->subDays(6),                   today(),              'Últimos 7 días'],
            'mes'       => [today()->startOfMonth(),               today(),              'Este mes'],
            'ult30'     => [today()->subDays(29),                  today(),              'Últimos 30 días'],
            'ult6meses' => [today()->subMonths(6)->startOfMonth(), today()->endOfMonth(),'Últimos 6 meses'],
            'ult12'     => [today()->subMonths(12)->startOfMonth(),today()->endOfMonth(),'Últimos 12 meses'],
            'todo'      => [Carbon::create(2020,1,1),              today(),              'Todo el tiempo'],
            'fecha'     => [Carbon::parse($fecha),                 Carbon::parse($fecha),'Fecha: '.$fecha],
            default     => [today(),                               today(),              'Hoy'],
        };
    }
}