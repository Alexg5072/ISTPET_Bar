{{-- resources/views/components/modulo-proximamente.blade.php --}}
@props(['modulo' => 'este módulo', 'descripcion' => null])
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 bg-white/5 border border-white/10 text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 opacity-60">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
        </svg>
    </div>
    <h2 class="font-display font-black text-2xl uppercase tracking-widest text-gray-400 mb-3">
        Módulo en Preparación
    </h2>
    <p class="text-sm text-gray-400 max-w-md leading-relaxed mb-6">
        {{ $descripcion ?? "El módulo de {$modulo} está diseñado y preparado a nivel de base de datos y arquitectura, pero aún no está habilitado en esta versión." }}
    </p>
    <div class="flex flex-col items-center gap-3">
        <span class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest bg-yellow-400/10 text-yellow-500 border border-yellow-400/25">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Disponible en próxima fase
        </span>
        <p class="text-xs text-gray-500">
            Para habilitarlo: Configuración → Módulos → Activar
        </p>
    </div>
</div>
