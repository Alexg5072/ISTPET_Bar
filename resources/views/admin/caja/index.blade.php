@extends('layouts.app')
@section('title','Caja')
@section('page-icon')
<x-admin.icon name="caja" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Caja del Día')
@section('page-subtitle', 'Resumen · ' . ($periodoLabel ?? 'Hoy'))

@section('content')
<div class="space-y-6 pt-2">

    {{-- ── Selector de período ── --}}
    <x-admin.periodo-selector :periodo="$periodo" :fecha="$fecha" :periodoLabel="$periodoLabel" />

    {{-- Resumen por sede --}}
    @foreach($resumen as $item)
    <div class="admin-card overflow-hidden">
        {{-- Header con color de sede --}}
        <div class="px-6 py-5 flex items-center justify-between"
             style="background:linear-gradient(135deg, {{ $item['sede']->color_primario }}dd, {{ $item['sede']->color_primario }});">
            <div class="flex items-center gap-3">
                <span class="text-3xl">@if($item['sede']->slug === 'instituto')<x-admin.icon name="instituto" class="w-7 h-7 text-amber-400" />@else<x-admin.icon name="car" class="w-7 h-7 text-blue-400" />@endif</span>
                <div>
                    <div class="font-display font-black text-lg text-white uppercase tracking-wide">
                        Caja {{ $item['sede']->slug === 'instituto' ? 'Instituto' : 'Conducción' }}
                    </div>
                    <div class="text-white/55 text-xs">{{ $item['sede']->nombre }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="font-display font-black text-3xl text-gold-400">
                    ${{ number_format($item['total'], 2) }}
                </div>
                <div class="text-white/50 text-xs">Total cobrado — {{ $periodoLabel }}</div>
            </div>
        </div>

        {{-- Desglose --}}
        <div class="grid grid-cols-3 divide-x divide-white/[0.06]">
            <div class="p-5 text-center">
                <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-2" style="color:rgba(255,255,255,0.50);">Efectivo</div>
                <div class="font-display font-black text-2xl text-white">${{ number_format($item['efectivo'], 2) }}</div>
            </div>
            <div class="p-5 text-center">
                <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-2" style="color:rgba(255,255,255,0.50);">QR DeUna</div>
                <div class="font-display font-black text-2xl text-white">${{ number_format($item['qr'], 2) }}</div>
            </div>
            <div class="p-5 text-center">
                <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-2" style="color:rgba(255,255,255,0.50);">Pedidos</div>
                <div class="font-display font-black text-2xl text-white">{{ $item['pedidos'] }}</div>
            </div>
        </div>

        {{-- Barra de progreso efectivo vs QR --}}
        @if($item['total'] > 0)
        <div class="px-6 pb-2">
            <div class="flex justify-between text-[0.65rem] text-gray-500 mb-1 uppercase tracking-wider">
                <span style="color:rgba(255,255,255,0.50);">Efectivo {{ round($item['efectivo'] / $item['total'] * 100) }}%</span>
                <span style="color:rgba(255,255,255,0.50);">QR {{ round($item['qr'] / $item['total'] * 100) }}%</span>
            </div>
            <div class="h-2 bg-white/5 rounded-full overflow-hidden flex">
                <div class="h-full bg-yellow-400 transition-all duration-500"
                     style="width:{{ round($item['efectivo'] / $item['total'] * 100) }}%"></div>
                <div class="h-full bg-blue-400 transition-all duration-500"
                     style="width:{{ round($item['qr'] / $item['total'] * 100) }}%"></div>
            </div>
        </div>
        @endif

        {{-- Botón cierre --}}
        <div class="px-6 pb-5 pt-3">
            <a href="{{ route('admin.caja.cierre', $item['sede']) }}?periodo={{ $periodo }}&fecha={{ $fecha }}"
               class="btn-gold w-full justify-center py-3">
                Imprimir cierre de caja {{ $item['sede']->slug === 'instituto' ? 'Instituto' : 'Conducción' }}
            </a>
        </div>
    </div>
    @endforeach

    {{-- Total global --}}
    <div class="admin-card p-6 flex items-center justify-between">
        <div>
            <div class="text-[0.65rem] font-black uppercase tracking-wider text-gray-500 mb-1" style="color:rgba(255,255,255,0.50);">Total global</div>
            <div class="text-xs text-gray-600" style="color:rgba(255,255,255,0.50);">
                {{ $periodoLabel }} · Suma de todas las sedes
            </div>
        </div>
        <div class="font-display font-black text-4xl text-gold-400">
            ${{ number_format(collect($resumen)->sum('total'), 2) }}
        </div>
    </div>

</div>
@endsection