<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Pedido;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query  = Pedido::with(['sede', 'user', 'items', 'pago'])->latest();
        $periodo = $request->input('periodo', 'hoy');
        $fecha   = $request->input('fecha', today()->toDateString());

        // Filtro de período
        [$desde, $hasta, $periodoLabel] = $this->resolverPeriodo($periodo, $fecha);
        $query->whereBetween('created_at', [
            $desde->copy()->startOfDay(),
            $hasta->copy()->endOfDay(),
        ]);

        // Filtros adicionales
        if ($request->filled('sede'))   $query->where('sede_id', $request->sede);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('metodo')) $query->where('metodo_pago', $request->metodo);

        $pedidos = $query->paginate(25)->withQueryString();
        $sedes   = Sede::activos()->get();

        return view('admin.pedidos.index', compact(
            'pedidos', 'sedes', 'periodo', 'periodoLabel', 'desde', 'hasta', 'fecha'
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
            'todo'      => [\Carbon\Carbon::create(2020,1,1),    today(),              'Todo el tiempo'],
            'fecha'     => (function() use ($fecha) {
                try {
                    $d = \Carbon\Carbon::parse($fecha);
                    return [$d, $d, 'Fecha: ' . $d->format('Y-m-d')];
                } catch (\Throwable) {
                    return [today(), today(), 'Hoy'];
                }
            })(),
            default     => [today(),                               today(),              'Hoy'],
        };
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['sede', 'user', 'items.producto', 'items.combo', 'pago', 'comprobante']);
        return view('admin.pedidos.show', compact('pedido'));
    }

    public function verificarQr(Pedido $pedido)
    {
        abort_if($pedido->estado !== 'pendiente_verificacion', 422, 'El pedido no está pendiente de verificación QR.');

        $pedido->pago?->marcarVerificado(Auth::id());

        AuditLog::registrar(
            "Pago QR verificado — {$pedido->codigo}",
            Pedido::class, $pedido->id,
            ['estado' => 'pendiente_verificacion'],
            ['estado' => 'pagado']
        );

        return back()->with('success', "Pago QR del pedido {$pedido->codigo} verificado correctamente.");
    }

    public function cobrar(Pedido $pedido)
    {
        abort_if($pedido->estado !== 'pendiente_pago', 422, 'El pedido no está pendiente de pago.');
        abort_if($pedido->metodo_pago !== 'efectivo', 422, 'Este pedido no es de pago en efectivo.');

        $pedido->update(['estado' => 'pagado', 'estado_pago' => 'pagado']);
        $pedido->pago?->update([
            'estado'         => 'pagado',
            'verificado_por' => Auth::id(),
            'verificado_at'  => now(),
        ]);

        AuditLog::registrar("Cobro en efectivo — {$pedido->codigo}", Pedido::class, $pedido->id);

        return back()->with('success', "Pedido {$pedido->codigo} cobrado correctamente.");
    }

    public function entregar(Pedido $pedido)
    {
        abort_if(!$pedido->puedeEntregarse(), 422, 'El pedido no puede marcarse como entregado todavía.');

        $pedido->update([
            'estado'         => 'entregado',
            'entregado_por'  => Auth::id(),
            'entregado_at'   => now(),
        ]);

        AuditLog::registrar("Pedido entregado — {$pedido->codigo}", Pedido::class, $pedido->id);

        return back()->with('success', "Pedido {$pedido->codigo} marcado como entregado.");
    }

    public function cancelar(Pedido $pedido)
    {
        abort_if(!$pedido->puedeCancelarse(), 422, 'Este pedido no puede cancelarse.');

        $estadoAnterior = $pedido->estado;
        $pedido->update(['estado' => 'cancelado']);

        AuditLog::registrar(
            "Pedido cancelado — {$pedido->codigo}",
            Pedido::class, $pedido->id,
            ['estado' => $estadoAnterior],
            ['estado' => 'cancelado']
        );

        return back()->with('success', "Pedido {$pedido->codigo} cancelado.");
    }
}