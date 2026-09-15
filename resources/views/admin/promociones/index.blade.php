@extends('layouts.app')
@section('title','Promociones')
@section('page-icon','🎯')
@section('page-title','Promociones del Kiosco')
@section('page-subtitle','Slides que aparecen en modo reposo · Se rotan automáticamente')

@section('header-actions')
    <button onclick="abrirModal()" class="btn-gold">+ Nueva promoción</button>
@endsection

@section('content')
<div class="space-y-5 pt-2">

    {{-- Info banner --}}
    <div class="admin-card p-4 flex gap-3 items-center"
         style="background:linear-gradient(135deg,rgba(27,42,107,0.4),rgba(27,42,107,0.15));border-color:rgba(99,130,246,0.2);">
        <span class="text-2xl flex-shrink-0">📺</span>
        <div class="flex-1">
            <div class="text-sm font-bold text-white mb-0.5">Modo reposo activo</div>
            <div class="text-xs text-blue-300/70">
                Tras <strong class="text-white">2 minutos</strong> sin actividad en el kiosco, estas slides se muestran en pantalla completa con rotación automática. Al tocar la pantalla vuelve al menú.
            </div>
        </div>
        <div class="flex-shrink-0 text-right">
            <div class="text-2xl font-black text-white" style="font-family:var(--font-display)">{{ $promociones->where('activo',true)->count() }}</div>
            <div class="text-xs text-blue-300/50 uppercase tracking-wider font-bold">activas</div>
        </div>
    </div>

    {{-- Grid --}}
    @if($promociones->isEmpty())
    <div class="admin-card p-16 text-center">
        <div class="text-6xl mb-4" style="opacity:.15">🎯</div>
        <div class="font-black text-lg uppercase tracking-wider mb-2" style="font-family:var(--font-display);color:rgba(255,255,255,0.2);">
            Sin promociones
        </div>
        <p class="text-sm mb-6" style="color:rgba(255,255,255,0.18);">
            Crea tu primera promoción para que aparezca en el kiosco.
        </p>
        <button onclick="abrirModal()" class="btn-gold">+ Crear primera promoción</button>
    </div>

    @else
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

        @foreach($promociones as $promo)
        <div class="promo-card {{ $promo->activo ? 'promo-card-active' : 'promo-card-paused' }}"
             data-id="{{ $promo->id }}">

            {{-- Preview imagen --}}
            <div class="promo-preview">
                @if($promo->imagen_path)
                    <img src="{{ asset('storage/'.$promo->imagen_path) }}"
                         alt="{{ $promo->titulo }}"
                         class="promo-img">
                    <div class="promo-img-overlay"></div>
                @else
                    <div class="promo-no-img">
                        <span style="font-size:2.5rem;opacity:.25;">🎯</span>
                        <span style="font-size:0.65rem;color:rgba(255,255,255,0.50);font-weight:700;text-transform:uppercase;letter-spacing:0.1em;">Sin imagen</span>
                    </div>
                @endif

                {{-- Slide # y duración --}}
                <div class="promo-slide-badge">
                    Slide {{ $loop->iteration }} · {{ $promo->duracion_segundos }}s
                </div>

                {{-- Estado --}}
                <div class="promo-estado-badge {{ $promo->activo ? 'promo-estado-on' : 'promo-estado-off' }}">
                    {{ $promo->activo ? '🟢' : '⏸️' }}
                </div>

                {{-- Precio si tiene --}}
                @if($promo->precio_destacado)
                <div class="promo-precio-badge">${{ $promo->precio_destacado }}</div>
                @endif
            </div>

            {{-- Info --}}
            <div class="promo-body">
                <div class="promo-titulo">{{ $promo->titulo }}</div>
                @if($promo->descripcion)
                <div class="promo-desc" style="color:rgba(255,255,255,0.50);">
                    {{ $promo->descripcion }}
                </div>
                @endif
                @if($promo->sede)
                <div class="promo-sede">
                    {{ $promo->sede->slug === 'instituto' ? '🏛️' : '🚗' }} {{ $promo->sede->nombre }}
                </div>
                @else
                <div class="promo-sede" style="color:rgba(255,255,255,0.50);">🌐 Todas las sedes</div>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="promo-actions">
                {{-- Toggle activo/pausado --}}
                <form method="POST" action="{{ route('admin.promociones.toggle', $promo) }}" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="promo-btn {{ $promo->activo ? 'promo-btn-pause' : 'promo-btn-play' }} w-full">
                        {{ $promo->activo ? '⏸ Pausar' : '▶️ Activar' }}
                    </button>
                </form>

                {{-- Editar --}}
                <button
                        data-id="{{ $promo->id }}"
                        data-url="{{ route('admin.promociones.update', $promo) }}"
                        data-titulo="{{ e($promo->titulo) }}"
                        data-descripcion="{{ e($promo->descripcion) }}"
                        data-precio="{{ e($promo->precio_destacado) }}"
                        data-duracion="{{ $promo->duracion_segundos }}"
                        data-orden="{{ $promo->orden }}"
                        data-sede="{{ $promo->sede_id }}"
                        data-imagen="{{ $promo->imagen_path }}"
                        data-p1="{{ $promo->producto_id_1 }}"
                        data-p2="{{ $promo->producto_id_2 }}"
                        data-p3="{{ $promo->producto_id_3 }}"
                        onclick="abrirEditarBtn(this)"
                        class="promo-btn promo-btn-edit">✏️</button>

                {{-- Eliminar --}}
                <button type="button" class="promo-btn promo-btn-delete"
                        data-id="{{ $promo->id }}"
                        data-url="{{ route('admin.promociones.destroy', $promo) }}"
                        data-titulo="{{ e($promo->titulo) }}"
                        onclick="eliminarPromoBtn(this)">🗑</button>
            </div>
        </div>
        @endforeach

        {{-- Card agregar --}}
        <div onclick="abrirModal()" class="promo-add-card">
            <span style="font-size:2.5rem;opacity:.2;">➕</span>
            <span class="promo-add-label" style="color:rgba(255,255,255,0.50);">Nueva promoción</span>
        </div>
    </div>
    @endif

</div>

{{-- ═══════════ MODAL CREAR / EDITAR ═══════════ --}}
<div id="modal-promo" class="promo-modal-bg" style="display:none;">
    <div class="promo-modal" onclick="event.stopPropagation()">

        <div class="promo-modal-header">
            <div>
                <div class="promo-modal-title" id="modal-title">➕ Nueva Promoción</div>
                <div class="promo-modal-sub">Configura el slide que verán en el kiosco</div>
            </div>
            <button onclick="cerrarModal()" class="promo-modal-close">✕</button>
        </div>

        <form id="form-promo" method="POST" enctype="multipart/form-data" class="promo-modal-body">
            @csrf
            <span id="method-field"></span>

            {{-- Preview imagen en tiempo real --}}
            <div class="img-preview-wrap" onclick="document.getElementById('input-imagen').click()">
                <img id="img-preview" src="" alt="" style="display:none;">
                <div id="img-placeholder">
                    <span style="font-size:2rem;opacity:.3;">🖼️</span>
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.50);margin-top:0.4rem;">
                        Clic para subir imagen<br>
                        <span style="font-size:0.65rem;opacity:.7; color:rgba(255,255,255,0.50);">JPG, PNG, WEBP · Recomendado 1920×1080</span>
                    </span>
                </div>
                <input type="file" id="input-imagen" name="imagen" accept="image/*"
                       class="hidden" onchange="previewImagen(this)">
            </div>

            <div class="promo-modal-fields">
                <div class="field-group">
                    <label class="field-label" style="color:rgba(255,255,255,0.50);">Título *</label>
                    <input name="titulo" id="f-titulo" class="admin-input"
                        placeholder="Ej: ¡Combo Clásico $2.50!" required
                        oninput="validarTituloPromo(this.value)">
                    <span id="promo-titulo-error"
                        style="display:none;font-size:0.68rem;color:#f87171;font-weight:700;margin-top:0.2rem;">
                        ⚠️ Ya existe una promoción con ese título
                    </span>
                </div>

                <div class="field-group">
                    <label class="field-label" style="color:rgba(255,255,255,0.50);">Descripción</label>
                    <input name="descripcion" id="f-descripcion" class="admin-input"
                           placeholder="Breve descripción del producto u oferta">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <div class="field-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <label class="field-label" style="color:rgba(255,255,255,0.50);">Precio destacado</label>
                            <label class="promo-toggle-mini" style="gap:0.4rem;">
                                <input type="checkbox" id="promo-precio-auto"
                                       class="promo-toggle-hidden" checked
                                       onchange="togglePromoPrecioAuto(this.checked)">
                                <div class="promo-toggle-mini-track on" id="promo-precio-auto-track">
                                    <div class="promo-toggle-mini-thumb"></div>
                                </div>
                                <span style="font-size:0.6rem;color:rgba(255,255,255,0.5);font-weight:600;white-space:nowrap;">Auto</span>
                            </label>
                        </div>
                        <input name="precio_destacado" id="f-precio" class="admin-input"
                               placeholder="Ej: 2.50" readonly>
                        <span id="f-precio-hint" style="font-size:0.6rem;color:rgba(110,231,183,0.8);font-weight:700;">
                            ⚡ Se usará el precio calculado abajo
                        </span>
                    </div>
                    <div class="field-group">
                        <label class="field-label" style="color:rgba(255,255,255,0.50);">Duración (seg)</label>
                        <input name="duracion_segundos" id="f-duracion" type="number"
                               min="2" max="60" value="5" class="admin-input">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <div class="field-group">
                        <label class="field-label" style="color:rgba(255,255,255,0.50);">Sede</label>
                        <select name="sede_id" id="f-sede" class="admin-select w-full">
                            <option value="">🌐 Todas las sedes</option>
                            @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">
                                {{ $sede->slug === 'instituto' ? '🏛️' : '🚗' }} {{ $sede->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label" style="color:rgba(255,255,255,0.50);">Orden</label>
                        <input name="orden" id="f-orden" type="number" min="0" value="0" class="admin-input">
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label" style="color:rgba(255,255,255,0.50);">🛒 Productos vinculados <span style="color:rgba(255,255,255,0.50);font-weight:600;text-transform:none;letter-spacing:0;">(máx. 3 — aparece botón "Pedir ahora" con precio total)</span></label>
                    <div style="display:flex;flex-direction:column;gap:0.5rem;">
                        @foreach([1,2,3] as $n)
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <span style="font-size:0.65rem;font-weight:900;color:rgba(255,255,255,0.50);width:14px;flex-shrink:0;font-family:var(--font-display);">{{ $n }}</span>
                            <select name="producto_id_{{ $n }}" id="f-producto-{{ $n }}" class="admin-select w-full" onchange="calcularTotal()" >
                                <option value="">— Sin producto —</option>
                                @foreach($productos as $prod)
                                <option value="{{ $prod->id }}" data-precio="{{ $prod->precio }}">
                                    {{ $prod->nombre }} — ${{ number_format($prod->precio,2) }}
                                    {{ $prod->stock_activo && $prod->stock_actual <= 0 ? '⚠️ Sin stock' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>
                    <div id="total-preview" style="display:none;margin-top:0.5rem;padding:0.5rem 0.85rem;border-radius:0.6rem;background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);font-size:0.78rem;color:#c9a84c;font-weight:800;font-family:var(--font-display);">
                        Total: <span id="total-val">$0.00</span>
                    </div>
                    <span style="font-size:0.62rem;color:rgba(255,255,255,0.50);margin-top:3px;display:block;">Si un producto llega a 0 stock, la promoción se desactivará automáticamente.</span>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-gold flex-1 justify-center py-3" id="btn-guardar">
                    ✅ Crear promoción
                </button>
                <button type="button" onclick="cerrarModal()"
                        class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
/* ── Cards ── */
.promo-card {
    border-radius: 1rem; overflow: hidden;
    display: flex; flex-direction: column;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(255,255,255,0.07);
}
.promo-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,0.4); }
.promo-card-active  { background: #1a1f32; }
.promo-card-paused  { background: #141620; opacity: .7; }

.promo-preview {
    aspect-ratio: 16/9; position: relative;
    overflow: hidden; background: #0d1228;
    display: flex; align-items: center; justify-content: center;
}
.promo-img { width:100%;height:100%;object-fit:cover;transition:transform 0.35s ease; }
.promo-card:hover .promo-img { transform: scale(1.05); }
.promo-img-overlay {
    position:absolute;inset:0;
    background:linear-gradient(to bottom, transparent 40%, rgba(0,0,0,0.55) 100%);
}
.promo-no-img { display:flex;flex-direction:column;align-items:center;gap:0.4rem; }

.promo-slide-badge {
    position:absolute;top:0.5rem;left:0.5rem;
    padding:0.2rem 0.6rem;border-radius:999px;
    background:rgba(0,0,0,0.6);backdrop-filter:blur(6px);
    font-size:0.58rem;font-weight:900;text-transform:uppercase;
    letter-spacing:0.1em;color:rgba(201,168,76,0.9);
    font-family:var(--font-display);
}
.promo-estado-badge {
    position:absolute;top:0.5rem;right:0.5rem;
    width:24px;height:24px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    font-size:0.7rem;backdrop-filter:blur(6px);
}
.promo-estado-on  { background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.4); }
.promo-estado-off { background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3); }
.promo-precio-badge {
    position:absolute;bottom:0.5rem;right:0.5rem;
    padding:0.2rem 0.65rem;border-radius:999px;
    background:rgba(201,168,76,0.15);border:1px solid rgba(201,168,76,0.35);
    font-size:0.75rem;font-weight:900;color:#c9a84c;
    font-family:var(--font-display);
}

.promo-body { padding:0.85rem 1rem 0.6rem;flex:1; }
.promo-titulo { font-family:var(--font-display);font-weight:900;font-size:0.82rem;color:white;text-transform:uppercase;letter-spacing:0.02em;line-height:1.2;margin-bottom:0.3rem; }
.promo-desc { font-size:0.7rem;color:rgba(255,255,255,0.3);line-height:1.5;margin-bottom:0.4rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.promo-sede { font-size:0.62rem;color:rgba(255,255,255,0.2);font-weight:700;text-transform:uppercase;letter-spacing:0.08em; }

.promo-actions { display:flex;gap:0.4rem;padding:0.6rem 0.75rem;border-top:1px solid rgba(255,255,255,0.05); }
.promo-btn {
    display:inline-flex;align-items:center;justify-content:center;gap:0.3rem;
    padding:0.4rem 0.6rem;border-radius:0.5rem;border:none;cursor:pointer;
    font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;
    transition:all 0.15s ease;font-family:var(--font-display);
}
.promo-btn-pause  { background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.4);border:1px solid rgba(255,255,255,0.08); }
.promo-btn-pause:hover  { background:rgba(250,204,21,0.1);color:#facc15;border-color:rgba(250,204,21,0.25); }
.promo-btn-play   { background:rgba(16,185,129,0.1);color:#6ee7b7;border:1px solid rgba(16,185,129,0.2); }
.promo-btn-play:hover   { background:rgba(16,185,129,0.2);border-color:rgba(16,185,129,0.4); }
.promo-btn-edit   { background:rgba(99,130,246,0.1);color:#a5b4fc;border:1px solid rgba(99,130,246,0.2); }
.promo-btn-edit:hover   { background:rgba(99,130,246,0.2); }
.promo-btn-delete { background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
.promo-btn-delete:hover { background:rgba(239,68,68,0.18); }

.promo-add-card {
    border-radius:1rem;border:2px dashed rgba(255,255,255,0.07);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:0.75rem;cursor:pointer;min-height:220px;
    transition:all 0.2s ease;background:rgba(255,255,255,0.01);
}
.promo-add-card:hover { border-color:rgba(201,168,76,0.3);background:rgba(201,168,76,0.03); }
.promo-add-label { font-size:0.72rem;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.18);font-family:var(--font-display); }

/* ── Modal ── */
.promo-modal-bg {
    position:fixed;inset:0;z-index:9999;
    background:rgba(0,0,0,0.75);backdrop-filter:blur(8px);
    display:flex;align-items:center;justify-content:center;padding:1.5rem;
}
/* modal visibility controlled via JS only */
.promo-modal {
    width:100%;max-width:560px;max-height:90vh;overflow-y:auto;
    background:#141825;border:1px solid rgba(255,255,255,0.08);
    border-radius:1.25rem;
    box-shadow:0 32px 80px rgba(0,0,0,0.6);
    animation:modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes modalIn { from{opacity:0;transform:scale(0.94)} to{opacity:1;transform:scale(1)} }
.promo-modal-header {
    display:flex;justify-content:space-between;align-items:flex-start;
    padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.06);
}
.promo-modal-title { font-family:var(--font-display);font-weight:900;font-size:1rem;color:white;text-transform:uppercase;letter-spacing:0.04em; }
.promo-modal-sub   { font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:2px; }
.promo-modal-close { background:transparent;border:none;cursor:pointer;color:rgba(255,255,255,0.3);font-size:1.1rem;padding:0.25rem;transition:color 0.15s ease; }
.promo-modal-close:hover { color:white; }
.promo-modal-body  { padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:1rem; }
.promo-modal-fields { display:flex;flex-direction:column;gap:0.85rem; }
.field-group { display:flex;flex-direction:column;gap:0.3rem; }
.field-label { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.25);font-family:var(--font-display); }

/* Preview imagen upload */
.img-preview-wrap {
    width:100%;aspect-ratio:16/9;border-radius:0.75rem;overflow:hidden;
    border:2px dashed rgba(255,255,255,0.1);cursor:pointer;
    background:#0d1228;position:relative;
    display:flex;align-items:center;justify-content:center;
    transition:border-color 0.2s ease;
}
.img-preview-wrap:hover { border-color:rgba(201,168,76,0.3); }
#img-preview { width:100%;height:100%;object-fit:cover;position:absolute;inset:0;display:none; }
#img-placeholder { display:flex;flex-direction:column;align-items:center;gap:0.5rem;pointer-events:none; }

/* ── Toggle mini (auto-precio) ── */
.promo-toggle-mini { display:flex;align-items:center;cursor:pointer; }
.promo-toggle-hidden { display:none; }
.promo-toggle-mini-track { position:relative;width:2.1rem;height:1.15rem;border-radius:999px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);transition:all 0.25s ease;flex-shrink:0; }
.promo-toggle-mini-track.on { background:rgba(16,185,129,0.2);border-color:rgba(16,185,129,0.4);box-shadow:0 0 10px rgba(16,185,129,0.15); }
.promo-toggle-mini-thumb { position:absolute;top:2px;left:2px;width:0.85rem;height:0.85rem;border-radius:50%;background:rgba(255,255,255,0.3);transition:all 0.25s ease; }
.promo-toggle-mini-track.on .promo-toggle-mini-thumb { transform:translateX(0.95rem);background:#6ee7b7; }
</style>

<script>
const STORE_ROUTE = '{{ route("admin.promociones.store") }}';
const TITULOS_EXISTENTES = @json($promociones->pluck('titulo'));
const ORDENES_PROMOS = @json($promociones->pluck('orden'));
let _promoTituloOriginal = null;

function siguienteOrdenPromo() {
    if (!ORDENES_PROMOS.length) return 0;
    const max = Math.max(...ORDENES_PROMOS.map(o => parseInt(o) || 0));
    return max + 1;
}

function setImgPreview(src) {
    const img = document.getElementById('img-preview');
    const ph  = document.getElementById('img-placeholder');
    if (src) {
        img.src = src;
        img.style.display = 'block';
        if (ph) ph.style.display = 'none';
    } else {
        img.style.display = 'none';
        img.src = '';
        if (ph) ph.style.display = 'flex';
    }
}

function abrirModal() {
    _promoTituloOriginal = null;
    document.getElementById('modal-title').textContent = '➕ Nueva Promoción';
    document.getElementById('btn-guardar').textContent = '✅ Crear promoción';
    document.getElementById('form-promo').action = STORE_ROUTE;
    document.getElementById('method-field').innerHTML = '';
    document.getElementById('promo-titulo-error').style.display = 'none';
    document.getElementById('f-titulo').style.borderColor = '';
    document.getElementById('btn-guardar').disabled = false;
    document.getElementById('btn-guardar').style.opacity = '';
    ['f-titulo','f-descripcion','f-precio'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-duracion').value = '5';
    document.getElementById('f-orden').value    = siguienteOrdenPromo();
    document.getElementById('f-sede').value     = '';
    document.getElementById('f-producto-1').value = '';
    document.getElementById('f-producto-2').value = '';
    document.getElementById('f-producto-3').value = '';
    document.getElementById('input-imagen').value = '';
    document.getElementById('promo-precio-auto').checked = true;
    togglePromoPrecioAuto(true);
    setImgPreview(null);
    calcularTotal();
    document.getElementById('modal-promo').style.display = 'flex';
}

function abrirEditarBtn(btn) {
    const id = btn.dataset.id;
    document.getElementById('modal-title').textContent = '✏️ Editar Promoción';
    document.getElementById('btn-guardar').textContent = '💾 Guardar cambios';
    document.getElementById('form-promo').action = btn.dataset.url;
    document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f-titulo').value       = btn.dataset.titulo       || '';
    _promoTituloOriginal = (btn.dataset.titulo || '').toLowerCase().trim();
    // Reset error state when opening edit
    document.getElementById('promo-titulo-error').style.display = 'none';
    document.getElementById('f-titulo').style.borderColor = '';
    document.getElementById('btn-guardar').disabled = false;
    document.getElementById('btn-guardar').style.opacity = '';
    document.getElementById('f-descripcion').value  = btn.dataset.descripcion  || '';
    document.getElementById('f-duracion').value     = btn.dataset.duracion     || 5;
    document.getElementById('f-orden').value        = btn.dataset.orden        || 0;
    document.getElementById('f-sede').value         = btn.dataset.sede         || '';
    document.getElementById('f-producto-1').value   = btn.dataset.p1           || '';
    document.getElementById('f-producto-2').value   = btn.dataset.p2           || '';
    document.getElementById('f-producto-3').value   = btn.dataset.p3           || '';
    document.getElementById('input-imagen').value   = '';
    document.getElementById('promo-precio-auto').checked = false;
    togglePromoPrecioAuto(false);
    document.getElementById('f-precio').value       = btn.dataset.precio       || '';
    setImgPreview(btn.dataset.imagen ? '/storage/' + btn.dataset.imagen : null);
    calcularTotal();
    document.getElementById('modal-promo').style.display = 'flex';
}

function validarTituloPromo(valor) {
    const error = document.getElementById('promo-titulo-error');
    const input = document.getElementById('f-titulo');
    const btn   = document.getElementById('btn-guardar');
    const normalizado = valor.toLowerCase().trim();
    const esDuplicado = TITULOS_EXISTENTES.some(
        t => t.toLowerCase().trim() === normalizado &&
             normalizado !== _promoTituloOriginal
    );
    if (esDuplicado && normalizado !== '') {
        error.style.display = 'block';
        input.style.borderColor = 'rgba(248,113,113,0.5)';
        input.style.boxShadow   = '0 0 0 3px rgba(248,113,113,0.08)';
        btn.disabled = true;
        btn.style.opacity = '0.45';
        btn.style.cursor  = 'not-allowed';
    } else {
        error.style.display = 'none';
        input.style.borderColor = '';
        input.style.boxShadow   = '';
        btn.disabled = false;
        btn.style.opacity = '';
        btn.style.cursor  = '';
    }
}

function cerrarModal() {
    document.getElementById('modal-promo').style.display = 'none';
}

function previewImagen(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => setImgPreview(e.target.result);
    reader.readAsDataURL(input.files[0]);
}

function togglePromoPrecioAuto(activo) {
    const track = document.getElementById('promo-precio-auto-track');
    const input = document.getElementById('f-precio');
    const hint  = document.getElementById('f-precio-hint');
    track.classList.toggle('on', activo);
    if (activo) {
        input.readOnly = true;
        hint.textContent = '⚡ Se usará el precio calculado abajo';
        hint.style.color = 'rgba(110,231,183,0.8)';
        calcularTotal();
    } else {
        input.readOnly = false;
        hint.textContent = '✏️ Ingresa el precio que tú quieras';
        hint.style.color = 'rgba(255,255,255,0.35)';
        input.focus();
    }
}

function calcularTotal() {
    let total = 0;
    let count = 0;
    [1,2,3].forEach(n => {
        const sel = document.getElementById('f-producto-' + n);
        if (!sel) return;
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value && opt.dataset.precio) {
            total += parseFloat(opt.dataset.precio);
            count++;
        }
    });
    const preview = document.getElementById('total-preview');
    const val     = document.getElementById('total-val');
    const precioField = document.getElementById('f-precio');
    const auto = document.getElementById('promo-precio-auto')?.checked;
    if (preview && val) {
        if (count > 0) {
            preview.style.display = 'block';
            val.textContent = '$' + total.toFixed(2);
            if (precioField && auto) {
                precioField.value = total.toFixed(2);
            }
        } else {
            preview.style.display = 'none';
            if (precioField && auto) {
                precioField.value = '';
            }
        }
    }
}

function eliminarPromoBtn(btn) {
    const titulo = btn.dataset.titulo;
    const url    = btn.dataset.url;
    if (!window.confirm('¿Eliminar «' + titulo + '»?')) return;

    const token = document.querySelector('meta[name="csrf-token"]').content;
    btn.disabled = true;
    btn.textContent = '⏳';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': token,
        },
        body: '_method=DELETE&_token=' + encodeURIComponent(token),
        redirect: 'manual',
    })
    .then(r => {
        // Laravel responde 302 tras back() — status 0 o redirected = éxito
        if (r.ok || r.redirected || r.type === 'opaqueredirect' || r.status === 0) {
            const card = btn.closest('[class*="promo-card"]');
            if (card) {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => location.reload(), 350);
            } else {
                location.reload();
            }
        } else {
            alert('Error al eliminar. Código: ' + r.status);
            btn.disabled = false;
            btn.textContent = '🗑';
        }
    })
    .catch(() => { location.reload(); });
}

// Cerrar al hacer clic en el fondo
document.getElementById('modal-promo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

/* ── Auto-actualización: si algo cambia desde otro lugar (productos,
   combos, configuración, etc.), esta pantalla se refresca sola.
   No interrumpe si hay un modal abierto (crear/editar promoción). ── */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-promo')?.style.display === 'flex';
    if (!modalAbierto) {
        location.reload();
    }
});
</script>
<script src="{{ asset('js/kiosco-realtime.js') }}"></script>
@endsection