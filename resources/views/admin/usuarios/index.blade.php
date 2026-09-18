{{-- admin/usuarios/index.blade.php --}}
@extends('layouts.app')
@section('title','Usuarios')
@section('page-icon')
<x-admin.icon name="usuarios" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Usuarios y Roles')
@section('page-subtitle','Gestiona quién accede al sistema y qué puede hacer')
@section('header-actions')
    <button onclick="document.getElementById('modal-usuario').classList.remove('hidden')" class="btn-gold">
        + Nuevo usuario
    </button>
@endsection
@section('content')
<div class="space-y-6 pt-2">

    {{-- Cards de roles --}}
    <div class="grid gap-4" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
        @php
        $roleInfo = [
            'superadmin' => ['icon'=>'','color'=>'gold-400','label'=>'Superadmin','desc'=>'Acceso total'],
            'admin'      => ['icon'=>'','color'=>'blue-400','label'=>'Admin','desc'=>'Gestión operativa'],
            'cajero'     => ['icon'=>'','color'=>'emerald-400','label'=>'Cajero','desc'=>'Pedidos y caja'],
            'visor'      => ['icon'=>'','color'=>'gray-400','label'=>'Visor','desc'=>'Solo lectura'],
            'usuario'    => ['icon'=>'','color'=>'purple-400','label'=>'Usuario','desc'=>'Cliente kiosco'],
        ];
        @endphp
        @foreach($roles as $rol)
        <div class="admin-card p-4">
            <div class="mb-2 flex justify-center text-amber-400"><x-admin.icon name="user" class="w-6 h-6" /></div>
            <div class="font-display font-black text-sm uppercase text-white">{{ $roleInfo[$rol->name]['label'] ?? $rol->name }}</div>
            <div class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50);">
                {{ $roleInfo[$rol->name]['desc'] ?? '' }}
            </div>
            <div class="mt-2 font-display font-black text-xl text-{{ $roleInfo[$rol->name]['color'] ?? 'white' }}">
                {{ $usuarios->filter(fn($u)=>$u->hasRole($rol->name))->count() }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabla usuarios --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Usuarios registrados</div>
            <span class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50);">
                {{ $usuarios->total() }} usuarios
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr><th style="color:rgba(255,255,255,0.50)">Usuario</th><th style="color:rgba(255,255,255,0.50)">Correo</th><th style="color:rgba(255,255,255,0.50)">Rol</th><th style="color:rgba(255,255,255,0.50)">Sede</th><th style="color:rgba(255,255,255,0.50)">Último acceso</th><th style="color:rgba(255,255,255,0.50)">Estado</th><th style="color:rgba(255,255,255,0.50)">Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-inst-600 to-gold-500 flex items-center justify-center font-display font-black text-white text-xs flex-shrink-0">
                                    {{ $usuario->initials }}
                                </div>
                                <span class="col-name">{{ $usuario->name }}</span>
                            </div>
                        </td>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50);">
                            {{ $usuario->email }}
                        </td>
                        <td>
                            <span class="badge badge-gold">{{ $usuario->getRoleNames()->first() ?? '—' }}</span>
                        </td>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50);">
                            {{ $usuario->sede?->nombre ?? 'Ambas' }}
                        </td>
                        <td class="text-xs text-gray-500" style="color:rgba(255,255,255,0.50);">
                            {{ $usuario->ultimo_acceso?->diffForHumans() ?? 'Nunca' }}
                        </td>
                        <td>
                            <span class="badge {{ $usuario->activo ? 'badge-success' : 'badge-danger' }}">
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.usuarios.toggle', $usuario) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-ghost text-xs py-1 px-2">
                                        {{ $usuario->activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                @if(!$usuario->hasRole('superadmin'))
                                <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger"
                                            onclick="return confirm('¿Eliminar a {{ $usuario->name }}?')"><x-admin.icon name="trash" class="w-3.5 h-3.5" /></button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-600">Sin usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/[0.07]">{{ $usuarios->links() }}</div>
    </div>
</div>

{{-- Modal nuevo usuario --}}
<div id="modal-usuario" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-admin-card border border-white/10 rounded-2xl p-6 w-full max-w-md animate-scale-in">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-display font-black text-lg uppercase tracking-wider">Nuevo Usuario</h3>
            <button onclick="document.getElementById('modal-usuario').classList.add('hidden')" class="text-gray-500 hover:text-white text-xl">✕</button>
        </div>
        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-black uppercase tracking-wider text-gray-400 block mb-1">Nombre completo</label>
                <input name="name" class="admin-input" placeholder="Ej: María García"
                       pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]{3,100}$"
                       title="Solo letras y espacios (mínimo 3 caracteres)"
                       onkeypress="return /[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/.test(event.key)"
                       oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/g, '')"
                       required>
            </div>
            <div>
                <label class="text-xs font-black uppercase tracking-wider text-gray-400 block mb-1">Correo electrónico</label>
                <input name="email" type="email" class="admin-input" placeholder="usuario@istpet.edu.ec"
                       pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                       title="Correo electrónico válido con @ y dominio"
                       required>
            </div>
            <div>
                <label class="text-xs font-black uppercase tracking-wider text-gray-400 block mb-1">Contraseña temporal</label>
                <input name="password" type="password" class="admin-input" placeholder="Mínimo 8 caracteres" minlength="8" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-black uppercase tracking-wider text-gray-400 block mb-1">Rol</label>
                    <select name="role" id="select-role-modal" class="admin-select w-full" required onchange="toggleSedeRequerida(this.value)">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-black uppercase tracking-wider text-gray-400 block mb-1">
                        Sede <span id="sede-req-indicator" class="text-rose-400 hidden">*</span>
                    </label>
                    <select name="sede_id" id="select-sede-modal" class="admin-select w-full">
                        <option value="">Ambas / Global</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-gold flex-1 justify-center py-3">Crear usuario</button>
                <button type="button" onclick="document.getElementById('modal-usuario').classList.add('hidden')" class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSedeRequerida(role) {
    const ind = document.getElementById('sede-req-indicator');
    const select = document.getElementById('select-sede-modal');
    if (role === 'cajero' || role === 'visor') {
        if (ind) ind.classList.remove('hidden');
        if (select) select.required = true;
    } else {
        if (ind) ind.classList.add('hidden');
        if (select) select.required = false;
    }
}
</script>
@endsection