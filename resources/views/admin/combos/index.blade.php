@extends('layouts.app')
@section('title','Combos')
@section('page-icon')
<x-admin.icon name="combos" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Gestión de Combos')
@section('page-subtitle','Combos del menú · Visibles en el kiosco bajo la categoría "Combos"')
@section('header-actions')
    <button onclick="abrirNuevoCombo()" class="btn-gold">+ Nuevo combo</button>
@endsection

@section('content')
<div class="space-y-5 pt-2">

    {{-- Info --}}
    <div class="admin-card p-4 flex gap-3 items-center"
         style="background:linear-gradient(135deg,rgba(201,168,76,0.08),rgba(201,168,76,0.03));border-color:rgba(201,168,76,0.18);">
        <span class="flex items-center justify-center text-amber-400"><x-admin.icon name="star" class="w-6 h-6" /></span>
        <div class="flex-1">
            <div class="text-sm font-bold text-white mb-0.5">Combos del menú</div>
            <div class="text-xs" style="color:rgba(255,255,255,0.50);">
                Los combos aparecen en el kiosco al seleccionar la categoría <strong class="text-white">Combos</strong> o en <strong class="text-white">Todos</strong>. Un combo se desactiva automáticamente si algún producto está agotado.
            </div>
        </div>
        <div class="flex-shrink-0 text-right">
            <div class="text-2xl font-black text-white" style="font-family:var(--font-display)">{{ $combos->where('activo',true)->count() }}</div>
            <div class="text-xs uppercase tracking-wider font-bold" style="color:rgba(201,168,76,0.5);">activos</div>
        </div>
    </div>

    {{-- Grid combos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($combos as $combo)
        <div class="combo-card {{ $combo->disponible && $combo->activo ? '' : 'combo-card-off' }}">
            <div class="combo-img-wrap">
                <img src="{{ $combo->imagen_url }}" alt="{{ $combo->nombre }}" class="combo-img">
                <div class="combo-img-overlay"></div>
                <div class="combo-price">${{ number_format($combo->precio, 2) }}</div>
                <div class="combo-state-badge {{ $combo->activo ? 'combo-state-on' : 'combo-state-off' }}">
                    {{ $combo->activo ? 'Activo' : 'Inactivo' }}
                </div>
                @if($combo->sede)
                <div class="combo-sede-badge">{{ $combo->sede->slug === 'instituto' ? 'Instituto' : 'Conducción' }}</div>
                @endif
            </div>
            <div class="combo-body">
                <div class="combo-nombre">{{ $combo->nombre }}</div>
                @if($combo->descripcion)
                <div class="combo-desc" style="color:rgba(255,255,255,0.50);">{{ $combo->descripcion }}</div>
                @endif
                <div class="combo-items">
                    @foreach($combo->items as $item)
                    <div class="combo-item {{ !$item->producto?->disponible ? 'combo-item-out' : '' }}">
                        <span class="combo-item-dot" style="background:{{ $item->producto?->disponible ? '#c9a84c' : '#f87171' }};"></span>
                        <span>{{ $item->cantidad }}× {{ $item->producto?->nombre ?? '—' }}</span>
                        @if(!$item->producto?->disponible)
                        <span class="badge badge-danger" style="font-size:0.55rem;padding:1px 5px;">agotado</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if(!$combo->disponible && $combo->activo)
                <div class="combo-alert">No disponible — algún producto está agotado</div>
                @endif
            </div>
            <div class="combo-actions">
                <button class="btn-blue flex-1 justify-center py-2 text-xs"
                    onclick="abrirEditarCombo(
                        {{ $combo->id }},
                        '{{ addslashes($combo->nombre) }}',
                        '{{ addslashes($combo->descripcion ?? '') }}',
                        '{{ $combo->precio }}',
                        {{ $combo->activo ? 'true' : 'false' }},
                        {{ $combo->items->map(fn($i)=>['id'=>$i->producto_id,'qty'=>$i->cantidad])->toJson() }},
                        '{{ $combo->imagen_url }}'
                    )">Editar</button>
                <form method="POST" action="{{ route('admin.combos.destroy', $combo) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger py-2 px-3 text-xs"
                            onclick="return confirm('¿Eliminar «{{ $combo->nombre }}»?')"></button>
                </form>
            </div>
        </div>
        @empty
        <div class="admin-card p-16 col-span-3 text-center">
            <div class="flex justify-center opacity-10 mb-4 text-amber-400"><x-admin.icon name="combos" class="w-16 h-16" /></div>
            <div class="font-display font-black text-lg uppercase tracking-wider mb-2" style="color:rgba(255,255,255,0.2);">Sin combos</div>
            <div class="text-sm mb-6" style="color:rgba(255,255,255,0.18);">Crea tu primer combo para que aparezca en el kiosco.</div>
            <button onclick="abrirNuevoCombo()" class="btn-gold">+ Crear primer combo</button>
        </div>
        @endforelse
    </div>

    <div>{{ $combos->links() }}</div>
</div>

{{-- ══════════════ MODAL COMBO ══════════════ --}}
<div id="modal-combo" class="ist-modal-bg" style="display:none;" onclick="if(event.target===this)cerrarModalCombo()">
    <div class="ist-modal" onclick="event.stopPropagation()" style="max-width:520px;">

        {{-- Header sticky --}}
        <div class="ist-modal-header">
            <div>
                <div class="ist-modal-title" id="combo-modal-title">Nuevo Combo</div>
                <div class="ist-modal-sub">Define los productos que conforman el combo</div>
            </div>
            <button onclick="cerrarModalCombo()" class="ist-modal-close">✕</button>
        </div>

        <form id="form-combo" method="POST" enctype="multipart/form-data" class="ist-modal-body">
            @csrf
            <input type="hidden" name="_method" id="combo-method" value="POST">

            {{-- Preview imagen en tiempo real --}}
            <div class="img-upload-wrap" onclick="document.getElementById('combo-imagen').click()">
                <img id="combo-img-preview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                <div id="combo-img-placeholder">
                    <span style="font-size:2rem;opacity:.3;"></span>
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.50);margin-top:0.4rem;text-align:center;">
                        Clic para subir imagen del combo<br>
                        <span style="font-size:0.65rem;opacity:.7; color:rgba(255,255,255,0.50);">JPG, PNG, WEBP</span>
                    </span>
                </div>
                <input type="file" id="combo-imagen" name="imagen" accept="image/*"
                       class="hidden" onchange="previewComboImg(this)">
            </div>

            <div class="ist-modal-fields">
                {{-- Nombre --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Nombre del combo *</label>
                    <input name="nombre" id="combo-nombre" class="admin-input"
                           placeholder="Ej: Combo Clásico" required
                           oninput="validarNombreComboModal(this.value)">
                    <span id="combo-nombre-error" class="ist-field-error" style="display:none;">
                        Ya existe un combo con ese nombre
                    </span>
                </div>

                {{-- Descripción --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Descripción</label>
                    <textarea name="descripcion" id="combo-desc" rows="2"
                              class="admin-input resize-none"
                              placeholder="Ej: Papas + Salchipapa + Cola"></textarea>
                </div>

                {{-- Precio + Toggle activo --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;align-items:end;">
                    <div class="ist-field-group">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Precio ($) *</label>
                            <label class="ist-toggle" style="gap:0.4rem;">
                                <input type="checkbox" id="combo-precio-auto"
                                       class="ist-toggle-hidden" checked
                                       onchange="toggleComboPrecioAuto(this.checked)">
                                <div class="ist-toggle-track on" id="combo-precio-auto-track" style="width:2.1rem;height:1.15rem;">
                                    <div class="ist-toggle-thumb" style="width:0.85rem;height:0.85rem;"></div>
                                </div>
                                <span class="ist-toggle-label" style="font-size:0.6rem;white-space:nowrap;">Auto</span>
                            </label>
                        </div>
                        <input name="precio" id="combo-precio" type="number" step="0.01" min="0.05" max="999.99"
                               class="admin-input" placeholder="0.00" required readonly
                               onkeydown="return !['e','E','+','-'].includes(event.key)"
                               oninput="if(!document.getElementById('combo-precio-auto').checked) marcarPrecioManual();">
                        <span id="combo-precio-hint" style="font-size:0.62rem;color:rgba(110,231,183,0.8);font-weight:700;">
                            Precio calculado automáticamente
                        </span>
                    </div>
                    <div class="ist-field-group" style="padding-bottom:0.2rem;">
                        <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Estado</label>
                        <label class="ist-toggle">
                            <input type="checkbox" name="activo" id="combo-activo"
                                   class="ist-toggle-hidden" checked
                                   onchange="document.getElementById('combo-activo-track').classList.toggle('on', this.checked)">
                            <div class="ist-toggle-track on" id="combo-activo-track">
                                <div class="ist-toggle-thumb"></div>
                            </div>
                            <span class="ist-toggle-label">Activo en kiosco</span>
                        </label>
                    </div>
                </div>

                {{-- Productos del combo --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Productos que incluye *</label>
                    <div id="combo-items-container" style="display:flex;flex-direction:column;gap:0.5rem;"></div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:0.5rem;">
                        <button type="button" onclick="agregarItemCombo()" id="btn-agregar-item"
                                class="btn-ghost text-xs py-2" style="flex:1;justify-content:center;">
                            + Agregar producto
                        </button>
                        <span style="font-size:0.62rem;color:rgba(255,255,255,0.50);margin-left:0.75rem;white-space:nowrap;">Máximo 4</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-gold flex-1 justify-center py-3" id="btn-combo-guardar">
                    Crear combo
                </button>
                <button type="button" onclick="cerrarModalCombo()"
                        class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
/* ── Cards combo ── */
.combo-card { background:#111827;border:1px solid rgba(255,255,255,0.07);border-radius:1.1rem;overflow:hidden;display:flex;flex-direction:column;transition:transform 0.2s ease,box-shadow 0.2s ease,border-color 0.2s ease; }
.combo-card:hover { transform:translateY(-4px);box-shadow:0 18px 42px rgba(0,0,0,0.4);border-color:rgba(201,168,76,0.2); }
.combo-card-off { opacity:.6; }
.combo-img-wrap { aspect-ratio:16/9;position:relative;overflow:hidden;background:#0d1228; }
.combo-img { width:100%;height:100%;object-fit:cover;transition:transform 0.35s ease; }
.combo-card:hover .combo-img { transform:scale(1.05); }
.combo-img-overlay { position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,0.7) 100%); }
.combo-price { position:absolute;bottom:0.6rem;left:0.85rem;font-family:var(--font-display);font-weight:900;font-size:1.6rem;color:#c9a84c;line-height:1;text-shadow:0 2px 12px rgba(0,0,0,0.6); }
.combo-state-badge { position:absolute;top:0.6rem;right:0.6rem;padding:0.22rem 0.65rem;border-radius:999px;font-size:0.6rem;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;backdrop-filter:blur(8px);font-family:var(--font-display); }
.combo-state-on  { background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.35);color:#6ee7b7; }
.combo-state-off { background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#fca5a5; }
.combo-sede-badge { position:absolute;top:0.6rem;left:0.6rem;width:26px;height:26px;border-radius:50%;background:rgba(0,0,0,0.5);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;font-size:0.75rem; }
.combo-body { padding:1rem 1rem 0.6rem;flex:1;display:flex;flex-direction:column;gap:0.5rem; }
.combo-nombre { font-family:var(--font-display);font-weight:900;font-size:0.9rem;color:white;text-transform:uppercase;letter-spacing:0.03em;line-height:1.2; }
.combo-desc   { font-size:0.72rem;color:rgba(255,255,255,0.3);line-height:1.5; }
.combo-items  { display:flex;flex-direction:column;gap:0.3rem;margin-top:0.25rem; }
.combo-item   { display:flex;align-items:center;gap:0.5rem;font-size:0.75rem;color:rgba(255,255,255,0.55); }
.combo-item-out { color:rgba(248,113,113,0.6); }
.combo-item-dot { width:5px;height:5px;border-radius:50%;flex-shrink:0; }
.combo-alert  { padding:0.45rem 0.75rem;border-radius:0.5rem;margin-top:0.35rem;background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);font-size:0.7rem;color:#fca5a5; }
.combo-actions { display:flex;gap:0.5rem;padding:0.65rem 0.85rem;border-top:1px solid rgba(255,255,255,0.05); }

/* ── Modal sistema unificado (mismo que productos) ── */
.ist-modal-bg {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center; padding: 1.5rem;
}
.ist-modal {
    width: 100%; max-height: 90vh; overflow-y: auto;
    background: #141825; border: 1px solid rgba(255,255,255,0.08);
    border-radius: 1.25rem; box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    animation: istModalIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
    scrollbar-width: thin; scrollbar-color: rgba(201,168,76,0.2) transparent;
}
@keyframes istModalIn { from{opacity:0;transform:scale(0.94)} to{opacity:1;transform:scale(1)} }
.ist-modal-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06);
    position: sticky; top: 0; background: #141825; z-index: 2;
}
.ist-modal-title { font-family:var(--font-display);font-weight:900;font-size:1rem;color:white;text-transform:uppercase;letter-spacing:0.04em; }
.ist-modal-sub   { font-size:0.72rem;color:rgba(255,255,255,0.25);margin-top:2px; }
.ist-modal-close { background:transparent;border:none;cursor:pointer;color:rgba(255,255,255,0.3);font-size:1.1rem;padding:0.25rem;transition:color 0.15s ease;line-height:1; }
.ist-modal-close:hover { color:white; }
.ist-modal-body   { padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:1rem; }
.ist-modal-fields { display:flex;flex-direction:column;gap:0.85rem; }
.ist-field-group  { display:flex;flex-direction:column;gap:0.3rem; }
.ist-field-label  { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.25);font-family:var(--font-display); }
.ist-field-error  { font-size:0.68rem;color:#f87171;font-weight:700;margin-top:0.2rem; }

/* ── Preview imagen ── */
.img-upload-wrap {
    width: 100%; aspect-ratio: 16/9; border-radius: 0.75rem; overflow: hidden;
    border: 2px dashed rgba(255,255,255,0.1); cursor: pointer;
    background: #0d1228; position: relative;
    display: flex; align-items: center; justify-content: center;
    transition: border-color 0.2s ease;
}
.img-upload-wrap:hover { border-color: rgba(201,168,76,0.35); }
#combo-img-placeholder { display:flex;flex-direction:column;align-items:center;gap:0.5rem;pointer-events:none; }

/* ── Toggle pequeño (auto-precio) ── */
#combo-precio-auto-track.on .ist-toggle-thumb { transform: translateX(0.95rem) !important; }
.ist-toggle { display:flex;align-items:center;gap:0.65rem;cursor:pointer; }
.ist-toggle-hidden { display:none; }
.ist-toggle-track { position:relative;width:2.75rem;height:1.5rem;border-radius:999px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);transition:all 0.25s ease;flex-shrink:0; }
.ist-toggle-track.on { background:rgba(16,185,129,0.2);border-color:rgba(16,185,129,0.4);box-shadow:0 0 12px rgba(16,185,129,0.15); }
.ist-toggle-thumb { position:absolute;top:3px;left:3px;width:1.05rem;height:1.05rem;border-radius:50%;background:rgba(255,255,255,0.3);transition:all 0.25s ease; }
.ist-toggle-track.on .ist-toggle-thumb { transform:translateX(1.25rem);background:#6ee7b7; }
.ist-toggle-label { font-size:0.78rem;color:rgba(255,255,255,0.5);font-weight:600; }

@media (max-width: 767px) {
    .ist-modal-bg { padding: 0.75rem; }
    .ist-modal-body, .ist-modal-header { padding-left:1rem;padding-right:1rem; }
}
</style>

@push('scripts')
<script>
const STORE_COMBO_ROUTE  = '{{ route("admin.combos.store") }}';
const NOMBRES_COMBOS     = @json($todosLosCombos->pluck('nombre'));
const PRODUCTOS_LISTA    = @json($productos->map(fn($p) => ['id'=>$p->id,'nombre'=>$p->nombre,'precio'=>$p->precio]));
let _comboNombreOriginal = null;
let _comboEditId         = null;

/* ── Preview imagen ── */
function previewComboImg(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('combo-img-preview');
        const ph  = document.getElementById('combo-img-placeholder');
        img.src = e.target.result; img.style.display = 'block'; ph.style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

function resetComboImgPreview(src) {
    const img = document.getElementById('combo-img-preview');
    const ph  = document.getElementById('combo-img-placeholder');
    if (src && src !== '') {
        img.src = src; img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; img.src = ''; ph.style.display = 'flex';
    }
}

/* ── Precio automático del combo ── */
function calcularPrecioComboAuto() {
    const auto = document.getElementById('combo-precio-auto').checked;
    if (!auto) return;
    let total = 0;
    document.querySelectorAll('#combo-items-container > div').forEach(row => {
        const select = row.querySelector('select[name="productos[]"]');
        const cantInput = row.querySelector('input[name="cantidades[]"]');
        const cantidad = parseFloat(cantInput?.value) || 0;
        const opt = select?.selectedOptions?.[0];
        const precioUnit = opt ? parseFloat(opt.dataset.precio) || 0 : 0;
        total += precioUnit * cantidad;
    });
    document.getElementById('combo-precio').value = total.toFixed(2);
}

function toggleComboPrecioAuto(activo) {
    const track = document.getElementById('combo-precio-auto-track');
    const input = document.getElementById('combo-precio');
    const hint  = document.getElementById('combo-precio-hint');
    track.classList.toggle('on', activo);
    if (activo) {
        input.readOnly = true;
        hint.style.display = '';
        hint.textContent = 'Precio calculado automáticamente';
        hint.style.color = 'rgba(110,231,183,0.8)';
        calcularPrecioComboAuto();
    } else {
        input.readOnly = false;
        hint.textContent = 'Ingresa el precio del combo';
        hint.style.color = 'rgba(255,255,255,0.35)';
        input.focus();
    }
}

function marcarPrecioManual() { /* el precio queda como lo escribió el usuario */ }

/* ── Validación nombre ── */
function validarNombreComboModal(valor) {
    const error = document.getElementById('combo-nombre-error');
    const input = document.getElementById('combo-nombre');
    const btn   = document.getElementById('btn-combo-guardar');
    const norm  = valor.toLowerCase().trim();
    const dup   = NOMBRES_COMBOS.some(n => n.toLowerCase().trim() === norm && norm !== _comboNombreOriginal);
    if (dup && norm !== '') {
        error.style.display = 'block';
        input.style.borderColor = 'rgba(248,113,113,0.5)'; input.style.boxShadow = '0 0 0 3px rgba(248,113,113,0.08)';
        btn.disabled = true; btn.style.opacity = '0.45'; btn.style.cursor = 'not-allowed';
    } else {
        error.style.display = 'none'; input.style.borderColor = ''; input.style.boxShadow = '';
        btn.disabled = false; btn.style.opacity = ''; btn.style.cursor = '';
    }
}

/* ── Items del combo ── */
function buildProductoSelect(selectedId) {
    let opts = '<option value="">Selecciona un producto</option>';
    PRODUCTOS_LISTA.forEach(p => {
        opts += `<option value="${p.id}" data-precio="${p.precio}" ${p.id == selectedId ? 'selected' : ''}>${p.nombre} — $${parseFloat(p.precio).toFixed(2)}</option>`;
    });
    return opts;
}

function getSelectedProductIds() {
    return Array.from(document.querySelectorAll('#combo-items-container select[name="productos[]"]'))
                .map(s => s.value).filter(v => v !== '');
}

function refreshAllSelects() {
    const used = getSelectedProductIds();
    document.querySelectorAll('#combo-items-container select[name="productos[]"]').forEach(sel => {
        const own = sel.value;
        Array.from(sel.options).forEach(opt => {
            if (opt.value === '') return;
            opt.disabled = used.includes(opt.value) && opt.value !== own;
        });
    });
    const count = document.querySelectorAll('#combo-items-container > div').length;
    const btn = document.getElementById('btn-agregar-item');
    if (btn) btn.style.display = count >= 4 ? 'none' : '';
    calcularPrecioComboAuto();
}

function agregarItemCombo(productoId = '', cantidad = 1) {
    const container = document.getElementById('combo-items-container');
    if (container.querySelectorAll(':scope > div').length >= 4) return;
    const row = document.createElement('div');
    row.style.cssText = 'display:flex;gap:0.5rem;align-items:center;';
    row.innerHTML = `
        <select name="productos[]" class="admin-select flex-1" required onchange="refreshAllSelects()">
            ${buildProductoSelect(productoId)}
        </select>
        <input name="cantidades[]" type="number" min="1" value="${cantidad}"
               class="admin-input" style="width:64px;text-align:center;" placeholder="Cant."
               oninput="calcularPrecioComboAuto()">
        <button type="button" onclick="this.closest('div').remove();refreshAllSelects();"
                class="btn-danger py-2 px-2 flex-shrink-0" style="font-size:0.75rem;">✕</button>
    `;
    container.appendChild(row);
    refreshAllSelects();
}

function _resetComboModal() {
    document.getElementById('combo-nombre-error').style.display = 'none';
    document.getElementById('combo-nombre').style.borderColor = '';
    document.getElementById('combo-nombre').style.boxShadow = '';
    document.getElementById('btn-combo-guardar').disabled = false;
    document.getElementById('btn-combo-guardar').style.opacity = '';
    document.getElementById('btn-combo-guardar').style.cursor = '';
    document.getElementById('combo-imagen').value = '';
}

function abrirNuevoCombo() {
    _comboEditId = null; _comboNombreOriginal = null;
    document.getElementById('combo-modal-title').textContent = 'Nuevo Combo';
    document.getElementById('btn-combo-guardar').textContent = 'Crear combo';
    document.getElementById('form-combo').action = STORE_COMBO_ROUTE;
    document.getElementById('combo-method').value = 'POST';
    document.getElementById('combo-nombre').value = '';
    document.getElementById('combo-desc').value = '';
    document.getElementById('combo-precio').value = '';
    document.getElementById('combo-activo').checked = true;
    document.getElementById('combo-activo-track').classList.add('on');
    document.getElementById('combo-precio-auto').checked = true;
    document.getElementById('combo-items-container').innerHTML = '';
    agregarItemCombo();
    toggleComboPrecioAuto(true);
    resetComboImgPreview(null);
    _resetComboModal();
    document.getElementById('modal-combo').style.display = 'flex';
}

function abrirEditarCombo(id, nombre, descripcion, precio, activo, items, imagenUrl) {
    _comboEditId = id; _comboNombreOriginal = nombre.toLowerCase().trim();
    document.getElementById('combo-modal-title').textContent = 'Editar Combo';
    document.getElementById('btn-combo-guardar').textContent = 'Guardar cambios';
    document.getElementById('form-combo').action = `/admin/combos/${id}`;
    document.getElementById('combo-method').value = 'PUT';
    document.getElementById('combo-nombre').value = nombre;
    document.getElementById('combo-desc').value = descripcion;
    document.getElementById('combo-activo').checked = activo;
    document.getElementById('combo-activo-track').classList.toggle('on', activo);
    document.getElementById('combo-items-container').innerHTML = '';
    (items && items.length ? items : [{}]).forEach(item => agregarItemCombo(item.id || '', item.qty || 1));
    document.getElementById('combo-precio-auto').checked = false;
    toggleComboPrecioAuto(false);
    document.getElementById('combo-precio').value = precio;
    refreshAllSelects();
    resetComboImgPreview(imagenUrl || null);
    _resetComboModal();
    document.getElementById('modal-combo').style.display = 'flex';
}

function cerrarModalCombo() {
    document.getElementById('modal-combo').style.display = 'none';
}

/* ── Auto-actualización: si algo cambia desde otro lugar (productos,
   stock, etc.), esta pantalla se refresca sola. No interrumpe si hay
   un modal abierto (crear/editar combo). ── */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-combo')?.style.display === 'flex';
    if (!modalAbierto) {
        location.reload();
    }
});
</script>
<script src="{{ asset('js/kiosco-realtime.js') }}"></script>
@endpush
@endsection