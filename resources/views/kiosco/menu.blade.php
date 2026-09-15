@extends('layouts.kiosco')
@section('title', 'Menú — ' . $sede->nombre)

@section('content')
@push('styles')

<style>
/* ── Variables de tema para el kiosco ── */
:root, [data-theme="dark"] {
    --kiosco-area-bg:    #080d1e;
    --kiosco-title:      #ffffff;
    --kiosco-sub:        rgba(255,255,255,0.5);
    --kiosco-cat-bg:     #1a1f32;
    --kiosco-cat-color:  rgba(255,255,255,0.75);
    --kiosco-cat-border: rgba(255,255,255,0.1);
    --kiosco-card-bg:    #1a1f32;
    --kiosco-card-border:rgba(255,255,255,0.08);
    --kiosco-card-title: #ffffff;
    --kiosco-card-desc:  rgba(255,255,255,0.45);
    --kiosco-price:      #c9a84c;
    --kiosco-btn-add:    #c9a84c;
    --kiosco-img-bg:     #0d1228;
}
[data-theme="light"] {
    --kiosco-area-bg:    #ffffff;
    --kiosco-title:      #0f1635;
    --kiosco-sub:        #3a4a7a;
    --kiosco-cat-bg:     rgba(255,255,255,0.85);
    --kiosco-cat-color:  #1b2a6b;
    --kiosco-cat-border: rgba(27,42,107,0.20);
    --kiosco-card-bg:    rgba(255,255,255,0.88);
    --kiosco-card-border:rgba(27,42,107,0.12);
    --kiosco-card-title: #0f1635;
    --kiosco-card-desc:  #3a4a7a;
    --kiosco-price:      #1b2a6b;
    --kiosco-btn-add:    #1b2a6b;
    --kiosco-img-bg:     #e8f0fc;
}
[data-theme="light"] #kiosco-theme-btn {
    background:  rgba(201,168,76,0.35) !important;
    border:      1px solid rgba(201,168,76,0.8) !important;
    color:       #cbd630 !important;
    text-shadow: none !important;
}
[data-theme="light"] #kiosco-theme-btn:hover {
    background: rgba(201,168,76,0.5) !important;
}
[data-theme="light"] .kiosco-card-rendered {
    box-shadow:              0 4px 20px rgba(27,42,107,0.12) !important;
    backdrop-filter:         blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
}
[data-theme="light"] #menu-area {
    background:
        radial-gradient(circle, rgba(27,42,107,0.22) 1.2px, transparent 1.2px),
        linear-gradient(170deg,
            #ffffff 0%,
            #eef3ff 30%,
            #dce8ff 60%,
            #eef3ff 80%,
            #ffffff 100%
        ) !important;
    background-size: 18px 18px, 100% 100% !important;
    box-shadow: inset 0 8px 32px rgba(27,42,107,0.06),
                inset 0 -8px 32px rgba(27,42,107,0.04) !important;
}
[data-theme="light"] #kiosco-cart-aside {
    background: linear-gradient(165deg, #1b2a6b 0%, #0f1635 100%) !important;
}

/* ── Puntitos modo oscuro ── */
#menu-area {
    background:
        radial-gradient(circle, rgba(99,130,255,0.12) 1.2px, transparent 1.2px),
        linear-gradient(160deg, #080d1e 0%, #0d1228 50%, #080d1e 100%) !important;
    background-size: 18px 18px, 100% 100% !important;
}
[data-theme="dark"] #menu-area {
    background:
        radial-gradient(circle, rgba(99,130,255,0.12) 1.2px, transparent 1.2px),
        linear-gradient(160deg, #080d1e 0%, #0d1228 50%, #080d1e 100%) !important;
    background-size: 18px 18px, 100% 100% !important;
}
</style>
<style>
.kiosco-menu-grid-layout {
    grid-template-columns: 1fr 360px;
}
@media (max-width: 767px) {
    .kiosco-menu-grid-layout {
        grid-template-columns: 1fr !important;
        grid-template-rows: auto 1fr !important;
    }
    #kiosco-menu-grid > header {
        grid-column: 1 !important;
        padding: 0.55rem 0.875rem !important;
    }
    #menu-area {
        grid-column: 1 !important;
        padding-bottom: 4.5rem !important;
    }
    #kiosco-cart-aside { display: none !important; }
    .kiosco-sede-badge { display: none !important; }
    #kiosco-clock      { display: none !important; }
    #products-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.625rem !important;
    }
    #category-bar button {
        min-width: 64px !important;
        padding: 0.45rem 0.5rem !important;
        font-size: 0.56rem !important;
    }
    #category-bar button span.text-2xl { font-size: 1.35rem !important; }
    #cart-drawer-toggle { display: flex !important; }
}
@media (min-width: 768px) and (max-width: 1023px) {
    .kiosco-menu-grid-layout { grid-template-columns: 1fr 300px; }
    #products-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>
@endpush

<div id="kiosco-menu-grid" class="h-screen overflow-hidden grid kiosco-menu-grid-layout"
     style="grid-template-rows: auto 1fr;"
     x-data x-init="$store.carrito.init()">

    {{-- ── HEADER ── --}}
    <header class="col-span-2 flex items-center justify-between px-6 py-3"
            style="background: linear-gradient(135deg, {{ $sede->color_primario }}f0, {{ $sede->color_primario }});
                   border-bottom: 1px solid rgba(201,168,76,0.25);
                   box-shadow: 0 4px 24px rgba(0,0,0,0.3);">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                 style="background: rgba(201,168,76,0.15); border: 1px solid rgba(201,168,76,0.3);">
                {{ $sede->slug === 'instituto' ? '🏛️' : '🚗' }}
            </div>
            <div>
                <div class="font-display font-black text-white text-sm uppercase tracking-wider leading-tight">ISTPET Bar</div>
                <div class="text-xs" style="color: rgba(255,255,255,0.45);">Sistema de Pedidos</div>
            </div>
        </div>

        {{-- Badge sede --}}
        <div class="kiosco-sede-badge flex items-center gap-2 px-5 py-2 rounded-full"
             style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.35);">
            <div class="w-2 h-2 rounded-full" style="background:#c9a84c;"></div>
            <span class="font-display font-bold text-sm uppercase tracking-wider" style="color:#e2c47a;">
                {{ $sede->slug === 'instituto' ? '🏛️ Instituto Traversari' : '🚗 Escuela Conducción' }}
            </span>
        </div>

        {{-- ── TOGGLE TEMA ── --}}
        <button onclick="toggleThemeKiosco()" id="kiosco-theme-btn"
                style="display:flex;align-items:center;gap:0.35rem;padding:0.3rem 0.85rem;
                       border-radius:999px;cursor:pointer;
                       font-family:var(--font-display);font-weight:800;font-size:0.68rem;
                       text-transform:uppercase;letter-spacing:0.06em;
                       transition:all 0.3s ease;
                       background:rgba(201,168,76,0.32);
                       border:1px solid rgba(201,168,76,0.75);
                       color:#ffd700;
                       text-shadow:0 0 8px rgba(255,215,0,0.6);">
            <span id="kiosco-theme-icon" style="font-size:0.95rem;">☀️</span>
            <span id="kiosco-theme-label-btn" class="kiosco-sede-badge" style="display:inline;">Claro</span>
        </button>

        {{-- Usuario logueado --}}
        @auth
            @if(auth()->user()->hasRole('usuario'))
            <div style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0.85rem;border-radius:999px;
                        background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);">
                <span style="font-size:0.75rem;color:rgba(255,255,255,0.4);">👤</span>
                <span style="font-family:var(--font-display);font-weight:700;font-size:0.75rem;color:#c9a84c;text-transform:uppercase;letter-spacing:0.06em;">
                    {{ explode(' ', auth()->user()->name)[0] }}
                </span>
            </div>
            @endif
        @endauth

        {{-- Reloj + cambiar --}}
        <div class="flex items-center gap-4">
            <span class="font-display font-bold text-lg" style="color: rgba(255,255,255,0.65);" id="kiosco-clock"></span>
            <a href="{{ route('kiosco.inicio') }}"
               class="flex items-center gap-1.5 px-4 py-1.5 rounded-full font-display font-bold text-xs uppercase tracking-wider"
               style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15);
                      color: rgba(255,255,255,0.5); transition: all 0.2s; text-decoration: none;"
               onmouseover="this.style.background='rgba(255,255,255,0.14)';this.style.color='rgba(255,255,255,0.8)'"
               onmouseout="this.style.background='rgba(255,255,255,0.07)';this.style.color='rgba(255,255,255,0.5)'">
                ← Cambiar sede
            </a>
        </div>
    </header>

    {{-- ── MENÚ PRINCIPAL ── --}}
    <main class="overflow-y-auto" id="menu-area"
          style="background: var(--kiosco-area-bg, #080d1e); position:relative;">

        <div id="menu-orbs" aria-hidden="true" style="pointer-events:none;position:absolute;inset:0;overflow:hidden;z-index:0;">
            <div id="orb1" style="position:absolute;top:-10%;right:-5%;width:55%;height:55%;border-radius:50%;
                        background:radial-gradient(circle,rgba(27,42,107,0.4) 0%,transparent 70%);
                        filter:blur(60px);transition:background 0.4s ease;"></div>
            <div id="orb2" style="position:absolute;bottom:-10%;left:-5%;width:50%;height:50%;border-radius:50%;
                        background:radial-gradient(circle,rgba(15,22,53,0.5) 0%,transparent 70%);
                        filter:blur(80px);transition:background 0.4s ease;"></div>
            <div id="orb3" style="position:absolute;top:40%;left:30%;width:35%;height:35%;border-radius:50%;
                        background:radial-gradient(circle,rgba(201,168,76,0.06) 0%,transparent 65%);
                        filter:blur(70px);transition:background 0.4s ease;"></div>
        </div>

        <div class="p-5" style="position:relative;z-index:1;">

            <div class="mb-5">
                <h2 class="font-display font-black text-2xl uppercase tracking-wide mb-0.5"
                    style="color: var(--kiosco-title, #ffffff);">
                    ¿Qué vas a pedir hoy? 🍽️
                </h2>
                <p style="color: var(--kiosco-sub, rgba(255,255,255,0.5)); font-size: 0.875rem;">
                    Selecciona una categoría y agrega productos al carrito
                </p>
            </div>

            <div class="flex gap-2.5 overflow-x-auto pb-3 mb-5 scrollbar-none" id="category-bar">
                <button onclick="cargarProductos('todos', this)"
                        class="flex-shrink-0 flex flex-col items-center gap-1.5 px-5 py-3 rounded-2xl font-display font-black text-xs uppercase tracking-wider transition-all duration-200 active:scale-95"
                        style="min-width: 90px; background: {{ $sede->color_primario }}; color: #c9a84c;
                               border: 2px solid #c9a84c; box-shadow: 0 4px 16px rgba(27,42,107,0.3);"
                        data-cat="todos">
                    <span class="text-2xl">🍽️</span>
                    <span>Todos</span>
                </button>

                <button onclick="cargarProductos('combos', this)"
                        class="flex-shrink-0 flex flex-col items-center gap-1.5 px-5 py-3 rounded-2xl font-display font-black text-xs uppercase tracking-wider transition-all duration-200 active:scale-95"
                        style="min-width: 90px; background: var(--kiosco-cat-bg,#1a1f32); color: var(--kiosco-cat-color,rgba(255,255,255,0.75));
                               border: 2px solid var(--kiosco-cat-border,rgba(255,255,255,0.1)); box-shadow: 0 2px 10px rgba(0,0,0,0.25);"
                        data-cat="combos"
                        onmouseover="if(!this.classList.contains('cat-active'))this.style.boxShadow='0 4px 20px rgba(0,0,0,0.35)'"
                        onmouseout="if(!this.classList.contains('cat-active'))this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                    <span class="text-2xl">⭐</span>
                    <span>Combos</span>
                </button>

                @foreach($categorias as $cat)
                <button onclick="cargarProductos('{{ $cat->id }}', this)"
                        class="flex-shrink-0 flex flex-col items-center gap-1.5 px-5 py-3 rounded-2xl font-display font-black text-xs uppercase tracking-wider transition-all duration-200 active:scale-95"
                        style="min-width: 90px; background: var(--kiosco-cat-bg,#1a1f32); color: var(--kiosco-cat-color,rgba(255,255,255,0.75));
                               border: 2px solid var(--kiosco-cat-border,rgba(255,255,255,0.1)); box-shadow: 0 2px 10px rgba(0,0,0,0.25);"
                        data-cat="{{ $cat->id }}"
                        onmouseover="if(!this.classList.contains('cat-active'))this.style.boxShadow='0 4px 20px rgba(0,0,0,0.35)'"
                        onmouseout="if(!this.classList.contains('cat-active'))this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'">
                    <span class="text-2xl">{{ $cat->icono }}</span>
                    <span>{{ $cat->nombre }}</span>
                </button>
                @endforeach
            </div>

            <h3 class="font-display font-black text-lg uppercase tracking-wide mb-4"
                style="color: var(--kiosco-title, #ffffff);" id="section-title">
                🍽️ Todos los productos
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4" id="products-grid">
                <div class="col-span-full flex flex-col items-center justify-center py-16 gap-3">
                    <div class="w-10 h-10 rounded-full border-2 border-t-inst-600 animate-spin"
                         style="border-color: rgba(27,42,107,0.15); border-top-color: {{ $sede->color_primario }};"></div>
                    <span class="font-display font-bold text-xs uppercase tracking-wider" style="color:#9ca3af;">
                        Cargando productos...
                    </span>
                </div>
            </div>

        </div>
    </main>

    {{-- ── SIDEBAR CARRITO ── --}}
    <aside id="kiosco-cart-aside" class="flex flex-col overflow-hidden"
           style="background: linear-gradient(165deg, {{ $sede->color_primario }}f8, {{ $sede->color_primario }});
                  border-left: 1px solid rgba(255,255,255,0.08);
                  box-shadow: -8px 0 32px rgba(0,0,0,0.2);">

        <div class="flex items-center justify-between px-5 py-4 flex-shrink-0"
             style="border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🛒</span>
                <span class="font-display font-black text-white text-base uppercase tracking-wider">Tu pedido</span>
                <span id="cart-badge"
                      class="flex items-center justify-center w-6 h-6 rounded-full font-black text-xs transition-transform duration-200"
                      style="background: #c9a84c; color: #0b1133;"
                      x-text="$store.carrito.count">0</span>
            </div>
            <button type="button" onclick="confirmarLimpiar()"
                    class="text-lg transition-colors duration-150"
                    style="color: rgba(255,255,255,0.2);"
                    onmouseover="this.style.color='rgba(239,68,68,0.7)'"
                    onmouseout="this.style.color='rgba(255,255,255,0.2)'">🗑</button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-2">
            <div x-show="$store.carrito.vacio"
                 class="flex flex-col items-center justify-center h-full gap-3 py-10">
                <div class="text-5xl opacity-15">🛒</div>
                <div class="font-display font-bold text-xs uppercase tracking-wider text-center"
                     style="color: rgba(255,255,255,0.3);">
                    Tu carrito está vacío
                </div>
            </div>
            <template x-if="!$store.carrito.vacio">
                <div>
                    <template x-for="item in $store.carrito.items" :key="item.tipo + '_' + item.id">
                        <div class="flex items-center gap-3 py-3"
                             style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                                 style="background: rgba(255,255,255,0.08);">🍽️</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-display font-bold text-sm text-white uppercase truncate leading-tight"
                                     x-text="item.nombre"></div>
                                <div class="text-xs font-bold mt-0.5"
                                     style="color: #e2c47a;" x-text="'$' + (item.precio * item.cantidad).toFixed(2)"></div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button type="button" class="qty-btn" @click="$store.carrito.cambiarCantidad(item.tipo, item.id, -1)">−</button>
                                <span class="font-display font-black text-white text-sm w-5 text-center"
                                      x-text="item.cantidad"></span>
                                <button type="button" class="qty-btn" @click="$store.carrito.cambiarCantidad(item.tipo, item.id, 1)">+</button>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex-shrink-0 px-5 py-4 space-y-3"
             style="border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.15);">
            <div class="flex justify-between items-center text-sm"
                 style="color: rgba(255,255,255,0.45);">
                <span>Subtotal</span>
                <span x-text="'$' + $store.carrito.total.toFixed(2)"></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-display font-black text-white uppercase tracking-wider">Total</span>
                <span class="font-display font-black text-2xl"
                      style="color: #c9a84c;" x-text="'$' + $store.carrito.total.toFixed(2)"></span>
            </div>
            <a href="{{ route('kiosco.pago') }}"
               :class="$store.carrito.vacio ? 'opacity-40 pointer-events-none' : ''"
               class="flex items-center justify-center gap-2 w-full py-3.5 rounded-xl font-display font-black text-sm uppercase tracking-wider"
               style="background: linear-gradient(135deg, #a07d2e, #c9a84c, #e2c47a);
                      color: #0b1133; box-shadow: 0 8px 24px rgba(201,168,76,0.35);
                      transition: all 0.2s; text-decoration: none;"
               onmouseover="if(!this.classList.contains('pointer-events-none'))this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 32px rgba(201,168,76,0.5)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(201,168,76,0.35)'">
                Ir al pago
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <button type="button" onclick="$store.carrito.limpiar()"
                    class="w-full py-1.5 font-display font-bold text-xs uppercase tracking-wider transition-colors duration-150 text-center"
                    style="color: rgba(255,255,255,0.2);"
                    onmouseover="this.style.color='rgba(239,68,68,0.6)'"
                    onmouseout="this.style.color='rgba(255,255,255,0.2)'">
                Vaciar carrito
            </button>
        </div>
    </aside>

</div>

{{-- ── DRAWER TOGGLE (visible solo en móvil) ── --}}
<button id="cart-drawer-toggle"
        style="background: linear-gradient(135deg, {{ $sede->color_primario }}, {{ $sede->color_primario }}dd);">
    <div style="display:flex;align-items:center;gap:0.6rem;">
        <div style="position:relative;">
            <span style="font-size:1.5rem;">🛒</span>
            <span x-text="$store.carrito.count"
                  x-show="$store.carrito.count > 0"
                  style="position:absolute;top:-6px;right:-8px;
                         background:#c9a84c;color:#0b1133;
                         font-family:var(--font-display);font-weight:900;font-size:0.6rem;
                         width:1.1rem;height:1.1rem;border-radius:50%;
                         display:flex;align-items:center;justify-content:center;">0</span>
        </div>
        <div>
            <div style="font-family:var(--font-display);font-weight:900;font-size:0.8rem;
                        color:white;text-transform:uppercase;letter-spacing:0.06em;line-height:1.1;">
                Tu pedido
            </div>
            <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);font-family:var(--font-display);">
                Toca para ver y pagar →
            </div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <span id="cart-toggle-total"
              x-text="'$' + $store.carrito.total.toFixed(2)"
              style="font-family:var(--font-display);font-weight:900;font-size:1.35rem;color:#c9a84c;">
            $0.00
        </span>
        <div style="width:2rem;height:2rem;border-radius:50%;
                    background:rgba(201,168,76,0.2);border:1px solid rgba(201,168,76,0.4);
                    display:flex;align-items:center;justify-content:center;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                 stroke="#c9a84c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 15l7-7 7 7"/>
            </svg>
        </div>
    </div>
</button>

{{-- ── BACKDROP DRAWER ── --}}
<div id="cart-drawer-backdrop"></div>

{{-- ── DRAWER CARRITO (móvil) ── --}}
<div id="cart-drawer"
     style="background: linear-gradient(165deg, {{ $sede->color_primario }}f8, {{ $sede->color_primario }});">

    <div style="display:flex;justify-content:center;padding:0.6rem 0 0.2rem;">
        <div style="width:2.5rem;height:4px;border-radius:999px;background:rgba(255,255,255,0.2);"></div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;
                padding:0.6rem 1.25rem 0.75rem;border-bottom:1px solid rgba(255,255,255,0.1);">
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <span style="font-size:1.1rem;">🛒</span>
            <span style="font-family:var(--font-display);font-weight:900;color:white;font-size:0.95rem;
                         text-transform:uppercase;letter-spacing:0.06em;">Tu pedido</span>
            <span style="background:#c9a84c;color:#0b1133;font-weight:900;font-size:0.7rem;
                         padding:2px 7px;border-radius:999px;font-family:var(--font-display);"
                  x-text="$store.carrito.count">0</span>
        </div>
        <button onclick="document.getElementById('cart-drawer').classList.remove('open');
                         document.getElementById('cart-drawer-backdrop').classList.remove('active');
                         document.body.style.overflow='';"
                style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);
                       color:rgba(255,255,255,0.5);border-radius:0.5rem;padding:0.3rem 0.6rem;
                       font-size:0.8rem;cursor:pointer;">✕</button>
    </div>

    <div style="flex:1;overflow-y:auto;padding:0.5rem 1rem;min-height:0;max-height:55vh;">
        <div x-show="$store.carrito.vacio"
             style="text-align:center;padding:2rem;color:rgba(255,255,255,0.3);
                    font-family:var(--font-display);font-size:0.8rem;text-transform:uppercase;">
            Tu carrito está vacío
        </div>
        <template x-if="!$store.carrito.vacio">
            <div>
                <template x-for="item in $store.carrito.items" :key="item.tipo + '_' + item.id">
                    <div style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 0;
                                border-bottom:1px solid rgba(255,255,255,0.07);">
                        <div style="flex:1;min-width:0;">
                            <div style="font-family:var(--font-display);font-weight:800;font-size:0.85rem;
                                        color:white;text-transform:uppercase;white-space:nowrap;
                                        overflow:hidden;text-overflow:ellipsis;" x-text="item.nombre"></div>
                            <div style="font-size:0.8rem;font-weight:700;color:#e2c47a;margin-top:2px;"
                                 x-text="'$' + (item.precio * item.cantidad).toFixed(2)"></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                            <button type="button" class="qty-btn"
                                    @click="$store.carrito.cambiarCantidad(item.tipo, item.id, -1)">−</button>
                            <span style="font-family:var(--font-display);font-weight:900;color:white;
                                         font-size:0.9rem;width:1.25rem;text-align:center;"
                                  x-text="item.cantidad"></span>
                            <button type="button" class="qty-btn"
                                    @click="$store.carrito.cambiarCantidad(item.tipo, item.id, 1)">+</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <div style="padding:0.75rem 1.25rem 5rem;border-top:1px solid rgba(255,255,255,0.1);
                background:rgba(0,0,0,0.15);flex-shrink:0;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
            <span style="font-family:var(--font-display);font-weight:900;color:white;
                        text-transform:uppercase;font-size:0.85rem;">Total</span>
            <span style="font-family:var(--font-display);font-weight:900;font-size:1.6rem;color:#c9a84c;"
                x-text="'$' + $store.carrito.total.toFixed(2)">$0.00</span>
        </div>
        <a href="{{ route('kiosco.pago') }}"
           :class="$store.carrito.vacio ? 'opacity-40 pointer-events-none' : ''"
           style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;
                  padding:1rem;border-radius:0.9rem;font-family:var(--font-display);font-weight:900;
                  font-size:0.95rem;text-transform:uppercase;letter-spacing:0.06em;color:#0b1133;
                  background:linear-gradient(135deg,#a07d2e,#c9a84c,#e2c47a);text-decoration:none;
                  box-shadow:0 6px 20px rgba(201,168,76,0.35);box-sizing:border-box;">
            Ir al pago →
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>

/* ── TEMA KIOSCO ── */
var _themeDebounce = false;

function applyKioscoThemeUI(t) {
    var icon  = document.getElementById('kiosco-theme-icon');
    var label = document.getElementById('kiosco-theme-label-btn');
    var btn   = document.getElementById('kiosco-theme-btn');
    if (t === 'dark') {
        if (icon)  icon.textContent  = '☀️';
        if (label) label.textContent = 'Claro';
        if (btn) {
            btn.style.background   = 'rgba(201,168,76,0.32)';
            btn.style.borderColor  = 'rgba(201,168,76,0.75)';
            btn.style.color        = '#ffd700';
            btn.style.textShadow   = '0 0 8px rgba(255,215,0,0.6)';
        }
    } else {
        if (icon)  icon.textContent  = '🌙';
        if (label) label.textContent = 'Oscuro';
        if (btn) {
            btn.style.background   = 'rgba(201,168,76,0.32)';
            btn.style.borderColor  = 'rgba(201,168,76,0.75)';
            btn.style.color        = '#cbd630';
            btn.style.textShadow   = 'none';
        }
    }
}

function toggleThemeKiosco() {
    if (_themeDebounce) return;
    _themeDebounce = true;
    var btn = document.getElementById('kiosco-theme-btn');
    if (btn) { btn.style.opacity = '0.5'; btn.style.pointerEvents = 'none'; }
    setTimeout(function() {
        _themeDebounce = false;
        if (btn) { btn.style.opacity = '1'; btn.style.pointerEvents = ''; }
    }, 3000);

    var cur  = document.documentElement.getAttribute('data-theme') || 'dark';
    var next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('istpet-theme', next);
    applyKioscoThemeUI(next);

    var activeCat = document.querySelector('#category-bar button.cat-active');
    var catId = activeCat ? activeCat.dataset.cat : 'todos';

    var orb1 = document.getElementById('orb1');
    var orb2 = document.getElementById('orb2');
    var orb3 = document.getElementById('orb3');
    if (next === 'dark') {
        if (orb1) orb1.style.background = 'radial-gradient(circle,rgba(27,42,107,0.4) 0%,transparent 70%)';
        if (orb2) orb2.style.background = 'radial-gradient(circle,rgba(15,22,53,0.5) 0%,transparent 70%)';
        if (orb3) orb3.style.background = 'radial-gradient(circle,rgba(201,168,76,0.06) 0%,transparent 65%)';
    } else {
        if (orb1) orb1.style.background = 'radial-gradient(circle,rgba(99,130,246,0.25) 0%,transparent 70%)';
        if (orb2) orb2.style.background = 'radial-gradient(circle,rgba(27,42,107,0.18) 0%,transparent 70%)';
        if (orb3) orb3.style.background = 'radial-gradient(circle,rgba(201,168,76,0.08) 0%,transparent 65%)';
    }

    document.querySelectorAll('#category-bar button:not(.cat-active)').forEach(function(b) {
        b.style.background  = next === 'dark' ? '#1a1f32' : '#eef2fc';
        b.style.color       = next === 'dark' ? 'rgba(255,255,255,0.75)' : '#1b2a6b';
        b.style.borderColor = next === 'dark' ? 'rgba(255,255,255,0.1)' : 'rgba(27,42,107,0.2)';
        b.style.boxShadow   = next === 'dark' ? '0 2px 10px rgba(0,0,0,0.25)' : '0 2px 8px rgba(27,42,107,0.1)';
    });
    cargarProductos(catId, activeCat);
}

/* ── Persistencia del tema: lee localStorage ANTES de pintar ── */
(function(){
    var saved = localStorage.getItem('istpet-theme');
    var t = saved || 'dark';
    document.documentElement.setAttribute('data-theme', t);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { applyKioscoThemeUI(t); });
    } else {
        applyKioscoThemeUI(t);
    }
})();

const SEDE_ID    = {{ $sede->id }};
const SEDE_SLUG  = @json($sede->slug);
const SEDE_COLOR = @json($sede->color_primario);

function initClock() {
    const el = document.getElementById('kiosco-clock');
    if (!el) return;
    const upd = () => {
        el.textContent = new Date().toLocaleTimeString('es-EC', {hour:'2-digit',minute:'2-digit',hour12:true});
    };
    upd(); setInterval(upd, 1000);
}

function getCSSVar(name, fallback) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
}

function setActiveCat(btn) {
    const catBg     = getCSSVar('--kiosco-cat-bg',     '#1a1f32');
    const catColor  = getCSSVar('--kiosco-cat-color',  'rgba(255,255,255,0.75)');
    const catBorder = getCSSVar('--kiosco-cat-border',  'rgba(255,255,255,0.1)');

    document.querySelectorAll('#category-bar button').forEach(b => {
        b.classList.remove('cat-active');
        b.style.background  = catBg;
        b.style.color       = catColor;
        b.style.borderColor = catBorder;
        b.style.boxShadow   = '0 2px 10px rgba(0,0,0,0.2)';
    });
    if (btn) {
        btn.classList.add('cat-active');
        btn.style.background  = SEDE_COLOR;
        btn.style.color       = '#c9a84c';
        btn.style.borderColor = '#c9a84c';
        btn.style.boxShadow   = '0 4px 16px rgba(27,42,107,0.4)';
    }
}

async function cargarProductos(catId, btn) {
    setActiveCat(btn);
    const grid = document.getElementById('products-grid');

    grid.innerHTML = `
        <div class="col-span-full flex flex-col items-center justify-center py-16 gap-3">
            <div style="width:2.5rem;height:2.5rem;border-radius:50%;border:3px solid rgba(27,42,107,0.15);
                        border-top-color:${SEDE_COLOR};animation:spin 0.8s linear infinite;"></div>
            <span style="color:#9ca3af;font-size:0.75rem;font-family:var(--font-display);
                         font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">
                Cargando...
            </span>
        </div>`;

    try {
        const params = new URLSearchParams({ sede_id: SEDE_ID });
        if (catId && catId !== 'todos') params.set('categoria_id', catId);
        const res  = await fetch(`/kiosco/api/productos?${params}`);
        const data = await res.json();
        renderProductos(data.productos ?? []);
    } catch(e) {
        grid.innerHTML = `<div class="col-span-full text-center py-12" style="color:#9ca3af;">
            <div style="font-size:2.5rem;margin-bottom:0.5rem;">😔</div>
            <div style="font-family:var(--font-display);font-weight:700;text-transform:uppercase;font-size:0.85rem;">
                Error al cargar productos
            </div>
        </div>`;
    }
}

function renderProductos(productos) {
    const grid = document.getElementById('products-grid');

    if (!productos.length) {
        grid.innerHTML = `<div class="col-span-full text-center py-12">
            <div style="font-size:2.5rem;margin-bottom:0.75rem;opacity:0.3;">🍽️</div>
            <div style="font-family:var(--font-display);font-weight:700;text-transform:uppercase;font-size:0.85rem;color:#9ca3af;">
                Sin productos en esta categoría
            </div>
        </div>`;
        return;
    }

    grid.innerHTML = productos.map((p, i) => {
        const comboId = p.es_combo ? String(p.id).replace('combo-','') : p.id;
        const tipo    = p.es_combo ? 'combo' : 'producto';
        const nombre  = p.nombre.replace(/"/g, '&quot;').replace(/'/g, '&#39;');

        return `
        <div class="relative rounded-2xl overflow-hidden kiosco-card-rendered"
             style="background:var(--kiosco-card-bg,#1a1f32); border:2px solid var(--kiosco-card-border,rgba(255,255,255,0.08));
                    box-shadow:0 4px 16px rgba(0,0,0,0.15);
                    animation:fadeInCard 0.4s ease ${i*0.04}s both;
                    cursor:${p.disponible ? 'pointer' : 'not-allowed'};
                    ${!p.disponible ? 'opacity:0.5;filter:grayscale(0.4);' : ''}"
             data-tipo="${tipo}" data-id="${comboId}"
             data-nombre="${nombre}" data-disponible="${p.disponible}" data-stock="${p.stock_max != null ? p.stock_max : 999}"
             onclick="handleProductClick(this)"
             ${p.disponible ? `
                onmouseover="this.style.transform='translateY(-5px) scale(1.01)';this.style.borderColor='#c9a84c';this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)'"
                onmouseout="this.style.transform='';this.style.borderColor='transparent';this.style.boxShadow='0 2px 10px rgba(0,0,0,0.08)'"
                onmousedown="this.style.transform='scale(0.97)'"
                onmouseup="this.style.transform='translateY(-5px) scale(1.01)'"` : ''}>

            ${p.es_combo ? `
                <div style="position:absolute;top:0;left:0;padding:0.2rem 0.7rem;border-radius:0 0 0.6rem 0;z-index:10;
                             background:linear-gradient(135deg,#8a6a20,#c9a84c);color:#0b1133;
                             font-family:var(--font-display);font-weight:900;font-size:0.6rem;
                             text-transform:uppercase;letter-spacing:0.08em;">⭐ Combo</div>` : ''}

            ${!p.disponible ? `
                <div style="position:absolute;top:0.5rem;right:0.5rem;padding:0.2rem 0.5rem;border-radius:0.4rem;z-index:10;
                             background:rgba(239,68,68,0.9);color:white;
                             font-family:var(--font-display);font-weight:900;font-size:0.6rem;
                             text-transform:uppercase;letter-spacing:0.08em;">Agotado</div>` : ''}

            <div style="aspect-ratio:4/3;overflow:hidden;background:var(--kiosco-img-bg,#0d1228);position:relative;">
                <img src="${p.imagen_url ?? ''}" alt="${nombre}"
                     style="width:100%;height:100%;object-fit:cover;"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div style="display:none;position:absolute;inset:0;align-items:center;justify-content:center;
                             font-size:3rem;background:var(--kiosco-img-bg,#0d1228);">🍽️</div>
            </div>

            <div style="padding:0.85rem;">
                <div style="font-family:var(--font-display);font-weight:900;font-size:0.9rem;
                             color:var(--kiosco-card-title,#ffffff);text-transform:uppercase;letter-spacing:0.02em;
                             line-height:1.2;margin-bottom:0.3rem;">${p.nombre}</div>
                <div style="font-size:0.72rem;color:var(--kiosco-card-desc,rgba(255,255,255,0.5));margin-bottom:0.65rem;
                             display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
                             overflow:hidden;line-height:1.4;">${p.descripcion || ''}</div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-family:var(--font-display);font-weight:900;font-size:1.3rem;color:var(--kiosco-price,#c9a84c);">
                        $${parseFloat(p.precio).toFixed(2)}
                    </span>
                    ${p.disponible ? `
                    <button type="button"
                            data-tipo="${tipo}" data-id="${comboId}" data-nombre="${nombre}" data-stock="${p.stock_max != null ? p.stock_max : 999}"
                            onclick="event.stopPropagation();handleProductClick(this)"
                            onmouseover="this.style.transform='scale(1.15)'"
                            onmouseout="this.style.transform='scale(1)'"
                            onmousedown="this.style.transform='scale(0.88)'"
                            style="width:2.25rem;height:2.25rem;border-radius:50%;border:none;
                                   background:var(--kiosco-btn-add,#c9a84c);color:#0b1133;
                                   font-weight:900;font-size:1.4rem;cursor:pointer;
                                   display:flex;align-items:center;justify-content:center;
                                   box-shadow:0 4px 12px rgba(27,42,107,0.35);
                                   transition:transform 0.15s ease;">+</button>
                    ` : `<span style="font-size:0.72rem;color:var(--kiosco-card-desc,rgba(255,255,255,0.4));">No disponible</span>`}
                </div>
            </div>
        </div>`;
    }).join('');
}

function handleProductClick(el) {
    const card = el.closest('[data-tipo]') || el;
    if (!card.dataset.tipo) return;
    if (card.dataset.disponible === 'false' || card.dataset.disponible === 'undefined') {
        window.showToast?.(`⛔ ${card.dataset.nombre} está agotado`, 'error');
        return;
    }
    const stock = card.dataset.stock ? parseInt(card.dataset.stock) : 999;
    agregarAlCarrito(card.dataset.tipo, card.dataset.id, card.dataset.nombre, isNaN(stock) ? 999 : stock);
}

async function agregarAlCarrito(tipo, id, nombre, stockMax) {
    await Alpine.store('carrito').agregar(tipo, id, nombre, stockMax);
}

function confirmarLimpiar() {
    if (Alpine.store('carrito').vacio) return;
    const existente = document.getElementById('modal-limpiar');
    if (existente) existente.remove();

    const modal = document.createElement('div');
    modal.id = 'modal-limpiar';
    modal.style.cssText = `position:fixed;inset:0;z-index:9999;
                           display:flex;align-items:center;justify-content:center;
                           background:rgba(0,0,0,0.72);backdrop-filter:blur(8px);`;
    modal.innerHTML = `
        <div style="background:#1e2130;border:1px solid rgba(255,255,255,0.1);
                    border-radius:1.5rem;padding:2.25rem 2rem;max-width:360px;width:92%;
                    text-align:center;box-shadow:0 28px 70px rgba(0,0,0,0.55);
                    animation:scaleIn 0.25s ease both;">
            <div style="font-size:3.5rem;margin-bottom:1rem;line-height:1;">🗑️</div>
            <div style="font-family:var(--font-display);font-weight:900;font-size:1.25rem;
                        color:white;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">
                ¿Vaciar carrito?
            </div>
            <p style="color:rgba(255,255,255,0.42);font-size:0.875rem;line-height:1.6;margin-bottom:1.75rem;">
                Se eliminarán todos los productos que seleccionaste.
            </p>
            <div style="display:flex;gap:0.75rem;">
                <button id="modal-cancel-btn"
                        style="flex:1;padding:0.9rem;border-radius:0.75rem;
                               border:1px solid rgba(255,255,255,0.12);
                               background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.65);
                               font-family:var(--font-display);font-weight:700;font-size:0.85rem;
                               text-transform:uppercase;letter-spacing:0.05em;cursor:pointer;"
                        onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                    Cancelar
                </button>
                <button id="modal-confirm-btn"
                        style="flex:1;padding:0.9rem;border-radius:0.75rem;border:none;
                               background:linear-gradient(135deg,#7f1d1d,#ef4444);color:white;
                               font-family:var(--font-display);font-weight:900;font-size:0.85rem;
                               text-transform:uppercase;letter-spacing:0.05em;cursor:pointer;"
                        onmouseover="this.style.opacity='0.88'"
                        onmouseout="this.style.opacity='1'">
                    Sí, vaciar
                </button>
            </div>
        </div>`;

    document.body.appendChild(modal);
    document.getElementById('modal-cancel-btn').addEventListener('click', () => modal.remove());
    document.getElementById('modal-confirm-btn').addEventListener('click', () => {
        Alpine.store('carrito').limpiar();
        modal.remove();
    });
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
}

async function initModoReposo() {
    let slides = [];
    try {
        const res  = await fetch(`/kiosco/api/promociones?sede_id=${SEDE_ID}`);
        const data = await res.json();
        slides = data.promociones ?? [];
    } catch(e) {}

    if (!slides.length) {
        slides = [{
            titulo: 'ISTPET Bar',
            descripcion: 'Toca la pantalla para realizar tu pedido',
            imagen_url: null,
            precio_destacado: null,
            duracion: 5,
        }];
    }

    const waitForModoReposo = (resolve) => {
        if (window.ModoReposo) { resolve(window.ModoReposo); }
        else { setTimeout(() => waitForModoReposo(resolve), 50); }
    };
    const MR = await new Promise(waitForModoReposo);
    const IDLE_MS  = {{ \App\Models\Configuracion::get('kiosco.tiempo_inactividad', 60) * 1000 }};
    const SLIDE_MS = {{ \App\Models\Configuracion::get('kiosco.tiempo_reposo', 5) * 1000 }};
    const modo = new MR(IDLE_MS);
    modo.init(slides, IDLE_MS, SLIDE_MS);
}

/* ── Auto-actualización en tiempo real ──
   Se recarga la página completa (no solo los productos) porque la barra
   de categorías (íconos, nombres, orden) se renderiza con Blade al cargar
   la página y no se puede "parchear" solo con un fetch de productos.
   El carrito vive en la sesión del servidor, así que no se pierde nada
   al recargar. Antes de recargar, guardamos qué categoría tenía abierta
   el cliente para devolverlo ahí mismo (y no que vuelva a "Todos"). */
window.addEventListener('kiosco:cambio', function () {
    const modalAbierto = document.getElementById('modal-limpiar');
    if (!modalAbierto) {
        const btnActivo = document.querySelector('#category-bar button.cat-active');
        const catId = btnActivo?.dataset?.cat || 'todos';
        sessionStorage.setItem('istpet-kiosco-cat-activa', catId);
        location.reload();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initClock();

    // Si venimos de un auto-refresh, volvemos a abrir la misma categoría
    // que el cliente tenía seleccionada. Se borra después de leerla una
    // vez, para que una visita nueva al kiosco siempre arranque en "Todos".
    const catGuardada = sessionStorage.getItem('istpet-kiosco-cat-activa');
    sessionStorage.removeItem('istpet-kiosco-cat-activa');
    const catId = catGuardada || 'todos';
    const btn   = document.querySelector(`#category-bar button[data-cat="${catId}"]`)
               || document.querySelector('#category-bar button[data-cat="todos"]');

    cargarProductos(catId, btn);
    initModoReposo();
});
</script>

<script src="{{ asset('js/kiosco-realtime.js') }}" data-sede-id="{{ $sede->id }}"></script>

<style>
@keyframes fadeInCard {
    from { opacity:0; transform:translateY(12px) scale(0.97); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
@keyframes scaleIn {
    from { opacity:0; transform:scale(0.93); }
    to   { opacity:1; transform:scale(1); }
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush