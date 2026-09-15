<?php

namespace App\Services;

use App\Models\Combo;
use App\Models\Comprobante;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\QrCuenta;
use Illuminate\Support\Facades\DB;

class PedidoService
{
    public function crearDesdeSesion(array $carrito, string $metodoPago, int $sedeId, ?int $userId = null): Pedido
    {
        return DB::transaction(function () use ($carrito, $metodoPago, $sedeId, $userId) {
            $this->validarDisponibilidad($carrito);

            $subtotal = collect($carrito)->sum(fn ($i) => $i['precio'] * $i['cantidad']);
            $total = $subtotal;

            $estado = match ($metodoPago) {
                'qr_deuna' => 'pendiente_verificacion',
                'efectivo' => 'pendiente_pago',
                default => 'pendiente_pago',
            };

            $pedido = Pedido::create([
                'codigo' => Pedido::generarCodigo(),
                'sede_id' => $sedeId,
                'user_id' => $userId,
                'metodo_pago' => $metodoPago,
                'estado' => $estado,
                'estado_pago' => 'pendiente',
                'subtotal' => $subtotal,
                'total' => $total,
                'area_origen' => 'kiosco',
            ]);

            foreach ($carrito as $item) {
                $this->crearItem($pedido, $item);
            }

            $qrCuenta = $metodoPago === 'qr_deuna' ? QrCuenta::activa() : null;

            Pago::create([
                'pedido_id' => $pedido->id,
                'qr_cuenta_id' => $qrCuenta?->id,
                'metodo' => $metodoPago,
                'monto' => $total,
                'estado' => 'pendiente',
            ]);

            Comprobante::create([
                'pedido_id' => $pedido->id,
                'numero_comprobante' => Comprobante::generarNumero(),
                'contenido_json' => $this->buildSnapshot($pedido, $carrito),
            ]);

            return $pedido->fresh(['items', 'pago', 'comprobante', 'sede']);
        });
    }

    private function validarDisponibilidad(array $carrito): void
    {
        foreach ($carrito as $item) {
            if ($item['tipo'] === 'combo') {
                $combo = Combo::with('items.producto')->findOrFail($item['id']);

                if (! $combo->disponible) {
                    throw new \Exception("El combo '{$combo->nombre}' ya no está disponible.");
                }
            } else {
                $producto = Producto::findOrFail($item['id']);

                if (! $producto->disponible) {
                    throw new \Exception("El producto '{$producto->nombre}' ya no está disponible.");
                }

                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock_actual}.");
                }
            }
        }
    }

    private function crearItem(Pedido $pedido, array $item): void
    {
        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $item['tipo'] === 'producto' ? $item['id'] : null,
            'combo_id' => $item['tipo'] === 'combo' ? $item['id'] : null,
            'nombre_snapshot' => $item['nombre'],
            'precio_snapshot' => $item['precio'],
            'cantidad' => $item['cantidad'],
            'subtotal' => round($item['precio'] * $item['cantidad'], 2),
        ]);

        if ($item['tipo'] === 'producto') {
            $producto = Producto::find($item['id']);
            $producto?->decrementarStock($item['cantidad'], $pedido->id, null);
        }

        if ($item['tipo'] === 'combo') {
            $combo = Combo::with('items.producto')->find($item['id']);

            if ($combo) {
                foreach ($combo->items as $comboItem) {
                    $comboItem->producto?->decrementarStock(
                        $comboItem->cantidad * $item['cantidad'],
                        $pedido->id
                    );
                }
            }
        }
    }

    private function buildSnapshot(Pedido $pedido, array $carrito): array
    {
        return [
            'pedido_id' => $pedido->id,
            'codigo' => $pedido->codigo,
            'sede' => $pedido->sede?->nombre,
            'fecha' => $pedido->created_at?->toDateTimeString(),
            'metodo' => $pedido->metodo_pago,
            'estado' => $pedido->estado,
            'items' => collect($carrito)->map(fn ($i) => [
                'nombre' => $i['nombre'],
                'precio' => $i['precio'],
                'cantidad' => $i['cantidad'],
                'subtotal' => round($i['precio'] * $i['cantidad'], 2),
            ])->toArray(),
            'subtotal' => $pedido->subtotal,
            'total' => $pedido->total,
        ];
    }
}