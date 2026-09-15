<?php

namespace App\Http\Controllers\Kiosco;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

class ComprobanteController extends Controller
{
    public function show(string $codigo)
    {
        $pedido = Pedido::with([
            'sede',
            'items.producto',
            'items.combo',
            'pago',
            'comprobante',
        ])->where('codigo', $codigo)->firstOrFail();

        return view('kiosco.comprobante', compact('pedido'));
    }
}