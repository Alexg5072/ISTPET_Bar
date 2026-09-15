@extends('layouts.app')
@section('title','Movimientos de Stock')
@section('page-icon','📋')
@section('page-title','Movimientos de Stock')
@section('page-subtitle','Historial completo de entradas, salidas y ajustes')
@section('header-actions')
    <a href="{{ route('admin.stock.index') }}" class="btn-ghost">← Volver al stock</a>
@endsection

@section('content')
<div class="pt-2">
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">📋 Historial de movimientos</div>
            <span class="text-xs text-gray-500">{{ $movimientos->total() }} registros</span>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="color:rgba(255,255,255,0.50)">Fecha</th><th style="color:rgba(255,255,255,0.50)">Producto</th><th style="color:rgba(255,255,255,0.50)">Tipo</th>
                        <th style="color:rgba(255,255,255,0.50)">Cantidad</th><th style="color:rgba(255,255,255,0.50)">Antes</th><th style="color:rgba(255,255,255,0.50)">Después</th>
                        <th style="color:rgba(255,255,255,0.50)">Motivo</th><th style="color:rgba(255,255,255,0.50)">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    <tr>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50)">{{ $mov->created_at->format('d/m H:i') }}</td>
                        <td class="col-name">{{ $mov->producto?->nombre ?? '—' }}</td>
                        <td>
                            @php
                                $tipoColors = [
                                    'entrada'    => 'badge-success',
                                    'salida'     => 'badge-danger',
                                    'venta'      => 'badge-blue',
                                    'ajuste'     => 'badge-pending',
                                    'devolucion' => 'badge-gold',
                                ];
                            @endphp
                            <span class="badge {{ $tipoColors[$mov->tipo] ?? 'badge-gray' }}">
                                {{ ucfirst($mov->tipo) }}
                            </span>
                        </td>
                        <td class="font-display font-bold {{ $mov->cantidad >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $mov->cantidad >= 0 ? '+' : '' }}{{ $mov->cantidad }}
                        </td>
                        <td class="text-gray-400">{{ $mov->stock_antes }}</td>
                        <td class="font-bold text-white">{{ $mov->stock_despues }}</td>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50)">{{ $mov->motivo ?? '—' }}</td>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50)">{{ $mov->user?->name ?? 'Sistema' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-10 text-gray-600">Sin movimientos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/[0.07]">{{ $movimientos->links() }}</div>
    </div>
</div>
@endsection