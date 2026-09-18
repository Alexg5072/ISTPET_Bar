<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — ISTPET Bar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,500;0,600;0,700;0,800;0,900;1,900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans" x-data style="background-color:#080d1e;color:white;overflow-x:hidden;">

{{-- Backdrop oscuro al abrir sidebar en móvil --}}
<div id="sidebar-backdrop" onclick="closeSidebar()"></div>

<div class="flex h-full">

    {{-- ══════════════════════════════════
         SIDEBAR
    ══════════════════════════════════ --}}
    <aside id="sidebar"
           class="w-[240px] flex flex-col fixed top-0 left-0 bottom-0 z-40 overflow-y-auto scrollbar-none"
           style="background:linear-gradient(180deg,#0e1326 0%,#080d1e 100%);
                  border-right:1px solid rgba(201,168,76,0.08);
                  box-shadow:2px 0 24px rgba(0,0,0,0.4);">

        <div class="px-5 pt-6 pb-5" style="border-bottom:1px solid rgba(255,255,255,0.05);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,#1b2a6b,#2a3f9f);
                            border:1px solid rgba(201,168,76,0.35);
                            box-shadow:0 4px 16px rgba(27,42,107,0.5);">
                    <x-admin.icon name="instituto" class="w-5 h-5 text-amber-400" />
                </div>
                <div>
                    <div style="font-family:var(--font-display);font-weight:900;font-size:0.9rem;
                                color:white;letter-spacing:0.06em;text-transform:uppercase;line-height:1.1;">
                        ISTPET <span style="color:#c9a84c;">Bar</span>
                    </div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.45);
                                font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-top:1px;">
                        Panel Admin
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5">
            <p class="sb-section" style="color:rgba(255,255,255,0.50);">Principal</p>
            <x-admin.nav-item route="admin.dashboard" icon="dashboard" label="Dashboard">
                @php $p = \App\Models\Pedido::delDia()->pendientes()->count(); @endphp
                @if($p > 0)<span class="sb-badge-red">{{ $p }}</span>@endif
            </x-admin.nav-item>
            <x-admin.nav-item route="admin.pedidos.index" icon="pedidos" label="Pedidos" />
            @hasanyrole('superadmin|admin|cajero')
                <x-admin.nav-item route="admin.stock.index" icon="stock" label="Stock" />
            @endhasanyrole
            @hasanyrole('superadmin|admin')
                <x-admin.nav-item route="admin.productos.index"   icon="productos" label="Productos" />
                <x-admin.nav-item route="admin.categorias.index"  icon="categorias" label="Categorías" />
                <x-admin.nav-item route="admin.combos.index"      icon="combos" label="Combos" />
                <x-admin.nav-item route="admin.reportes.index"    icon="reportes" label="Reportes" />
                <x-admin.nav-item route="admin.caja.index"        icon="caja" label="Caja" />
                <x-admin.nav-item route="admin.promociones.index" icon="promociones" label="Promociones" />
            @endhasanyrole
            @hasanyrole('superadmin|admin')
                <p class="sb-section" style="margin-top:1.2rem; color:rgba(255,255,255,0.45);">Administración</p>
                <x-admin.nav-item route="admin.usuarios.index" icon="usuarios" label="Usuarios" />
            @endhasanyrole
            @role('superadmin')
                <x-admin.nav-item route="admin.configuracion.index" icon="configuracion" label="Configuración" />
            @endrole
            @hasanyrole('superadmin|admin')
                <div class="mx-2 my-4" style="height:1px;background:rgba(255,255,255,0.04);"></div>
                <p class="sb-section" style="color:rgba(255,255,255,0.12);">Módulos extra</p>
                <x-admin.nav-item-extra route="admin.fiado.index"       icon="fiado" label="Fiado / Cuentas" />
                <x-admin.nav-item-extra route="admin.areas-venta.index" icon="areas-venta" label="Áreas de Venta" />
                <x-admin.nav-item-extra route="admin.invitados.index"   icon="invitados" label="Invitados" />
                <x-admin.nav-item-extra route="admin.inventario.index"  icon="inventario" label="Inventario Av." />
            @endhasanyrole
        </nav>

        <div class="px-4 py-4" style="border-top:1px solid rgba(255,255,255,0.05);">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm flex-shrink-0"
                     style="background:linear-gradient(135deg,#1b2a6b,#2a3f9f);">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="min-w-0">
                    <div style="font-size:0.78rem;font-weight:700;color:white;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </div>
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.50);">Administrador</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout-btn flex items-center gap-2 w-full text-left" style="color:rgba(255,255,255,0.50);">
                    <x-admin.icon name="logout" class="w-4 h-4 text-gray-400" />
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════════════════════════════════
         CONTENIDO PRINCIPAL
    ══════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-full" id="main-content"
        style="background:linear-gradient(160deg,#080d1e 0%,#0f1635 50%,#080d1e 100%);">

        {{-- Orbs difuminados decorativos --}}
        <div aria-hidden="true" style="pointer-events:none;position:fixed;top:0;left:240px;right:0;bottom:0;overflow:hidden;z-index:-1;">
            <div style="position:absolute;top:-10%;right:-5%;width:55vw;height:55vw;max-width:700px;max-height:700px;
                        border-radius:50%;
                        background:radial-gradient(circle,rgba(27,42,107,0.35) 0%,transparent 70%);
                        filter:blur(70px);"></div>
            <div style="position:absolute;bottom:-15%;left:5%;width:50vw;height:50vw;max-width:600px;max-height:600px;
                        border-radius:50%;
                        background:radial-gradient(circle,rgba(15,22,53,0.45) 0%,transparent 70%);
                        filter:blur(90px);"></div>
            <div style="position:absolute;top:40%;left:30%;width:35vw;height:35vw;max-width:450px;max-height:450px;
                        border-radius:50%;
                        background:radial-gradient(circle,rgba(201,168,76,0.045) 0%,transparent 65%);
                        filter:blur(80px);"></div>
        </div>

        <header class="sticky top-0 z-30 px-7 py-4 flex items-center justify-between gap-3"
                style="background:rgba(8,13,30,0.88);backdrop-filter:blur(20px);
                       border-bottom:1px solid rgba(255,255,255,0.06);position:relative;z-index:30;">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                    style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);">
                    @hasSection('page-icon')
                        @yield('page-icon')
                    @else
                        <x-admin.icon name="dashboard" class="w-5 h-5 text-amber-400" />
                    @endif
                </div>
                <div class="min-w-0">
                    <div style="font-family:var(--font-display);font-weight:900;font-size:1.25rem;
                                color:white;letter-spacing:0.03em;text-transform:uppercase;line-height:1.1;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        @yield('page-title','Admin')
                    </div>
                    <div style="color:rgba(255,255,255,0.50);font-size:0.73rem;margin-top:1px;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        @yield('page-subtitle','')
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                @yield('header-actions')
                <div id="header-clock"
                     style="font-family:var(--font-display);font-weight:700;font-size:0.8rem;
                            color:rgba(255,255,255,0.50);padding:0 0.5rem;"></div>
                <a href="{{ route('kiosco.inicio') }}" target="_blank" class="hdr-btn" style="color:rgba(255,255,255,0.50);display:inline-flex;align-items:center;">
                    <x-admin.icon name="eye" class="w-4 h-4 mr-1.5 opacity-70" />
                    Ver Kiosco
                </a>
            </div>
        </header>

        @if(session('success') || session('error') || session('info'))
        <div class="px-7 pt-5" style="position:relative;z-index:1;">
            @if(session('success'))
            <div class="flash-msg flash-success animate-fade-in flex items-center gap-2">
                <x-admin.icon name="check-circle" class="w-4 h-4 text-emerald-400 flex-shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="flash-msg flash-error animate-fade-in flex items-center gap-2">
                <x-admin.icon name="x-circle" class="w-4 h-4 text-rose-400 flex-shrink-0" />
                <span>{{ session('error') }}</span>
            </div>
            @endif
            @if(session('info'))
            <div class="flash-msg flash-info animate-fade-in flex items-center gap-2">
                <x-admin.icon name="alert" class="w-4 h-4 text-blue-400 flex-shrink-0" />
                <span>{{ session('info') }}</span>
            </div>
            @endif
        </div>
        @endif

        <main class="flex-1 px-7 pt-5 pb-10" style="position:relative;">
            @yield('content')
        </main>
    </div>
</div>

<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

{{-- FAB Sidebar (solo móvil) --}}
<button id="fab-sidebar" onclick="toggleSidebar()"
        style="display:none;position:fixed;bottom:1.5rem;right:1.25rem;z-index:9999;
               width:3.25rem;height:3.25rem;border-radius:50%;cursor:pointer;
               background:linear-gradient(135deg,#1b2a6b 0%,#2a3f9f 100%);
               outline:1px solid rgba(201,168,76,0.35);
               box-shadow:0 6px 28px rgba(27,42,107,0.6),0 0 0 1px rgba(201,168,76,0.15);
               align-items:center;justify-content:center;
               transition:transform 0.2s ease,box-shadow 0.2s ease;"
        onmouseover="this.style.transform='scale(1.08)';this.style.boxShadow='0 8px 32px rgba(27,42,107,0.8),0 0 0 1px rgba(201,168,76,0.35)';"
        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 6px 28px rgba(27,42,107,0.6),0 0 0 1px rgba(201,168,76,0.15)';">
    <span id="fab-icon-open" class="flex items-center justify-center pointer-events-none"><x-admin.icon name="instituto" class="w-6 h-6 text-amber-300" /></span>
    <span id="fab-icon-close" style="display:none;" class="items-center justify-center pointer-events-none"><x-admin.icon name="close" class="w-6 h-6 text-white" /></span>
</button>

<style>
/* ── Sidebar en PC: siempre visible ── */
@media (min-width: 1024px) {
    aside#sidebar    { transform: translateX(0) !important; }
    #main-content    { margin-left: 240px !important; }
    #sidebar-backdrop{ display: none !important; }
    #fab-sidebar     { display: none !important; }
}

/* ── Sidebar en móvil: drawer, controlado por clase .sidebar-open en body ── */
@media (max-width: 1023px) {
    /* El sidebar vive fuera de la pantalla por defecto */
    aside#sidebar {
        transform: translateX(-260px);
        transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
        position: fixed !important;
        top: 0; left: 0; bottom: 0;
        width: 260px !important;
        z-index: 40;
    }
    /* Cuando body tiene .sidebar-open, el sidebar entra */
    body.sidebar-open aside#sidebar {
        transform: translateX(0);
        box-shadow: 8px 0 40px rgba(0,0,0,0.7);
    }
    /* Backdrop */
    #sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 39;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(2px);
    }
    body.sidebar-open #sidebar-backdrop {
        display: block;
    }
    /* Contenido sin margen */
    #main-content { margin-left: 0 !important; }
    /* FAB visible */
    #fab-sidebar  { display: flex !important; }

    /* ── Layout: header y main con padding reducido ── */
    header.sticky {
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        padding-top: 0.6rem !important;
        padding-bottom: 0.6rem !important;
        box-sizing: border-box !important;
        width: 100% !important;
    }
    main.flex-1 {
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        padding-top: 0.875rem !important;
        box-sizing: border-box !important;
    }
    .px-7.pt-5 {
        padding-left: 0.875rem !important;
        padding-right: 0.875rem !important;
        padding-top: 0.75rem !important;
    }
    #header-clock { display: none !important; }
    .hdr-btn      { display: none !important; }

    /* ── Overflow ── */
    html, body, #main-content {
        overflow-x: hidden !important;
        max-width: 100vw !important;
    }

    /* ── Tabla wrap ── */
    .admin-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .admin-table { min-width: 520px; }
    .admin-table th, .admin-table td {
        padding: 0.5rem 0.65rem !important;
        font-size: 0.75rem !important;
        white-space: nowrap;
    }
    /* ── Cards ── */
    .admin-card-header { padding: 0.65rem 1rem !important; flex-wrap: wrap; gap: 0.35rem; }
    /* ── Flash ── */
    .flash-msg { font-size: 0.78rem !important; padding: 0.6rem 0.875rem !important; }
}

/* ── Solo móvil < 768px ── */
@media (max-width: 767px) {
    /* Header más pequeño */
    header.sticky {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }
    main.flex-1 {
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
        padding-top: 0.75rem !important;
    }
}

/* ── Estilos fijos de sidebar y UI ── */
.sb-section{padding:.6rem .75rem .3rem;font-size:.58rem;font-weight:900;text-transform:uppercase;letter-spacing:.18em;color:rgba(255,255,255,.22);font-family:var(--font-display);}
.sb-badge-red{margin-left:auto;font-size:.58rem;font-weight:900;padding:2px 6px;border-radius:999px;background:#ef4444;color:white;font-family:var(--font-display);}
.sb-logout-btn{width:100%;display:flex;align-items:center;gap:.5rem;padding:.45rem .75rem;border-radius:.6rem;font-size:.75rem;font-weight:700;color:rgba(255,255,255,.3);background:transparent;border:1px solid rgba(255,255,255,.06);cursor:pointer;transition:all .15s ease;font-family:var(--font-display);text-transform:uppercase;letter-spacing:.06em;}
.sb-logout-btn:hover{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.25);color:#f87171;}
.hdr-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:.6rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.4);text-decoration:none;transition:all .15s ease;font-family:var(--font-display);}
.hdr-btn:hover{background:rgba(255,255,255,.08);color:white;border-color:rgba(255,255,255,.15);}
.flash-msg{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border-radius:.75rem;font-size:.85rem;font-weight:600;margin-bottom:.75rem;}
.flash-success{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);color:rgb(110,231,183);}
.flash-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:rgb(252,165,165);}
.flash-info{background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);color:rgb(147,197,253);}
</style>

@stack('scripts')

<script>
/* ══════════════════════════════════════════════════════
   SIDEBAR MÓVIL — usa clases CSS, nunca estilos inline
══════════════════════════════════════════════════════ */

// Reloj del header
(function(){
    const el = document.getElementById('header-clock');
    if (!el) return;

    function tick() {
        el.textContent = new Date().toLocaleTimeString('es-EC', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    tick();
    setInterval(tick, 30000);
})();

const fab = document.getElementById('fab-sidebar');

function isMobile() {
    return window.innerWidth < 1024;
}

function openSidebar() {
    document.body.classList.add('sidebar-open');
    document.body.style.overflow = 'hidden';

    const iconOpen = document.getElementById('fab-icon-open');
    const iconClose = document.getElementById('fab-icon-close');
    if (iconOpen) iconOpen.style.display = 'none';
    if (iconClose) {
        iconClose.style.display = 'flex';
    }
}

function closeSidebar() {
    document.body.classList.remove('sidebar-open');
    document.body.style.overflow = '';

    const iconOpen = document.getElementById('fab-icon-open');
    const iconClose = document.getElementById('fab-icon-close');
    if (iconOpen) iconOpen.style.display = 'flex';
    if (iconClose) iconClose.style.display = 'none';
}

function toggleSidebar() {
    if (document.body.classList.contains('sidebar-open')) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

// Cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSidebar();
    }
});

// Al cambiar tamaño de pantalla
window.addEventListener('resize', function() {
    if (!isMobile()) {
        document.body.classList.remove('sidebar-open');
        document.body.style.overflow = '';

        const iconOpen = document.getElementById('fab-icon-open');
        const iconClose = document.getElementById('fab-icon-close');
        if (iconOpen) iconOpen.style.display = 'flex';
        if (iconClose) iconClose.style.display = 'none';
    }
});

// Estado inicial
document.addEventListener('DOMContentLoaded', function() {
    const iconOpen = document.getElementById('fab-icon-open');
    const iconClose = document.getElementById('fab-icon-close');
    if (iconOpen) iconOpen.style.display = 'flex';
    if (iconClose) iconClose.style.display = 'none';
});
</script>
</body>
</html>