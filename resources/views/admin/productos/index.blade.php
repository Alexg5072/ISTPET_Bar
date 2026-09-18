@extends('layouts.app')
@section('title','Productos')
@section('page-icon')
<x-admin.icon name="productos" class="w-5 h-5 text-amber-400" />
@endsection
@section('page-title','Productos')
@section('page-subtitle','Gestiona precios · Activa o desactiva en tiempo real')
@section('header-actions')
    <button onclick="abrirNuevoProducto()" class="btn-gold">+ Nuevo producto</button>
@endsection

@section('content')
<div class="space-y-5 pt-2">

    {{-- Filtros --}}
    <form method="GET" class="admin-card p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="field-label" style="display:block;margin-bottom:0.3rem;">Buscar</label>
            <input name="buscar" value="{{ request('buscar') }}" class="admin-input" placeholder="Nombre del producto...">
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="field-label" style="display:block;margin-bottom:0.3rem;">Categoría</label>
            <select name="categoria" class="admin-select w-full">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icono }} {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="field-label" style="display:block;margin-bottom:0.3rem;">Estado</label>
            <select name="stock" class="admin-select w-full">
                <option value="">Todos</option>
                <option value="true"  {{ request('stock') === 'true'  ? 'selected' : '' }}>Disponibles</option>
                <option value="false" {{ request('stock') === 'false' ? 'selected' : '' }}>Agotados</option>
            </select>
        </div>
        <button type="submit" class="btn-primary py-2">Filtrar</button>
        <a href="{{ route('admin.productos.index') }}" class="btn-ghost py-2">Limpiar</a>
    </form>

    {{-- Resumen rápido --}}
    @php
        $total    = $productos->total();
        $activos  = $productos->getCollection()->where('activo', true)->count();
        $agotados = $productos->getCollection()->filter(fn($p) => $p->stock_activo && $p->stock_actual <= 0)->count();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="admin-card p-4 flex items-center gap-3">
            <div style="width:2.5rem;height:2.5rem;border-radius:0.6rem;background:rgba(201,168,76,0.1);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;color:#c9a84c;"><x-admin.icon name="productos" class="w-5 h-5" /></div>
            <div>
                <div style="font-family:var(--font-display);font-weight:900;font-size:1.5rem;color:white;line-height:1;">{{ $total }}</div>
                <div style="font-size:0.65rem;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Total productos</div>
            </div>
        </div>
        <div class="admin-card p-4 flex items-center gap-3">
            <div style="width:2.5rem;height:2.5rem;border-radius:0.6rem;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;color:#10b981;"><x-admin.icon name="check-circle" class="w-5 h-5" /></div>
            <div>
                <div style="font-family:var(--font-display);font-weight:900;font-size:1.5rem;color:#6ee7b7;line-height:1;">{{ $activos }}</div>
                <div style="font-size:0.65rem;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Activos</div>
            </div>
        </div>
        <div class="admin-card p-4 flex items-center gap-3">
            <div style="width:2.5rem;height:2.5rem;border-radius:0.6rem;background:rgba(239,68,68,0.1);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;color:#ef4444;"><x-admin.icon name="ban" class="w-5 h-5" /></div>
            <div>
                <div style="font-family:var(--font-display);font-weight:900;font-size:1.5rem;color:{{ $agotados > 0 ? '#f87171' : 'rgba(255,255,255,0.4)' }};line-height:1;">{{ $agotados }}</div>
                <div style="font-size:0.65rem;color:rgba(255,255,255,0.50);text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Sin stock</div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Catálogo — {{ $productos->total() }} productos</div>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="color:rgba(255,255,255,0.50)">Producto</th>
                        <th style="color:rgba(255,255,255,0.50)">Categoría</th>
                        <th style="color:rgba(255,255,255,0.50)">Precio</th>
                        <th style="color:rgba(255,255,255,0.50)">Stock</th>
                        <th style="color:rgba(255,255,255,0.50)">Disponible</th>
                        <th style="color:rgba(255,255,255,0.50)">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr class="{{ !$producto->stock_activo || $producto->stock_actual <= 0 ? 'prod-row-off' : '' }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="prod-thumb">
                                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="prod-img">
                                </div>
                                <div>
                                    <div class="prod-nombre">{{ $producto->nombre }}</div>
                                    <div class="prod-desc">{{ Str::limit($producto->descripcion, 45) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-gray">
                                {{ $producto->categoria?->icono }} {{ $producto->categoria?->nombre ?? '—' }}
                            </span>
                        </td>
                        <td class="col-price">${{ number_format($producto->precio, 2) }}</td>
                        <td>
                            <div class="stock-chip {{ $producto->stock_actual <= $producto->stock_minimo ? 'stock-low' : 'stock-ok' }}">
                                {{ $producto->stock_actual }}
                                @if($producto->stock_actual <= $producto->stock_minimo && $producto->stock_actual > 0)
                                    <span class="stock-warn">▲ bajo</span>
                                @elseif($producto->stock_actual <= 0)
                                    <span class="stock-warn">agotado</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.productos.toggle-stock', $producto) }}">
                                @csrf @method('PATCH')
                                <label class="toggle-wrapper">
                                    <input type="checkbox" class="toggle-input"
                                           {{ $producto->stock_activo ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button class="btn-blue"
                                    onclick="abrirEditarProducto(
                                        {{ $producto->id }},
                                        '{{ addslashes($producto->nombre) }}',
                                        {{ $producto->categoria_id ?? 'null' }},
                                        '{{ addslashes($producto->descripcion ?? '') }}',
                                        '{{ $producto->precio }}',
                                        '{{ $producto->imagen_url }}'
                                    )"><x-admin.icon name="edit" class="w-3.5 h-3.5" /></button>
                                <form method="POST" action="{{ route('admin.productos.destroy', $producto) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger"
                                            onclick="return confirm('¿Eliminar «{{ $producto->nombre }}»?')"><x-admin.icon name="trash" class="w-3.5 h-3.5" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-16" style="color:rgba(255,255,255,0.18);">Sin productos con estos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-white/[0.07]">
            {{ $productos->withQueryString()->links() }}
        </div>
    </div>

</div>

{{-- ══════════════ MODAL PRODUCTO ══════════════ --}}
<div id="modal-producto" class="ist-modal-bg" style="display:none;" onclick="if(event.target===this)cerrarModalProducto()">
    <div class="ist-modal" onclick="event.stopPropagation()" style="max-width:500px;">

        {{-- Header --}}
        <div class="ist-modal-header">
            <div>
                <div class="ist-modal-title" id="prod-modal-title">Nuevo Producto</div>
                <div class="ist-modal-sub">Completa los datos del producto</div>
            </div>
            <button onclick="cerrarModalProducto()" class="ist-modal-close">✕</button>
        </div>

        <form id="form-producto" method="POST" enctype="multipart/form-data" class="ist-modal-body">
            @csrf
            <input type="hidden" name="_method" id="prod-method" value="POST">

            {{-- Preview imagen en tiempo real --}}
            <div class="img-upload-wrap" onclick="document.getElementById('prod-imagen').click()">
                <img id="prod-img-preview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                <div id="prod-img-placeholder">
                    <span style="font-size:2rem;opacity:.3;"></span>
                    <span style="font-size:0.72rem;color:rgba(255,255,255,0.50);margin-top:0.4rem;text-align:center;">
                        Clic para subir imagen<br>
                        <span style="font-size:0.65rem;opacity:.7; color:rgba(255,255,255,0.50);">JPG, PNG, WEBP · Máx. 2MB</span>
                    </span>
                </div>
                <input type="file" id="prod-imagen" name="imagen" accept="image/jpeg,image/png,image/webp"
                       class="hidden" onchange="previewProdImg(this)">
            </div>

            <div class="ist-modal-fields">
                {{-- Nombre --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Nombre *</label>
                    <input name="nombre" id="prod-nombre" class="admin-input"
                           placeholder="Ej: Almuerzo del Día" required
                           oninput="validarNombreProductoModal(this.value)">
                    <span id="prod-nombre-error" class="ist-field-error" style="display:none;">
                        Ya existe un producto con ese nombre
                    </span>
                </div>

                {{-- Categoría --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Categoría *</label>
                    <select name="categoria_id" id="prod-categoria" class="admin-select w-full" required>
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->icono }} {{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Descripción --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Descripción</label>
                    <textarea name="descripcion" id="prod-desc" rows="2"
                              class="admin-input resize-none"
                              placeholder="Descripción breve del producto..."></textarea>
                </div>

                {{-- Precio --}}
                <div class="ist-field-group">
                    <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Precio ($) *</label>
                    <input name="precio" id="prod-precio" type="number" step="0.01" min="0"
                           class="admin-input" placeholder="0.00" required>
                </div>

                {{-- Stock (solo al crear) --}}
                <div id="prod-stock-fields" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <div class="ist-field-group">
                        <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Stock inicial *</label>
                        <input name="stock_actual" id="prod-stock" type="number" min="0"
                               value="0" class="admin-input" placeholder="0">
                    </div>
                    <div class="ist-field-group">
                        <label class="ist-field-label" style="color:rgba(255,255,255,0.50);">Stock mínimo</label>
                        <input name="stock_minimo" id="prod-stock-min" type="number" min="0"
                               value="2" class="admin-input" placeholder="2">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit" class="btn-gold flex-1 justify-center py-3" id="btn-prod-guardar">
                    Crear producto
                </button>
                <button type="button" onclick="cerrarModalProducto()"
                        class="btn-ghost flex-1 justify-center py-3">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<style>
/* ── Tabla ── */
.prod-row-off td { opacity:.55; }
.prod-thumb { width:44px;height:44px;border-radius:0.6rem;overflow:hidden;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);flex-shrink:0; }
.prod-img   { width:100%;height:100%;object-fit:cover; }
.prod-nombre{ font-weight:700;color:white;font-size:0.85rem;line-height:1.2; }
.prod-desc  { font-size:0.68rem;color:rgba(255,255,255,0.28);margin-top:1px; }
.stock-chip { display:inline-flex;align-items:center;gap:0.35rem;padding:0.2rem 0.6rem;border-radius:0.5rem;font-family:var(--font-display);font-weight:900;font-size:0.8rem; }
.stock-ok  { background:rgba(255,255,255,0.05);color:white; }
.stock-low { background:rgba(239,68,68,0.1);color:#f87171; }
.stock-warn{ font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;opacity:.7; }

/* ── Modal sistema unificado ── */
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
.ist-modal-body  { padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:1rem; }
.ist-modal-fields{ display:flex;flex-direction:column;gap:0.85rem; }
.ist-field-group { display:flex;flex-direction:column;gap:0.3rem; }
.ist-field-label { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.25);font-family:var(--font-display); }
.ist-field-error { font-size:0.68rem;color:#f87171;font-weight:700;margin-top:0.2rem; }

/* ── Preview imagen upload ── */
.img-upload-wrap {
    width: 100%; aspect-ratio: 16/9; border-radius: 0.75rem; overflow: hidden;
    border: 2px dashed rgba(255,255,255,0.1); cursor: pointer;
    background: #0d1228; position: relative;
    display: flex; align-items: center; justify-content: center;
    transition: border-color 0.2s ease;
}
.img-upload-wrap:hover { border-color: rgba(201,168,76,0.35); }
#prod-img-placeholder { display:flex;flex-direction:column;align-items:center;gap:0.5rem;pointer-events:none; }

/* ── Toggle futurista ── */
.ist-toggle {
    display: flex; align-items: center; gap: 0.65rem; cursor: pointer;
}
.ist-toggle-hidden { display: none; }
.ist-toggle-track {
    position: relative; width: 2.75rem; height: 1.5rem; border-radius: 999px;
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
    transition: all 0.25s ease; flex-shrink: 0;
}
.ist-toggle-track.on {
    background: rgba(16,185,129,0.2); border-color: rgba(16,185,129,0.4);
    box-shadow: 0 0 12px rgba(16,185,129,0.15);
}
.ist-toggle-thumb {
    position: absolute; top: 3px; left: 3px;
    width: 1.05rem; height: 1.05rem; border-radius: 50%;
    background: rgba(255,255,255,0.3); transition: all 0.25s ease;
}
.ist-toggle-track.on .ist-toggle-thumb {
    transform: translateX(1.25rem); background: #6ee7b7;
}
.ist-toggle-label {
    font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 600;
}

/* ── Mini-cards móvil ── */
@media (max-width: 767px) {
    .grid.grid-cols-3 .admin-card { padding:0.6rem 0.5rem!important;gap:0.4rem!important; }
    .grid.grid-cols-3 .admin-card > div:first-child { width:1.75rem!important;height:1.75rem!important;font-size:0.85rem!important;border-radius:0.4rem!important;flex-shrink:0!important; }
    .grid.grid-cols-3 .admin-card > div:last-child > div:first-child { font-size:1.1rem!important; }
    .grid.grid-cols-3 .admin-card > div:last-child > div:last-child { font-size:0.42rem!important;letter-spacing:0.06em!important;white-space:nowrap!important; }
    .ist-modal-bg { padding: 0.75rem; }
    .ist-modal-body, .ist-modal-header { padding-left: 1rem; padding-right: 1rem; }
}
</style>

@push('scripts')
<script>
const STORE_PROD_ROUTE  = '{{ route("admin.productos.store") }}';
const NOMBRES_PRODUCTOS = @json($todosLosProductos->pluck('nombre'));
let _prodNombreOriginal = null;
let _prodEditId = null;

/* ── Preview imagen ── */
function previewProdImg(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('prod-img-preview');
        const ph  = document.getElementById('prod-img-placeholder');
        img.src = e.target.result;
        img.style.display = 'block';
        ph.style.display  = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}

function resetProdImgPreview(src) {
    const img = document.getElementById('prod-img-preview');
    const ph  = document.getElementById('prod-img-placeholder');
    if (src) {
        img.src = src; img.style.display = 'block'; ph.style.display = 'none';
    } else {
        img.style.display = 'none'; img.src = ''; ph.style.display = 'flex';
    }
}

/* ── Validación nombre ── */
function validarNombreProductoModal(valor) {
    const error = document.getElementById('prod-nombre-error');
    const input = document.getElementById('prod-nombre');
    const btn   = document.getElementById('btn-prod-guardar');
    const norm  = valor.toLowerCase().trim();
    const dup   = NOMBRES_PRODUCTOS.some(n => n.toLowerCase().trim() === norm && norm !== _prodNombreOriginal);
    if (dup && norm !== '') {
        error.style.display = 'block';
        input.style.borderColor = 'rgba(248,113,113,0.5)';
        input.style.boxShadow   = '0 0 0 3px rgba(248,113,113,0.08)';
        btn.disabled = true; btn.style.opacity = '0.45'; btn.style.cursor = 'not-allowed';
    } else {
        error.style.display = 'none'; input.style.borderColor = ''; input.style.boxShadow = '';
        btn.disabled = false; btn.style.opacity = ''; btn.style.cursor = '';
    }
}

function _resetProdModal() {
    document.getElementById('prod-nombre-error').style.display = 'none';
    document.getElementById('prod-nombre').style.borderColor = '';
    document.getElementById('prod-nombre').style.boxShadow = '';
    document.getElementById('btn-prod-guardar').disabled = false;
    document.getElementById('btn-prod-guardar').style.opacity = '';
    document.getElementById('btn-prod-guardar').style.cursor = '';
    document.getElementById('prod-imagen').value = '';
}

function abrirNuevoProducto() {
    _prodEditId = null; _prodNombreOriginal = null;
    document.getElementById('prod-modal-title').textContent = 'Nuevo Producto';
    document.getElementById('btn-prod-guardar').textContent = 'Crear producto';
    document.getElementById('form-producto').action = STORE_PROD_ROUTE;
    document.getElementById('prod-method').value = 'POST';
    document.getElementById('prod-nombre').value = '';
    document.getElementById('prod-categoria').value = '';
    document.getElementById('prod-desc').value = '';
    document.getElementById('prod-precio').value = '';
    document.getElementById('prod-stock').value = '0';
    document.getElementById('prod-stock-min').value = '2';
    document.getElementById('prod-stock-fields').style.display = 'grid';
    resetProdImgPreview(null);
    _resetProdModal();
    document.getElementById('modal-producto').style.display = 'flex';
}

function abrirEditarProducto(id, nombre, categoriaId, descripcion, precio, imagenUrl) {
    _prodEditId = id; _prodNombreOriginal = nombre.toLowerCase().trim();
    document.getElementById('prod-modal-title').textContent = 'Editar Producto';
    document.getElementById('btn-prod-guardar').textContent = 'Guardar cambios';
    document.getElementById('form-producto').action = `/admin/productos/${id}`;
    document.getElementById('prod-method').value = 'PUT';
    document.getElementById('prod-nombre').value = nombre;
    document.getElementById('prod-categoria').value = categoriaId || '';
    document.getElementById('prod-desc').value = descripcion;
    document.getElementById('prod-precio').value = precio;
    document.getElementById('prod-stock-fields').style.display = 'none';
    resetProdImgPreview(imagenUrl || null);
    _resetProdModal();
    document.getElementById('modal-producto').style.display = 'flex';
}

function cerrarModalProducto() {
    document.getElementById('modal-producto').style.display = 'none';
}

@if($errors->any() && session('editing_producto_id'))
    abrirEditarProducto(
        {{ session('editing_producto_id') }},
        '{{ old("nombre","") }}',
        {{ old('categoria_id','null') }},
        '{{ old("descripcion","") }}',
        '{{ old("precio","") }}',
        ''
    );
@elseif($errors->any())
    abrirNuevoProducto();
    document.getElementById('prod-nombre').value      = '{{ old("nombre","") }}';
    document.getElementById('prod-categoria').value   = '{{ old("categoria_id","") }}';
    document.getElementById('prod-desc').value        = '{{ old("descripcion","") }}';
    document.getElementById('prod-precio').value      = '{{ old("precio","") }}';
@endif

/* ── Auto-actualización: si algo cambia desde otro lugar (otra pestaña,
   el kiosco, etc.), esta pantalla se refresca sola. No interrumpe si
   hay un modal abierto (crear/editar producto). ── */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-producto')?.style.display === 'flex';
    if (!modalAbierto) {
        location.reload();
    }
});
</script>
<script src="{{ asset('js/kiosco-realtime.js') }}"></script>
@endpush
@endsection