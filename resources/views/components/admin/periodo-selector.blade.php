@props(['periodo' => 'hoy', 'fecha' => '', 'periodoLabel' => ''])

<form method="GET" id="periodo-form" class="admin-card p-4">
    {{-- Preservar otros filtros del form padre --}}
    @foreach(request()->except(['periodo','fecha','page']) as $k => $v)
        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
    @endforeach
    <input type="hidden" name="periodo" id="periodo-input" value="{{ $periodo }}">
    <input type="hidden" name="fecha"   id="fecha-input"   value="{{ $fecha ?: today()->toDateString() }}">

    <div class="flex flex-wrap items-center gap-2">
        <span class="ps-label">Período:</span>

        {{-- Botones rápidos --}}
        <div class="flex flex-wrap gap-1.5">
            @foreach([
                ['hoy',       'Hoy'],
                ['ayer',      'Ayer'],
                ['semana',    'Esta semana'],
                ['ult7',      'Últ. 7 días'],
                ['mes',       'Este mes'],
                ['ult30',     'Últ. 30 días'],
                ['ult6meses', 'Últ. 6 meses'],
                ['ult12',     'Últ. 12 meses'],
                ['todo',      'Todo el tiempo'],
            ] as [$val, $lbl])
            <button type="button"
                    onclick="setPeriodo('{{ $val }}')"
                    class="ps-btn {{ $periodo === $val ? 'ps-btn-active' : '' }}" style="color:rgba(255,255,255,0.50);">
                {{ $lbl }}
            </button>
            @endforeach
        </div>

        {{-- Fecha puntual --}}
        <div class="flex items-center gap-1.5 ml-auto">
            <input type="date" id="fecha-pick"
                   value="{{ $periodo === 'fecha' ? $fecha : today()->toDateString() }}"
                   class="admin-input" style="width:auto;padding:0.35rem 0.65rem;font-size:0.78rem;"
                   onchange="setPeriodoFecha(this.value)">
            <button type="button" onclick="setPeriodoFecha(document.getElementById('fecha-pick').value)"
                    class="ps-btn {{ $periodo === 'fecha' ? 'ps-btn-active' : '' }}"
                    style="color:rgba(255,255,255,0.50);">
                📅 Fecha exacta
            </button>
        </div>
    </div>

    {{-- Etiqueta del período activo --}}
    @if($periodoLabel)
    <div class="mt-2.5 flex items-center gap-2">
        <div class="ps-active-dot"></div>
        <span class="ps-active-label" style="color:rgba(255,255,255,0.50);">{{ $periodoLabel }}</span>
    </div>
    @endif
</form>

<style>
.ps-label {
    font-size: 0.62rem; font-weight: 900; text-transform: uppercase;
    letter-spacing: 0.14em; color: rgba(255,255,255,0.50);
    font-family: var(--font-display); white-space: nowrap;
}
.ps-btn {
    display: inline-flex; align-items: center;
    padding: 0.32rem 0.75rem; border-radius: 0.5rem;
    font-size: 0.72rem; font-weight: 700;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.4);
    cursor: pointer; white-space: nowrap;
    transition: all 0.15s ease;
    font-family: var(--font-display);
}
.ps-btn:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.8); border-color: rgba(255,255,255,0.15); }
.ps-btn-active { background: rgba(201,168,76,0.12) !important; border-color: rgba(201,168,76,0.35) !important; color: #c9a84c !important; }
.ps-active-dot { width: 6px; height: 6px; border-radius: 50%; background: #c9a84c; flex-shrink: 0; animation: blink 2s ease-in-out infinite; }
.ps-active-label { font-size: 0.72rem; color: rgba(255,255,255,0.35); font-weight: 600; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
</style>

<script>
function setPeriodo(val) {
    document.getElementById('periodo-input').value = val;
    document.getElementById('periodo-form').submit();
}
function setPeriodoFecha(val) {
    document.getElementById('periodo-input').value = 'fecha';
    document.getElementById('fecha-input').value   = val;
    document.getElementById('periodo-form').submit();
}
</script>
