<!DOCTYPE html>
<html lang="es" class="h-full" style="background-color: #080d1e; color: #ffffff;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ISTPET Bar') — Sistema de Pedidos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/kiosco.js'])
    <style>
        html, body {
            background-color: #080d1e !important;
            color: #ffffff !important;
            margin: 0;
            padding: 0;
        }
    </style>
    @stack('styles')
    <style>
        * { -webkit-user-select:none; user-select:none; }
        input,textarea { -webkit-user-select:text; user-select:text; }
        ::-webkit-scrollbar { width:4px; }
        ::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.1); border-radius:4px; }

        /* ── Drawer: oculto en PC, visible en móvil ── */
        #cart-drawer-toggle { display: none !important; }
        #cart-drawer-backdrop { display: none; }

        #cart-drawer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 49;
            max-height: 92vh;
            flex-direction: column;
            border-radius: 1.25rem 1.25rem 0 0;
            border-top: 1px solid rgba(201,168,76,0.25);
            transform: translateY(110%);
            transition: transform 0.32s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
            display: flex;
            visibility: hidden;
        }
        #cart-drawer.open {
            transform: translateY(0) !important;
            visibility: visible;
        }

        @media (max-width: 767px) {
            #cart-drawer-toggle {
                display: flex !important;
                position: fixed;
                bottom: 0; left: 0; right: 0;
                z-index: 50;
                padding: 0.7rem 1.1rem;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 -4px 24px rgba(0,0,0,0.5);
                cursor: pointer;
                min-height: 4rem;
                border-top: 1px solid rgba(201,168,76,0.3);
            }
            #cart-drawer {
                visibility: visible;
                max-height: 92vh;
            }
            #cart-drawer-backdrop.active {
                display: block !important;
                position: fixed;
                inset: 0;
                z-index: 48;
                background: rgba(0,0,0,0.55);
                backdrop-filter: blur(2px);
            }
        }
    </style>
</head>
<body class="h-full bg-[#080d1e] text-white font-sans overflow-x-hidden" x-data>

    @yield('content')

    <div id="toast-container" class="fixed top-5 right-4 z-[99999] flex flex-col gap-2 pointer-events-none"></div>

    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle   = document.getElementById('cart-drawer-toggle');
        const drawer   = document.getElementById('cart-drawer');
        const backdrop = document.getElementById('cart-drawer-backdrop');
        if (!toggle || !drawer) return;

        function openDrawer() {
            drawer.classList.add('open');
            if (backdrop) backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            drawer.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', openDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);

        // ── Al redimensionar: si pasa a PC, cerrar drawer y ocultar toggle ──
        function checkViewport() {
            if (window.innerWidth >= 768) {
                closeDrawer();
                toggle.style.display = 'none';
            } else {
                toggle.style.display = 'flex';
            }
        }
        checkViewport();
        window.addEventListener('resize', checkViewport);

        // Sincronizar badge del toggle con Alpine store
        const poll = setInterval(function () {
            if (window.Alpine && Alpine.store && Alpine.store('carrito')) {
                clearInterval(poll);
                Alpine.effect(function () {
                    const c = Alpine.store('carrito');
                    const countEl = document.getElementById('cart-toggle-count');
                    const totalEl = document.getElementById('cart-toggle-total');
                    if (countEl) countEl.textContent = c.count + (c.count === 1 ? ' producto' : ' productos');
                    if (totalEl) totalEl.textContent = '$' + parseFloat(c.total || 0).toFixed(2);
                    if (c.vacio) closeDrawer();
                });
            }
        }, 80);
    });
    </script>
</body>
</html>