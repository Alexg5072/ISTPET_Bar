<?php

namespace App\Http\Controllers\Kiosco;

use App\Http\Controllers\Controller;
use App\Models\{Producto, Combo};
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    private const SESSION_KEY = 'kiosco_carrito';

    public function agregar(Request $request)
    {
        $request->validate([
            'tipo'     => 'required|in:producto,combo',
            'item_id'  => 'required|integer',
            'cantidad' => 'nullable|integer|min:1|max:20',
        ]);

        $cantidad = $request->input('cantidad', 1);
        $tipo     = $request->input('tipo');
        $itemId   = $request->input('item_id');
        $carrito  = session(self::SESSION_KEY, []);

        if ($tipo === 'combo') {
            $item = Combo::with('items.producto')->findOrFail($itemId);
            if (!$item->disponible) {
                return response()->json(['error' => 'Este combo no está disponible.'], 422);
            }
            $key  = "combo_{$itemId}";
            $data = [
                'tipo'      => 'combo',
                'id'        => $itemId,
                'nombre'    => $item->nombre,
                'precio'    => (float) $item->precio,
                'imagen_url'=> $item->imagen_url,
            ];
        } else {
            $item = Producto::findOrFail($itemId);
            if (!$item->disponible) {
                return response()->json(['error' => 'Este producto no está disponible.'], 422);
            }

            $key             = "producto_{$itemId}";
            $cantidadActual  = $carrito[$key]['cantidad'] ?? 0;
            $stockDisponible = $item->stock_actual;

            // Validar que no supere el stock disponible
            if ($cantidadActual + $cantidad > $stockDisponible) {
                $restante = $stockDisponible - $cantidadActual;
                if ($restante <= 0) {
                    return response()->json([
                        'error' => "Ya tienes el máximo disponible de \"{$item->nombre}\" en tu carrito ({$stockDisponible} unidad" . ($stockDisponible === 1 ? ')' : 'es)'),
                    ], 422);
                }
                // Agregar solo lo que queda
                $cantidad = $restante;
            }

            $data = [
                'tipo'        => 'producto',
                'id'          => $itemId,
                'nombre'      => $item->nombre,
                'precio'      => (float) $item->precio,
                'imagen_url'  => $item->imagen_url,
                'stock_max'   => $stockDisponible,
            ];
        }

        if (isset($carrito[$key])) {
            $carrito[$key]['cantidad'] += $cantidad;
        } else {
            $carrito[$key] = array_merge($data, ['cantidad' => $cantidad]);
        }

        session([self::SESSION_KEY => $carrito]);

        return response()->json([
            'success' => true,
            'carrito' => $this->resumen($carrito),
            'message' => "{$data['nombre']} agregado al carrito.",
        ]);
    }

    public function quitar(Request $request)
    {
        $request->validate([
            'tipo'    => 'required|in:producto,combo',
            'item_id' => 'required|integer',
            'delta'   => 'nullable|integer',
        ]);

        $key     = $request->tipo . '_' . $request->item_id;
        $delta   = $request->input('delta', -1);
        $carrito = session(self::SESSION_KEY, []);

        if (isset($carrito[$key])) {
            if ($delta === 0 || $carrito[$key]['cantidad'] <= 1) {
                unset($carrito[$key]);
            } else {
                $carrito[$key]['cantidad'] += $delta;
            }
        }

        session([self::SESSION_KEY => $carrito]);

        return response()->json([
            'success' => true,
            'carrito' => $this->resumen($carrito),
        ]);
    }

    public function limpiar()
    {
        session()->forget(self::SESSION_KEY);
        return response()->json(['success' => true, 'carrito' => $this->resumen([])]);
    }

    public function datos()
    {
        $carrito = session(self::SESSION_KEY, []);
        return response()->json(['carrito' => $this->resumen($carrito)]);
    }

    private function resumen(array $carrito): array
    {
        $items = array_values($carrito);
        $total = collect($items)->sum(fn($i) => $i['precio'] * $i['cantidad']);

        return [
            'items' => $items,
            'count' => collect($items)->sum('cantidad'),
            'total' => round($total, 2),
            'vacio' => empty($items),
        ];
    }
}