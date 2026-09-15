<?php namespace App\Http\Controllers\Kiosco;
use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class MisPedidosController extends Controller
{
    /**
     * Devuelve el historial de pedidos del usuario autenticado en formato JSON,
     * para alimentar el modal "Mis pedidos" en el kiosco.
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        $pedidos = Pedido::with(['sede', 'items'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(30)
            ->get()
            ->map(function (Pedido $p) {
                return [
                    'codigo'       => $p->codigo,
                    'fecha'        => $p->created_at?->format('d/m/Y'),
                    'fecha_iso'    => $p->created_at?->format('Y-m-d'),
                    'hora'         => $p->created_at?->format('h:i A'),
                    'sede'         => $p->sede?->nombre,
                    'metodo_pago'  => $p->metodo_pago,
                    'estado'       => $p->estado,
                    'estado_badge' => $p->estado_badge,
                    'total'        => (float) $p->total,
                    'items'        => $p->items->map(fn ($i) => [
                        'nombre'   => $i->nombre_snapshot,
                        'cantidad' => $i->cantidad,
                        'subtotal' => (float) $i->subtotal,
                    ]),
                ];
            });

        return response()->json(['pedidos' => $pedidos]);
    }
}