@extends('layouts.app')
@section('title','Pedidos')
@section('page-icon','🧾')
@section('page-title','Gestión de Pedidos')
@section('page-subtitle','Verifica pagos QR · Cobra en efectivo · Marca entregas')

@section('content')
<div class="space-y-5 pt-2">

    {{-- ── Selector de período ── --}}
    <x-admin.periodo-selector :periodo="$periodo" :fecha="$fecha" :periodoLabel="$periodoLabel" />

    {{-- Filtros adicionales --}}
    <form method="GET" action="{{ route('admin.pedidos.index') }}"
          class="admin-card p-4 flex flex-wrap gap-3 items-end">
        {{-- Preservar período activo --}}
        <input type="hidden" name="periodo" value="{{ $periodo }}">
        <input type="hidden" name="fecha"   value="{{ $fecha }}">

        <div class="flex-1 min-w-[160px]">
            <label class="text-[0.65rem] font-black uppercase tracking-wider block mb-1" style="color:rgba(255,255,255,0.50);">Sede</label>
            <select name="sede" class="admin-select w-full">
                <option value="">Todas las sedes</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}" {{ request('sede') == $sede->id ? 'selected' : '' }}>
                        {{ $sede->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="text-[0.65rem] font-black uppercase tracking-wider block mb-1" style="color:rgba(255,255,255,0.50);">Estado</label>
            <select name="estado" class="admin-select w-full">
                <option value="">Todos los estados</option>
                <option value="pendiente_pago"         {{ request('estado') === 'pendiente_pago' ? 'selected' : '' }}>💰 Pend. Pago</option>
                <option value="pendiente_verificacion" {{ request('estado') === 'pendiente_verificacion' ? 'selected' : '' }}>📱 Pend. QR</option>
                <option value="pagado"                 {{ request('estado') === 'pagado' ? 'selected' : '' }}>✅ Pagado</option>
                <option value="entregado"              {{ request('estado') === 'entregado' ? 'selected' : '' }}>📦 Entregado</option>
                <option value="cancelado"              {{ request('estado') === 'cancelado' ? 'selected' : '' }}>❌ Cancelado</option>
            </select>
        </div>
        <div class="flex-1 min-w-[160px]">
            <label class="text-[0.65rem] font-black uppercase tracking-wider block mb-1" style="color:rgba(255,255,255,0.50);">Método</label>
            <select name="metodo" class="admin-select w-full">
                <option value="">Todos</option>
                <option value="efectivo"  {{ request('metodo') === 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                <option value="qr_deuna"  {{ request('metodo') === 'qr_deuna' ? 'selected' : '' }}>📱 QR DeUna</option>
            </select>
        </div>
        <button type="submit" class="btn-primary py-2">Filtrar</button>
        <a href="{{ route('admin.pedidos.index') }}" class="btn-ghost py-2">Limpiar</a>
    </form>

    {{-- Guía rápida --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-4 bg-yellow-400/5 border border-yellow-400/15 rounded-xl">
            <div class="font-display font-black text-sm text-yellow-400 mb-2">💵 Flujo Efectivo</div>
            <ol class="text-xs space-y-1 list-decimal list-inside" style="color:rgba(255,255,255,0.55);">
                <li>Cliente presenta comprobante en caja</li>
                <li>Cobras el monto indicado</li>
                <li>Presionas <strong class="text-white">💵 Cobrar</strong></li>
                <li>Presionas <strong class="text-white">📦 Entregar</strong> al dar el pedido</li>
            </ol>
        </div>
        <div class="p-4 bg-blue-400/5 border border-blue-400/15 rounded-xl">
            <div class="font-display font-black text-sm text-blue-400 mb-2">📱 Flujo QR DeUna</div>
            <ol class="text-xs space-y-1 list-decimal list-inside" style="color:rgba(255,255,255,0.55);">
                <li>Verificas en tu app DeUna que llegó el pago</li>
                <li>Presionas <strong class="text-white">✅ Verificar QR</strong></li>
                <li>Presionas <strong class="text-white">📦 Entregar</strong> al dar el pedido</li>
            </ol>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">🧾 Pedidos — {{ $periodoLabel }}</div>
            <span class="text-xs" style="color:rgba(255,255,255,0.50);">{{ $pedidos->total() }} pedidos</span>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="color:rgba(255,255,255,0.50)">N.º</th>
                        <th style="color:rgba(255,255,255,0.50)">Sede</th>
                        <th style="color:rgba(255,255,255,0.50)">Cliente</th>
                        <th style="color:rgba(255,255,255,0.50)">Hora</th>
                        <th style="color:rgba(255,255,255,0.50)">Método</th>
                        <th style="color:rgba(255,255,255,0.50)">Total</th>
                        <th style="color:rgba(255,255,255,0.50)">Estado</th>
                        <th style="color:rgba(255,255,255,0.50)">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                    <tr>
                        <td class="col-id">{{ $pedido->codigo }}</td>
                        <td>
                            <span class="badge {{ $pedido->sede->slug === 'instituto' ? 'badge-blue' : 'badge-gold' }}">
                                {{ $pedido->sede->slug === 'instituto' ? '🏛️ IST' : '🚗 Cond.' }}
                            </span>
                        </td>
                        <td class="text-xs">
                            @if($pedido->user)
                                <span style="color:rgba(165,180,252,0.9);font-weight:600;">👤 {{ $pedido->user->primer_nombre }}</span>
                            @else
                                <span style="color:rgba(255,255,255,0.25);">🕶️ Anónimo</span>
                            @endif
                        </td>
                        <td class="text-xs" style="color:rgba(255,255,255,0.50);">{{ $pedido->created_at->format('d/m H:i') }}</td>
                        <td class="text-xs" style="color:rgba(255,255,255,0.75);">{{ $pedido->metodo_pago === 'qr_deuna' ? '📱 QR DeUna' : '💵 Efectivo' }}</td>
                        <td class="col-price">{{ $pedido->total_formateado }}</td>
                        <td>
                            <span class="badge badge-{{ $pedido->estado_badge['color'] }}">
                                {{ $pedido->estado_badge['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2 flex-wrap">
                                @if($pedido->estado === 'pendiente_verificacion')
                                <form method="POST" action="{{ route('admin.pedidos.verificar-qr', $pedido) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-success"
                                            onclick="return confirm('¿Verificaste el pago QR en DeUna?')">
                                        ✅ Verificar QR
                                    </button>
                                </form>
                                @endif

                                @if($pedido->estado === 'pendiente_pago')
                                <form method="POST" action="{{ route('admin.pedidos.cobrar', $pedido) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-success"
                                            onclick="return confirm('¿Cobraste ${{ $pedido->total }} en efectivo?')">
                                        💵 Cobrar
                                    </button>
                                </form>
                                @endif

                                @if($pedido->puedeEntregarse())
                                <form method="POST" action="{{ route('admin.pedidos.entregar', $pedido) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-blue">📦 Entregar</button>
                                </form>
                                @endif

                                <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn-ghost">Ver</a>

                                @if($pedido->puedeCancelarse())
                                <form method="POST" action="{{ route('admin.pedidos.cancelar', $pedido) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-danger"
                                            onclick="return confirm('¿Cancelar el pedido {{ $pedido->codigo }}?')">
                                        ❌
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-16" style="color:rgba(255,255,255,0.40);">
                            Sin pedidos con estos filtros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/[0.07]">
            {{ $pedidos->withQueryString()->links() }}
        </div>
    </div>

</div>
@endsection