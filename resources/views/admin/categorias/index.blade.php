@extends('layouts.app')
@section('title','Categorías')
@section('page-icon')
<x-admin.icon name="categorias" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Categorías')
@section('page-subtitle','Organiza el menú en categorías para el kiosco')
@section('header-actions')
    <button onclick="abrirModalCat()" class="btn-gold">+ Nueva categoría</button>
@endsection

@section('content')
<div class="space-y-5 pt-2">

    {{-- Grid de categorías --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($categorias as $cat)
        <div class="cat-card {{ $cat->activo ? '' : 'cat-card-off' }}">
            <div class="cat-card-top">
                <div class="cat-icon flex items-center justify-center text-amber-400"><x-admin.icon name="categorias" class="w-8 h-8" /></div>
                <div class="cat-badge-estado {{ $cat->activo ? 'cat-on' : 'cat-off' }}">
                    {{ $cat->activo ? '●' : '○' }}
                </div>
            </div>
            <div class="cat-nombre">{{ $cat->nombre }}</div>
            <div class="cat-meta">
                @if($cat->sede)
                    {{ $cat->sede->slug === 'instituto' ? 'Instituto' : 'Conducción' }}
                @else
                    Ambas sedes
                @endif
                · Orden {{ $cat->orden }}
            </div>
            <div class="cat-actions">
                <button onclick="editarCat({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ $cat->icono }}', {{ $cat->activo ? 1 : 0 }}, {{ $cat->orden }}, '{{ $cat->sede_id }}')"
                        class="cat-btn cat-btn-edit">Editar</button>
                <form method="POST" action="{{ route('admin.categorias.destroy', $cat) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="cat-btn cat-btn-del"
                            onclick="return confirm('¿Eliminar «{{ $cat->nombre }}»?')"></button>
                </form>
            </div>
        </div>
        @empty
        <div class="admin-card p-14 col-span-5 text-center">
            <div class="flex justify-center opacity-10 mb-4 text-amber-400"><x-admin.icon name="categorias" class="w-16 h-16" /></div>
            <div class="font-display font-black text-lg uppercase tracking-wider mb-3" style="color:rgba(255,255,255,0.2);">Sin categorías</div>
            <button onclick="abrirModalCat()" class="btn-gold">+ Crear primera categoría</button>
        </div>
        @endforelse

        {{-- Card agregar --}}
        <div onclick="abrirModalCat()" class="cat-add-card">
            <span class="opacity-25 flex justify-center mb-1 text-amber-400"><x-admin.icon name="plus" class="w-8 h-8" /></span>
            <span class="cat-add-label">Nueva categoría</span>
        </div>
    </div>

    <div class="px-1">{{ $categorias->links() }}</div>
</div>

{{-- Modal --}}
<div id="modal-cat" style="display:none;" class="promo-modal-bg">
    <div class="promo-modal" onclick="event.stopPropagation()" style="max-width:420px;">
        <div class="promo-modal-header">
            <div>
                <div class="promo-modal-title" id="modal-cat-title">Nueva Categoría</div>
                <div class="promo-modal-sub" style="color:rgba(255,255,255,0.50);">Visible en el kiosco como botón de filtro</div>
            </div>
            <button onclick="cerrarModalCat()" class="promo-modal-close">✕</button>
        </div>
        <form id="modal-cat-form" action="{{ route('admin.categorias.store') }}" method="POST"
              class="promo-modal-body">
            @csrf
            <input type="hidden" name="_method" id="modal-cat-method" value="POST">

            {{-- Ícono grande --}}
            <div style="text-align:center;position:relative;">
                <div id="cat-icono-preview"
                    onclick="toggleEmojiPicker()"
                    style="font-size:4rem;line-height:1;margin-bottom:0.5rem;cursor:pointer;
                            display:inline-block;padding:0.5rem;border-radius:1rem;
                            transition:background 0.15s ease;"
                    onmouseover="this.style.background='rgba(255,255,255,0.06)'"
                    onmouseout="this.style.background='transparent'"
                    style="display:none;"></div>
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.50);margin-top:-0.25rem;margin-bottom:0.5rem;">
                    Toca para cambiar
                </div>
                {{-- Input oculto que guarda el valor --}}
                <input type="hidden" name="icono" id="cat-icono" value="">
                {{-- Picker de emojis --}}
                <div id="emoji-picker" style="display:none;position:absolute;left:50%;transform:translateX(-50%);
                    z-index:999;background:#1a1f32;border:1px solid rgba(255,255,255,0.1);
                    border-radius:1rem;padding:0.75rem;width:280px;max-height:220px;
                    overflow-y:auto;box-shadow:0 16px 40px rgba(0,0,0,0.6);
                    scrollbar-width:thin;scrollbar-color:rgba(201,168,76,0.3) transparent;">
                    <div style="font-size:0.55rem;font-weight:900;text-transform:uppercase;
                                letter-spacing:0.12em;color:rgba(255,255,255,0.2);
                                margin-bottom:0.5rem;font-family:var(--font-display);">Elige un emoji</div>
                    <div id="emoji-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:0.25rem;">
                    </div>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label" style="color:rgba(255,255,255,0.50);">Nombre *</label>
                <input name="nombre" id="cat-nombre" class="admin-input" placeholder="Ej: Almuerzos" required
                    oninput="validarNombreCat(this.value)">
                <span id="cat-nombre-error"
                    style="display:none;font-size:0.68rem;color:#f87171;font-weight:700;margin-top:0.2rem;">
                    Ya existe una categoría con ese nombre
                </span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                <div class="field-group">
                    <label class="field-label" style="color:rgba(255,255,255,0.50);">Sede</label>
                    <select name="sede_id" id="cat-sede" class="admin-select w-full">
                        <option value="">Ambas</option>
                        @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label" style="color:rgba(255,255,255,0.50);">Orden</label>
                    <input name="orden" type="number" min="0" value="0" id="cat-orden" class="admin-input">
                </div>
            </div>
            <label class="remember-toggle">
                <input type="checkbox" name="activo" id="cat-activo" class="remember-hidden" checked
                       onchange="document.getElementById('cat-activo-track').classList.toggle('checked', this.checked)">
                <div class="remember-track checked" id="cat-activo-track"><div class="remember-thumb"></div></div>
                <span class="remember-label-text">Categoría activa en el kiosco</span>
            </label>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-gold flex-1 justify-center py-3" id="btn-cat-guardar">Crear categoría</button>
                <button type="button" onclick="cerrarModalCat()" class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
.cat-card {
    background:#1a1f32;border:1px solid rgba(255,255,255,0.07);
    border-radius:1rem;padding:1.25rem;display:flex;flex-direction:column;
    gap:0.5rem;transition:transform 0.2s ease,border-color 0.2s ease,box-shadow 0.2s ease;
    position:relative;overflow:hidden;
}
.cat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,rgba(201,168,76,0.3),transparent); }
.cat-card:hover { transform:translateY(-3px);border-color:rgba(201,168,76,0.2);box-shadow:0 12px 30px rgba(0,0,0,0.35); }
.cat-card-off { opacity:.55; }

.cat-card-top { display:flex;justify-content:space-between;align-items:flex-start; }
.cat-icon { font-size:2.5rem;line-height:1;filter:drop-shadow(0 3px 8px rgba(0,0,0,0.4)); }
.cat-badge-estado { font-size:0.7rem;font-weight:900;padding:2px 7px;border-radius:999px; }
.cat-on  { color:#6ee7b7;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25); }
.cat-off { color:#f87171;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2); }

.cat-nombre { font-family:var(--font-display);font-weight:900;font-size:0.88rem;color:white;text-transform:uppercase;letter-spacing:0.04em;line-height:1.2; }
.cat-meta   { font-size:0.65rem;color:rgba(255,255,255,0.50);line-height:1.4; }

.cat-actions { display:flex;gap:0.4rem;margin-top:0.25rem; }
.cat-btn { display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.65rem;border-radius:0.5rem;border:none;cursor:pointer;font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;transition:all 0.15s ease;font-family:var(--font-display); }
.cat-btn-edit { background:rgba(99,130,246,0.1);color:#a5b4fc;border:1px solid rgba(99,130,246,0.2);flex:1;justify-content:center; }
.cat-btn-edit:hover { background:rgba(99,130,246,0.2); }
.cat-btn-del  { background:rgba(239,68,68,0.08);color:#f87171;border:1px solid rgba(239,68,68,0.15); }
.cat-btn-del:hover { background:rgba(239,68,68,0.18); }

.cat-add-card {
    border-radius:1rem;border:2px dashed rgba(255,255,255,0.07);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:0.6rem;cursor:pointer;min-height:160px;
    transition:all 0.2s ease;background:rgba(255,255,255,0.01);
}
.cat-add-card:hover { border-color:rgba(201,168,76,0.3);background:rgba(201,168,76,0.03); }
.cat-add-label { font-size:0.68rem;font-weight:800;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.50);font-family:var(--font-display); }

/* ── Modal overlay — copiado de promociones ya que no está en app.css ── */
.promo-modal-bg {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(8px);
    display: flex;              
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}
.promo-modal {
    width: 100%;
    max-width: 420px;
    max-height: 90vh;
    overflow-y: auto;
    background: #141825;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1.25rem;
    box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
}
@keyframes modalIn {
    from { opacity:0; transform:scale(0.94); }
    to   { opacity:1; transform:scale(1); }
}
.promo-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.promo-modal-title {
    font-family: var(--font-display);
    font-weight: 900;
    font-size: 1rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.promo-modal-sub {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.25);
    margin-top: 2px;
}
.promo-modal-close {
    background: transparent;
    border: none;
    cursor: pointer;
    color: rgba(255,255,255,0.3);
    font-size: 1.1rem;
    padding: 0.25rem;
    transition: color 0.15s ease;
}
.promo-modal-close:hover { color: white; }
.promo-modal-body {
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.field-label {
    font-size: 0.6rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: rgba(255,255,255,0.25);
    font-family: var(--font-display);
}
/* Toggle del checkbox */
.remember-toggle {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    cursor: pointer;
}
.remember-hidden { display: none; }
.remember-track {
    position: relative;
    width: 2.5rem;
    height: 1.4rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.25s ease;
    flex-shrink: 0;
}
.remember-track.checked {
    background: rgba(16,185,129,0.25);
    border-color: rgba(16,185,129,0.4);
}
.remember-thumb {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: rgba(255,255,255,0.35);
    transition: all 0.25s ease;
}
.remember-track.checked .remember-thumb {
    transform: translateX(1.1rem);
    background: #6ee7b7;
}
.remember-label-text {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.5);
    font-weight: 600;
}
</style>

@push('scripts')
<script>
// Orden máximo actual para autoincremento
const maxOrden = {{ $maxOrden }};

function abrirModalCat() {
    document.getElementById('modal-cat-title').textContent = 'Nueva Categoría';
    document.getElementById('btn-cat-guardar').textContent = 'Crear categoría';
    document.getElementById('modal-cat-form').action = '{{ route("admin.categorias.store") }}';
    document.getElementById('modal-cat-method').value = 'POST';
    document.getElementById('cat-nombre').value = '';
    _catEditId = null;
    _catNombreOriginal = null;
    document.getElementById('cat-icono').value  = '';
    document.getElementById('cat-orden').value  = maxOrden + 1; // ← orden automático
    document.getElementById('cat-sede').value   = '';
    document.getElementById('cat-activo').checked = true;
    document.getElementById('cat-activo-track').classList.add('checked');
    
    document.getElementById('modal-cat').style.display = 'flex';
}

function editarCat(id, nombre, icono, activo, orden, sedeId) {
    document.getElementById('modal-cat-title').textContent = 'Editar Categoría';
    document.getElementById('btn-cat-guardar').textContent = 'Guardar cambios';
    document.getElementById('modal-cat-form').action = `/admin/categorias/${id}`;
    document.getElementById('modal-cat-method').value = 'PUT';
    document.getElementById('cat-nombre').value = nombre;
    _catEditId = id;
    _catNombreOriginal = nombre.toLowerCase().trim();
    document.getElementById('cat-icono').value  = icono;
    document.getElementById('cat-orden').value  = orden;
    document.getElementById('cat-sede').value   = sedeId || '';
    document.getElementById('cat-activo').checked = activo === 1;
    document.getElementById('cat-activo-track').classList.toggle('checked', activo === 1);
    
    document.getElementById('modal-cat').style.display = 'flex';
    document.getElementById('emoji-picker').style.display = 'none';
}

function cerrarModalCat() {
    document.getElementById('modal-cat').style.display = 'none';
}

// Cerrar al hacer clic en el fondo (no en el contenido)
document.getElementById('modal-cat').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalCat();
});

// ── Emojis disponibles para categorías ──
const EMOJIS_CAT = [];

function buildEmojiGrid() {
    const grid = document.getElementById('emoji-grid');
    if (grid.children.length > 0) return; // ya construido
    EMOJIS_CAT.forEach(emoji => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = emoji;
        btn.title = emoji;
        btn.style.cssText = `
            font-size:1.4rem;padding:0.3rem;border-radius:0.4rem;
            border:none;background:transparent;cursor:pointer;
            transition:background 0.1s ease;line-height:1;
        `;
        btn.onmouseover = () => btn.style.background = 'rgba(255,255,255,0.08)';
        btn.onmouseout  = () => btn.style.background = 'transparent';
        btn.onclick = () => seleccionarEmoji(emoji);
        grid.appendChild(btn);
    });
}

function toggleEmojiPicker() {
    const picker = document.getElementById('emoji-picker');
    const visible = picker.style.display !== 'none';
    picker.style.display = visible ? 'none' : 'block';
    if (!visible) buildEmojiGrid();
}

function seleccionarEmoji(emoji) {
    document.getElementById('cat-icono-preview').textContent = emoji;
    document.getElementById('cat-icono').value = emoji;
    document.getElementById('emoji-picker').style.display = 'none';
}

// Cerrar picker al hacer clic fuera
document.addEventListener('click', function(e) {
    const picker  = document.getElementById('emoji-picker');
    const preview = document.getElementById('cat-icono-preview');
    if (picker && !picker.contains(e.target) && e.target !== preview) {
        picker.style.display = 'none';
    }
});

// Lista de nombres existentes para validación en tiempo real
const NOMBRES_EXISTENTES = @json($categorias->pluck('nombre'));
let _catEditId = null;
let _catNombreOriginal = null;

function validarNombreCat(valor) {
    const error = document.getElementById('cat-nombre-error');
    const input = document.getElementById('cat-nombre');
    const btn   = document.getElementById('btn-cat-guardar');
    const normalizado = valor.toLowerCase().trim();
    // Al editar, excluir el nombre original de la validación
    const esDuplicado = NOMBRES_EXISTENTES.some(
        n => n.toLowerCase().trim() === normalizado &&
             normalizado !== _catNombreOriginal
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

/* ── Auto-actualización: si algo cambia desde otro lugar, esta pantalla
   se refresca sola. No interrumpe si hay un modal abierto. ── */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-cat')?.style.display === 'flex';
    if (!modalAbierto) {
        location.reload();
    }
});
</script>
<script src="{{ asset('js/kiosco-realtime.js') }}"></script>
@endpush
@endsection