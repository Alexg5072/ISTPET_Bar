{{-- resources/views/components/modulo-proximamente.blade.php --}}
@props(['modulo' => 'este módulo', 'descripcion' => null])
<div class="flex flex-col items-center justify-center py-24 text-center">
    <div class="text-7xl mb-6 opacity-20">🔒</div>
    <h2 class="font-display font-black text-2xl uppercase tracking-widest text-gray-500 mb-3">
        Módulo en Preparación
    </h2>
    <p class="text-sm text-gray-600 max-w-md leading-relaxed mb-6">
        {{ $descripcion ?? "El módulo de {$modulo} está diseñado y preparado a nivel de base de datos y arquitectura, pero aún no está habilitado en esta versión." }}
    </p>
    <div class="flex flex-col items-center gap-3">
        <span class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest bg-yellow-400/10 text-yellow-500 border border-yellow-400/25">
            ⏳ Disponible en próxima fase
        </span>
        <p class="text-xs text-gray-700">
            Para habilitarlo: Configuración → Módulos → Activar
        </p>
    </div>
</div>
