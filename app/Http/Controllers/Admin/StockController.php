<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Producto, StockMovimiento, AuditLog};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')
            ->where('activo', true)
            ->orderBy('stock_actual')
            ->paginate(25);

        return view('admin.stock.index', compact('productos'));
    }

    public function ajuste(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo'        => 'required|in:entrada,ajuste',
            'cantidad'    => 'required|integer|min:0|max:10000',
            'motivo'      => 'nullable|string|max:255',
        ], [
            'producto_id.required' => 'Debes seleccionar un producto válido.',
            'tipo.required'        => 'El tipo de movimiento es obligatorio.',
            'tipo.in'              => 'El tipo de movimiento no es válido.',
            'cantidad.required'    => 'La cantidad es obligatoria.',
            'cantidad.integer'     => 'La cantidad debe ser un número entero.',
            'cantidad.min'         => 'La cantidad no puede ser negativa.',
            'cantidad.max'         => 'La cantidad máxima permitida por movimiento es de 10,000 unidades.',
        ]);

        // Para entrada, la cantidad debe ser al menos 1
        if ($data['tipo'] === 'entrada' && $data['cantidad'] < 1) {
            return back()->withErrors(['cantidad' => 'Debes agregar al menos 1 unidad en una entrada.']);
        }

        $producto   = Producto::findOrFail($data['producto_id']);
        $stockAntes = $producto->stock_actual;

        $nuevo = $data['tipo'] === 'entrada'
            ? $stockAntes + $data['cantidad']   // Suma al existente
            : max(0, (int) $data['cantidad']);   // Reemplaza con valor exacto (mínimo 0)

        $producto->update([
            'stock_actual' => $nuevo,
            'stock_activo' => $nuevo > 0,
        ]);

        StockMovimiento::create([
            'producto_id'   => $producto->id,
            'user_id'       => Auth::id(),
            'tipo'          => $data['tipo'],
            'cantidad'      => $data['cantidad'],
            'stock_antes'   => $stockAntes,
            'stock_despues' => $nuevo,
            'motivo'        => $data['motivo'] ?? ($data['tipo'] === 'entrada' ? 'Entrada de stock' : 'Ajuste directo'),
        ]);

        AuditLog::registrar(
            "Ajuste de stock — {$producto->nombre}: {$stockAntes} → {$nuevo}",
            \App\Models\Producto::class,
            $producto->id
        );

        $accion = $data['tipo'] === 'entrada'
            ? "+{$data['cantidad']} unidades agregadas"
            : "establecido en {$nuevo} unidades";

        return back()->with('success', "'{$producto->nombre}': {$accion}. Stock actual: {$nuevo}.");
    }

    public function movimientos()
    {
        $movimientos = StockMovimiento::with(['producto', 'user'])->latest()->paginate(30);
        return view('admin.stock.movimientos', compact('movimientos'));
    }
}