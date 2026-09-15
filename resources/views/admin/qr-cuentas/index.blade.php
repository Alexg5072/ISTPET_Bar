@extends('layouts.app')
@section('title','QR Cuentas')
@section('page-icon','📱')
@section('page-title','Cuentas QR')
@section('page-subtitle','Gestión del QR de DeUna del bar')

@section('header-actions')
    <button onclick="document.getElementById('modal-qr').classList.remove('hidden')" class="btn-gold">
        + Subir QR
    </button>
@endsection

@section('content')
<div class="space-y-6 pt-2">

    {{-- Info de funcionamiento actual --}}
    <div class="p-4 bg-blue-400/5 border border-blue-400/15 rounded-xl flex gap-3 items-start">
        <span class="text-xl">ℹ️</span>
        <div class="text-sm text-blue-300">
            <strong>Versión actual:</strong> El sistema usa una sola cuenta QR global para ambas sedes.
            El módulo de múltiples QR por sede estará disponible en la siguiente fase.
        </div>
    </div>

    {{-- QR activo --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($qrCuentas as $qr)
        <div class="admin-card p-6 flex gap-5 items-start">
            {{-- Imagen QR --}}
            <div class="w-32 h-32 bg-white rounded-xl flex items-center justify-center flex-shrink-0 border-2 border-gold-400/30 overflow-hidden">
                @if($qr->qr_url)
                    <img src="{{ $qr->qr_url }}" alt="QR {{ $qr->nombre }}" class="w-full h-full object-contain p-2">
                @else
                    <div class="text-center p-2">
                        <div class="text-4xl mb-1">📱</div>
                        <div class="text-[0.6rem] text-gray-400 font-bold uppercase">Sin imagen</div>
                    </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="flex-1">
                <div class="font-display font-black text-lg text-white uppercase mb-1">{{ $qr->nombre }}</div>
                <div class="text-sm text-gray-400 mb-2">Titular: <span class="text-white font-semibold">{{ $qr->titular }}</span></div>
                <div class="flex gap-2 flex-wrap">
                    <span class="badge {{ $qr->activo ? 'badge-success' : 'badge-danger' }}">
                        {{ $qr->activo ? '✅ Activo' : '❌ Inactivo' }}
                    </span>
                    @if($qr->is_global)
                        <span class="badge badge-blue">🌐 Global</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-3">{{ $qr->descripcion }}</p>
            </div>
        </div>
        @empty
        <div class="admin-card p-10 col-span-2 text-center text-gray-500">
            <div class="text-5xl mb-4 opacity-30">📱</div>
            <p class="font-display font-bold uppercase tracking-wider">Sin cuentas QR registradas</p>
            <p class="text-sm mt-2">Sube el QR de DeUna del bar para que aparezca en el kiosco.</p>
        </div>
        @endforelse
    </div>

    {{-- Módulo múltiples QR --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">📱 Múltiples QR por sede</div>
            <span class="badge badge-pending">⏳ Próximamente</span>
        </div>
        <div class="p-6">
            <x-modulo-proximamente modulo="múltiples cuentas QR"
                descripcion="En la próxima versión podrás tener una cuenta QR diferente para el Instituto y otra para la Escuela de Conducción, con validaciones independientes. La tabla qr_cuentas ya soporta esta estructura." />
        </div>
    </div>

</div>

{{-- Modal subir QR --}}
<div id="modal-qr" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-admin-card border border-white/10 rounded-2xl p-6 w-full max-w-md animate-scale-in">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-display font-black text-lg uppercase tracking-wider">📱 Subir QR DeUna</h3>
            <button onclick="document.getElementById('modal-qr').classList.add('hidden')" class="text-gray-500 hover:text-white text-xl">✕</button>
        </div>
        <form action="{{ route('admin.qr-cuentas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Nombre</label>
                <input name="nombre" class="admin-input" placeholder="Ej: DeUna Bar ISTPET" required>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Titular</label>
                <input name="titular" class="admin-input" placeholder="Nombre del titular" required>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Imagen del QR</label>
                <input type="file" name="imagen" accept="image/*" class="admin-input py-1.5 text-xs">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold flex-1 justify-center py-3">✅ Guardar</button>
                <button type="button" onclick="document.getElementById('modal-qr').classList.add('hidden')" class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>
@endsection