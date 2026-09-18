@extends('layouts.app')
@section('title', 'Pedido ' . $pedido->codigo)
@section('page-icon')
<x-admin.icon name="pedidos" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title', $pedido->codigo)
@section('page-subtitle', 'Detalle completo del pedido')

@section('header-actions')
    <a href="{{ route('admin.pedidos.index') }}" class="btn-ghost">← Volver</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info general --}}
        <div class="admin-card p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Sede</div>
                    <div class="font-semibold text-white">{{ $pedido->sede->nombre }}</div>
                </div>
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Fecha</div>
                    <div class="text-sm text-white">{{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Método</div>
                    <div class="text-sm text-white">{{ $pedido->metodo_pago === 'qr_deuna' ? 'QR DeUna' : 'Efectivo' }}</div>
                </div>
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Estado</div>
                    <span class="badge badge-{{ $pedido->estado_badge['color'] }}">{{ $pedido->estado_badge['label'] }}</span>
                </div>
            </div>
        </div>

        {{-- Cliente que realizó el pedido (solo si inició sesión) --}}
        @if($pedido->user)
        <div class="admin-card p-6"
             style="background:linear-gradient(135deg,rgba(99,130,246,0.08),rgba(99,130,246,0.02));border-color:rgba(99,130,246,0.18);">
            <div class="flex items-center gap-2 mb-4">
                <x-admin.icon name="user" class="w-5 h-5 text-amber-400" />
                <span class="font-display font-black text-sm uppercase tracking-wider text-gray-300">Cliente identificado</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Nombre</div>
                    <div class="font-semibold text-white">{{ $pedido->user->name }}</div>
                </div>
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Correo</div>
                    <div class="text-sm text-white break-all">{{ $pedido->user->email }}</div>
                </div>
                @if($pedido->user->cedula)
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Cédula</div>
                    <div class="text-sm text-white">{{ $pedido->user->cedula }}</div>
                </div>
                @endif
                @if($pedido->user->telefono)
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Teléfono</div>
                    <div class="text-sm text-white">{{ $pedido->user->telefono }}</div>
                </div>
                @endif
                @if($pedido->user->carrera)
                <div>
                    <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1">Carrera</div>
                    <div class="text-sm text-white">{{ $pedido->user->carrera }}</div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="admin-card p-4 flex items-center gap-3"
             style="background:rgba(255,255,255,0.02);border-color:rgba(255,255,255,0.06);">
            <x-admin.icon name="user" class="w-5 h-5 opacity-40" />
            <div class="text-xs text-gray-500">Este pedido se hizo sin iniciar sesión — no hay datos de cliente para mostrar.</div>
        </div>
        @endif

        {{-- Ítems del pedido --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Productos pedidos</div>
            </div>
            <table class="admin-table">
                <thead>
                    <tr><th>Producto</th><th>Precio u.</th><th>Cant.</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    @foreach($pedido->items as $item)
                    <tr>
                        <td class="col-name">
                            {{ $item->nombre_snapshot }}
                            @if($item->combo_id)
                                <span class="badge badge-gold ml-2">Combo</span>
                            @endif
                        </td>
                        <td class="col-price">${{ number_format($item->precio_snapshot, 2) }}</td>
                        <td class="text-gray-300">×{{ $item->cantidad }}</td>
                        <td class="col-price">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-4 border-t border-white/[0.07] flex justify-between items-center">
                <span class="font-display font-black text-sm uppercase tracking-wider text-gray-400">TOTAL</span>
                <span class="font-display font-black text-2xl text-gold-400">{{ $pedido->total_formateado }}</span>
            </div>
        </div>

    </div>

    {{-- Columna lateral — acciones --}}
    <div class="space-y-5">

        {{-- Acciones --}}
        <div class="admin-card p-5 space-y-3">
            <div class="font-display font-black text-sm uppercase tracking-wider text-gray-400 mb-3">Acciones</div>

            @if($pedido->estado === 'pendiente_verificacion')
            <form method="POST" action="{{ route('admin.pedidos.verificar-qr', $pedido) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-success w-full justify-center py-3"
                        onclick="return confirm('¿Verificaste el pago en la app DeUna?')">
                    Verificar pago QR
                </button>
            </form>
            @endif

            @if($pedido->estado === 'pendiente_pago')
            <form method="POST" action="{{ route('admin.pedidos.cobrar', $pedido) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-success w-full justify-center py-3"
                        onclick="return confirm('¿Cobraste {{ $pedido->total_formateado }} en efectivo?')">
                    Cobrar en efectivo
                </button>
            </form>
            @endif

            @if($pedido->puedeEntregarse())
            <form method="POST" action="{{ route('admin.pedidos.entregar', $pedido) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-blue w-full justify-center py-3">
                    Marcar como entregado
                </button>
            </form>
            @endif

            @if($pedido->puedeCancelarse())
            <form method="POST" action="{{ route('admin.pedidos.cancelar', $pedido) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn-danger w-full justify-center py-3"
                        onclick="return confirm('¿Cancelar el pedido {{ $pedido->codigo }}? Esta acción queda en auditoría.')">
                    Cancelar pedido
                </button>
            </form>
            @endif

            @if($pedido->estado === 'entregado')
            <div class="text-center py-4 text-emerald-400 text-sm font-semibold">
                Pedido completado y entregado<br>
                <span class="text-xs text-gray-500">{{ $pedido->entregado_at?->format('H:i d/m/Y') }}</span>
            </div>
            @endif
        </div>

        {{-- Info del comprobante --}}
        @if($pedido->comprobante)
        <div class="admin-card p-5">
            <div class="font-display font-black text-sm uppercase tracking-wider text-gray-400 mb-3">Comprobante</div>
            <div class="text-xs space-y-1 text-gray-400">
                <div>N.º: <span class="text-white font-semibold">{{ $pedido->comprobante->numero_comprobante }}</span></div>
                <div>Impreso: <span class="text-white">{{ $pedido->comprobante->impreso ? 'Sí' : 'No' }}</span></div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection