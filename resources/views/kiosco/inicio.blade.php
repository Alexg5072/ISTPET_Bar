@extends('layouts.kiosco')
@section('title', 'ISTPET Bar')

@section('content')
<div class="kiosco-root">

    {{-- ░░ FONDO CINEMATOGRÁFICO ░░ --}}
    <div class="bg-scene">
        <div class="bg-grid"></div>
        <div class="bg-orb bg-orb-1"></div>
        <div class="bg-orb bg-orb-2"></div>
        <div class="bg-orb bg-orb-3"></div>
        <div class="bg-noise"></div>
        <div class="bg-vignette"></div>
    </div>

    {{-- ░░ LÍNEA SUPERIOR ░░ --}}
    <div class="top-bar">
        <div class="top-bar-line"></div>
        <div class="top-bar-glow"></div>
    </div>

    {{-- ░░ CONTENIDO PRINCIPAL ░░ --}}
    <main class="main-content">

        {{-- HEADER INSTITUCIONAL --}}
        <header class="inst-header">
            <div class="inst-logo-block">
                <div class="inst-wordmark">
                    <span class="wm-ist">IST</span><span class="wm-pet">PET</span>
                </div>
                <div class="inst-divider"></div>
                <div class="inst-name">
                    <div class="inst-name-top">Instituto Superior Tecnológico Mayor</div>
                    <div class="inst-name-sub">Pedro Traversari</div>
                </div>
            </div>
            <div class="inst-badge">
                <span class="inst-badge-dot"></span>
                <span class="inst-badge-text">Sistema de Pedidos — Bar Institucional</span>
                <span class="inst-badge-dot" style="animation-delay:0.5s"></span>
            </div>
        </header>

        {{-- HEADLINE --}}
        <div class="headline-block">
            <div class="headline-eyebrow">
                <div class="eyebrow-line"></div>
                <span>Selecciona tu sede</span>
                <div class="eyebrow-line"></div>
            </div>
            <h1 class="headline-title">
                ¿A qué <em>sede</em><br>perteneces?
            </h1>
            <p class="headline-sub">Elige tu sede para ver el menú y hacer tu pedido</p>
        </div>

        {{-- CARDS DE SEDE --}}
        <div class="sedes-grid">
            @foreach($sedes as $i => $sede)
            <a href="{{ route('kiosco.menu', ['sede' => $sede->slug]) }}"
               class="sede-card sede-card-{{ $sede->slug }}"
               style="animation-delay: {{ $i * 0.12 + 0.35 }}s">

                {{-- Glow de fondo --}}
                <div class="card-bg-glow"></div>

                {{-- Patrón geométrico --}}
                <div class="card-pattern">
                    @for($r = 0; $r < 5; $r++)
                        <div class="pattern-row">
                            @for($c = 0; $c < 8; $c++)
                                <div class="pattern-dot"></div>
                            @endfor
                        </div>
                    @endfor
                </div>

                {{-- Línea superior dorada --}}
                <div class="card-topline"></div>

                {{-- Badge sede --}}
                <div class="card-badge">
                    <span>{{ strtoupper($sede->slug === 'instituto' ? 'Instituto' : 'Conducción') }}</span>
                </div>

                {{-- Número decorativo --}}
                <div class="card-number">0{{ $i + 1 }}</div>

                {{-- Ícono grande --}}
                <div class="card-icon-wrap">
                    <div class="card-icon-ring"></div>
                    <div class="card-icon flex items-center justify-center">
                        @if($sede->slug === 'instituto')
                            <svg class="w-12 h-12 text-amber-400 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333M4.5 21V10.333"/></svg>
                        @else
                            <svg class="w-12 h-12 text-blue-400 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.948c0-.621-.504-1.125-1.125-1.125H5.625c-.621 0-1.125.504-1.125 1.125v12.25"/></svg>
                        @endif
                    </div>
                </div>

                {{-- Texto --}}
                <div class="card-body">
                    <h2 class="card-title">
                        @if($sede->slug === 'instituto')
                            Instituto<br><span>Tecnológico</span>
                        @else
                            Escuela de<br><span>Conducción</span>
                        @endif
                    </h2>
                    <p class="card-desc">{{ $sede->descripcion }}</p>
                </div>

                {{-- CTA --}}
                <div class="card-cta">
                    <span>Entrar al menú</span>
                    <div class="cta-arrow">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>

                {{-- Shimmer hover --}}
                <div class="card-shimmer"></div>
            </a>
            @endforeach
        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="kiosco-footer">
        <span>ISTPET © {{ date('Y') }}</span>
        <span class="footer-sep">·</span>
        <span>Sistema de Pedidos v2.0</span>
    </footer>

    {{-- Botón admin oculto — solo visible si NO es usuario del kiosco --}}
    @if(!auth()->check() || !auth()->user()->hasRole('usuario'))
    <a href="{{ route('admin.dashboard') }}" class="admin-btn flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
        <span>Iniciar Sesión</span>
    </a>
    @endif

    {{-- Botón registro / sesión --}}
    @auth
        @if(auth()->user()->hasRole('usuario'))
        <button type="button" onclick="abrirMisPedidos()" class="mis-pedidos-btn flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Mis pedidos</span>
        </button>
        <div class="user-session-btn flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font-size:0.72rem;opacity:0.6;padding:0;margin-left:0.25rem;" title="Cerrar sesión">✕</button>
            </form>
        </div>
        @endif
    @else
        <a href="{{ route('register') }}" class="register-btn flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Registrarse</span>
        </a>
    @endauth

</div>

{{-- ═══════════ MODAL MIS PEDIDOS ═══════════ --}}
@auth
@if(auth()->user()->hasRole('usuario'))
<div id="modal-mis-pedidos" class="mp-modal-bg" style="display:none;" onclick="if(event.target===this)cerrarMisPedidos()">
    <div class="mp-modal" onclick="event.stopPropagation()">
        <div class="mp-sticky-top">
            <div class="mp-modal-header">
                <div>
                    <div class="mp-modal-title">Mis Pedidos</div>
                    <div class="mp-modal-sub">Historial de compras de {{ explode(' ', auth()->user()->name)[0] }}</div>
                </div>
                <button onclick="cerrarMisPedidos()" class="mp-modal-close">✕</button>
            </div>

            <div class="mp-filtros">
                <div class="mp-filtro-group">
                    <label class="mp-filtro-label">Método de pago</label>
                    <select id="mp-filtro-metodo" class="mp-filtro-select" onchange="aplicarFiltrosMisPedidos()">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="qr_deuna">QR DeUna</option>
                    </select>
                </div>
                <div class="mp-filtro-group">
                    <label class="mp-filtro-label">Desde</label>
                    <input type="date" id="mp-filtro-desde" class="mp-filtro-select" onchange="aplicarFiltrosMisPedidos()">
                </div>
                <div class="mp-filtro-group">
                    <label class="mp-filtro-label">Hasta</label>
                    <input type="date" id="mp-filtro-hasta" class="mp-filtro-select" onchange="aplicarFiltrosMisPedidos()">
                </div>
                <button type="button" class="mp-filtro-clear" onclick="limpiarFiltrosMisPedidos()" title="Quitar filtros">Limpiar</button>
            </div>
        </div>

        <div class="mp-modal-body" id="mp-modal-body">
            <div class="mp-loading">
                <div class="mp-spinner"></div>
                <span>Cargando tus pedidos…</span>
            </div>
        </div>
    </div>
</div>
@endif
@endauth
<style>
/* ═══════════════════════════════════════════════════
   VARIABLES
═══════════════════════════════════════════════════ */
:root {
    --gold:        #c9a84c;
    --gold-light:  #e2c47a;
    --gold-pale:   #f0d98a;
    --navy:        #080d1e;
    --navy-mid:    #0f1635;
    --navy-card:   #111a3a;
    --navy-light:  #1b2a6b;
    --white:       #ffffff;
    --white-40:    rgba(255,255,255,0.40);
    --white-20:    rgba(255,255,255,0.20);
    --white-08:    rgba(255,255,255,0.08);
    --gold-08:     rgba(201,168,76,0.08);
    --gold-20:     rgba(201,168,76,0.20);
    --gold-40:     rgba(201,168,76,0.40);
    --ease-bounce: cubic-bezier(0.34,1.56,0.64,1);
    --ease-out:    cubic-bezier(0.22,1,0.36,1);
}

/* ═══════════════════════════════════════════════════
   RESET & ROOT
═══════════════════════════════════════════════════ */
* { box-sizing: border-box; margin: 0; padding: 0; }

.kiosco-root {
    min-height: 100vh;
    background: var(--navy);
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    overflow: hidden;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ═══════════════════════════════════════════════════
   FONDO
═══════════════════════════════════════════════════ */
.bg-scene {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}

.bg-grid {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(201,168,76,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(201,168,76,0.04) 1px, transparent 1px);
    background-size: 52px 52px;
    mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 20%, transparent 80%);
}

.bg-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
}
.bg-orb-1 {
    width: 600px; height: 600px;
    top: -150px; left: -100px;
    background: radial-gradient(circle, rgba(27,42,107,0.7) 0%, transparent 70%);
}
.bg-orb-2 {
    width: 500px; height: 500px;
    bottom: -100px; right: -80px;
    background: radial-gradient(circle, rgba(27,42,107,0.5) 0%, transparent 70%);
}
.bg-orb-3 {
    width: 400px; height: 400px;
    top: 40%; left: 50%;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(201,168,76,0.07) 0%, transparent 70%);
    animation: orbPulse 6s ease-in-out infinite;
}
.bg-noise {
    position: absolute;
    inset: 0;
    opacity: 0.025;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 180px;
}
.bg-vignette {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 90% 90% at 50% 50%, transparent 50%, rgba(8,13,30,0.7) 100%);
}

/* ═══════════════════════════════════════════════════
   TOP BAR
═══════════════════════════════════════════════════ */
.top-bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    z-index: 10;
}
.top-bar-line {
    height: 100%;
    background: linear-gradient(90deg,
        transparent 0%,
        var(--gold) 20%,
        var(--gold-pale) 50%,
        var(--gold) 80%,
        transparent 100%);
}
.top-bar-glow {
    height: 20px;
    background: linear-gradient(to bottom, rgba(201,168,76,0.25), transparent);
}

/* ═══════════════════════════════════════════════════
   MAIN
═══════════════════════════════════════════════════ */
.main-content {
    position: relative;
    z-index: 5;
    width: 100%;
    max-width: 1040px;
    padding: 3.5rem 2.5rem 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2.5rem;
    flex: 1;
}

/* ═══════════════════════════════════════════════════
   HEADER INSTITUCIONAL
═══════════════════════════════════════════════════ */
.inst-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    animation: riseIn 0.7s var(--ease-out) both;
}

.inst-logo-block {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.inst-wordmark {
    font-weight: 900;
    font-size: 4.5rem;
    line-height: 1;
    letter-spacing: -0.03em;
    text-transform: uppercase;
}
.wm-ist { color: var(--gold); }
.wm-pet { color: var(--gold-light); }

.inst-divider {
    width: 1px;
    height: 68px;
    background: linear-gradient(to bottom, transparent, var(--gold-40) 30%, var(--gold-40) 70%, transparent);
}

.inst-name {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}
.inst-name-top {
    color: var(--white);
    font-size: 1.25rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    line-height: 1.2;
}
.inst-name-sub {
    color: var(--gold);
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.inst-badge {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.45rem 1.4rem;
    border-radius: 999px;
    background: var(--gold-08);
    border: 1px solid rgba(201,168,76,0.2);
    backdrop-filter: blur(12px);
}
.inst-badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--gold);
    animation: blink 2s ease-in-out infinite;
}
.inst-badge-text {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: rgba(255,255,255,0.38);
}

/* ═══════════════════════════════════════════════════
   HEADLINE
═══════════════════════════════════════════════════ */
.headline-block {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8rem;
    animation: riseIn 0.7s var(--ease-out) 0.1s both;
}

.headline-eyebrow {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--gold);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.22em;
}
.eyebrow-line {
    width: 36px;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gold));
}
.eyebrow-line:last-child {
    background: linear-gradient(90deg, var(--gold), transparent);
}

.headline-title {
    font-size: clamp(2.6rem, 5.5vw, 4.2rem);
    font-weight: 900;
    color: var(--white);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    line-height: 1.1;
    text-shadow: 0 6px 40px rgba(0,0,0,0.5);
    overflow: visible;
    padding-bottom: 0.1em;
}
.headline-title em {
    font-style: normal;
    color: var(--gold);
    position: relative;
}
.headline-title em::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
    border-radius: 3px;
    animation: lineGrow 0.8s var(--ease-out) 0.6s both;
    transform-origin: left;
}

.headline-sub {
    color: rgba(255,255,255,0.38);
    font-size: 1rem;
    letter-spacing: 0.02em;
}

/* ═══════════════════════════════════════════════════
   SEDES GRID
═══════════════════════════════════════════════════ */
.sedes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    width: 100%;
}

/* ═══════════════════════════════════════════════════
   CARD
═══════════════════════════════════════════════════ */
.sede-card {
    position: relative;
    border-radius: 1.75rem;
    overflow: hidden;
    text-decoration: none;
    border: 1.5px solid rgba(255,255,255,0.07);
    background: linear-gradient(155deg, #111d45 0%, #0d1535 60%, #080d1e 100%);
    display: flex;
    flex-direction: column;
    padding: 2rem 2rem 1.75rem;
    gap: 1.1rem;
    min-height: 320px;
    cursor: pointer;
    transition:
        transform 0.4s var(--ease-bounce),
        border-color 0.3s ease,
        box-shadow 0.4s ease;
    animation: cardIn 0.7s var(--ease-bounce) both;
    will-change: transform;
}

.sede-card:hover {
    transform: translateY(-10px) scale(1.02);
    border-color: rgba(201,168,76,0.55);
    box-shadow:
        0 32px 72px rgba(0,0,0,0.55),
        0 0 0 1px rgba(201,168,76,0.15),
        0 0 60px rgba(201,168,76,0.08);
}
.sede-card:hover .card-shimmer { opacity: 1; }
.sede-card:hover .card-cta { background: linear-gradient(135deg, #8a6a20, #c9a84c, #e2c47a); }
.sede-card:hover .cta-arrow { transform: translateX(4px); }
.sede-card:hover .card-icon { transform: scale(1.15) rotate(-5deg); }
.sede-card:hover .card-bg-glow { opacity: 1; }
.sede-card:active { transform: scale(0.98); }

/* Fondo glow interno */
.card-bg-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 60% at 80% 20%, rgba(201,168,76,0.1), transparent 65%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

/* Patrón puntos */
.card-pattern {
    position: absolute;
    top: 1rem; right: 1rem;
    display: flex;
    flex-direction: column;
    gap: 6px;
    opacity: 0.06;
    pointer-events: none;
}
.pattern-row { display: flex; gap: 6px; }
.pattern-dot {
    width: 3px; height: 3px;
    border-radius: 50%;
    background: var(--gold);
}

/* Línea superior */
.card-topline {
    position: absolute;
    top: 0; left: 1.5rem; right: 1.5rem;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--gold) 30%, var(--gold-light) 50%, var(--gold) 70%, transparent);
    border-radius: 0 0 2px 2px;
}

/* Badge */
.card-badge {
    position: absolute;
    top: 1.1rem;
    right: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.28rem 0.75rem;
    border-radius: 999px;
    background: rgba(201,168,76,0.1);
    border: 1px solid rgba(201,168,76,0.25);
    font-size: 0.6rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--gold-light);
}

/* Número decorativo */
.card-number {
    position: absolute;
    bottom: 1.5rem;
    right: 1.75rem;
    font-size: 6rem;
    font-weight: 900;
    line-height: 1;
    color: rgba(255,255,255,0.025);
    letter-spacing: -0.04em;
    pointer-events: none;
    user-select: none;
}

/* Ícono */
.card-icon-wrap {
    position: relative;
    width: 72px;
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.card-icon-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 1.5px solid rgba(201,168,76,0.2);
    background: rgba(201,168,76,0.06);
    transition: all 0.3s ease;
    z-index: 0;
}
.sede-card:hover .card-icon-ring {
    border-color: rgba(201,168,76,0.45);
    background: rgba(201,168,76,0.1);
    box-shadow: 0 0 24px rgba(201,168,76,0.2);
}
.card-icon {
    font-size: 2.2rem;
    line-height: 1;
    transition: transform 0.35s var(--ease-bounce);
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    position: relative;
    z-index: 1;
}

/* Cuerpo */
.card-body {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}
.card-title {
    font-size: 1.9rem;
    font-weight: 900;
    color: var(--white);
    text-transform: uppercase;
    letter-spacing: 0.02em;
    line-height: 1.0;
}
.card-title span {
    color: var(--gold);
}
.card-desc {
    color: rgba(255,255,255,0.42);
    font-size: 0.85rem;
    line-height: 1.6;
}

/* CTA */
.card-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    align-self: flex-start;
    padding: 0.75rem 1.5rem;
    border-radius: 0.875rem;
    background: linear-gradient(135deg, #7a5a18, #b8963e, #d4aa55);
    color: #0b1133;
    font-size: 0.82rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 6px 20px rgba(201,168,76,0.3);
}
.cta-arrow {
    display: flex;
    align-items: center;
    transition: transform 0.25s ease;
}

/* Shimmer */
.card-shimmer {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        105deg,
        transparent 30%,
        rgba(255,255,255,0.04) 50%,
        transparent 70%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

/* ═══════════════════════════════════════════════════
   FOOTER
═══════════════════════════════════════════════════ */
.kiosco-footer {
    position: relative;
    z-index: 5;
    padding: 1rem 0 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    color: rgba(255,255,255,0.1);
}
.footer-sep { color: var(--gold-20); }

/* ═══════════════════════════════════════════════════
   ADMIN BTN
═══════════════════════════════════════════════════ */
.admin-btn {
    position: fixed;
    bottom: 1.25rem;
    right: 1.25rem;
    z-index: 20;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1.1rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.65);
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    text-decoration: none;
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
}
.register-btn {
    position: fixed;
    bottom: 1.25rem;
    left: 1.25rem;
    padding: 0.5rem 1.1rem;
    border-radius: 999px;
    background: rgba(201,168,76,0.15);
    border: 1px solid rgba(201,168,76,0.5);
    color: #e2c47a;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 50;
    box-shadow: 0 4px 16px rgba(201,168,76,0.2);
}
.register-btn:hover {
    background: rgba(201,168,76,0.25);
    border-color: rgba(201,168,76,0.75);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(201,168,76,0.3);
}
.user-session-btn {
    position: fixed;
    bottom: 1.25rem;
    left: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 999px;
    background: rgba(201,168,76,0.1);
    border: 1px solid rgba(201,168,76,0.3);
    color: #c9a84c;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    z-index: 50;
}
.mis-pedidos-btn {
    position: fixed;
    bottom: 3.9rem;
    left: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 999px;
    background: rgba(99,130,246,0.12);
    border: 1px solid rgba(99,130,246,0.35);
    color: #a5b4fc;
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    cursor: pointer;
    z-index: 50;
    transition: all 0.2s ease;
}
.mis-pedidos-btn:hover {
    background: rgba(99,130,246,0.22);
    border-color: rgba(99,130,246,0.55);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99,130,246,0.25);
}

/* ═══════════════════════════════════════════════════
   MODAL MIS PEDIDOS
═══════════════════════════════════════════════════ */
.mp-modal-bg {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.78); backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center; padding: 1.5rem;
}
.mp-modal {
    width: 100%; max-width: 560px; max-height: 85vh; overflow-y: auto;
    background: #0f1430; border: 1px solid rgba(201,168,76,0.18);
    border-radius: 1.25rem; box-shadow: 0 32px 80px rgba(0,0,0,0.6);
    animation: mpModalIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
    scrollbar-width: thin; scrollbar-color: rgba(201,168,76,0.25) transparent;
}
@keyframes mpModalIn { from{opacity:0;transform:scale(0.94)} to{opacity:1;transform:scale(1)} }
.mp-sticky-top {
    position: sticky; top: 0; background: #0f1430; z-index: 2;
}
.mp-modal-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06);
}
.mp-modal-title { font-family:var(--font-display);font-weight:900;font-size:1.05rem;color:white;text-transform:uppercase;letter-spacing:0.04em; }
.mp-modal-sub   { font-size:0.72rem;color:rgba(255,255,255,0.4);margin-top:2px; }
.mp-modal-close { background:transparent;border:none;cursor:pointer;color:rgba(255,255,255,0.3);font-size:1.1rem;padding:0.25rem;transition:color 0.15s ease;line-height:1; }
.mp-modal-close:hover { color:white; }
.mp-modal-body { padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:0.85rem; }

.mp-filtros {
    display:flex;gap:0.6rem;align-items:flex-end;flex-wrap:wrap;
    padding:0.85rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.06);
    background:rgba(255,255,255,0.015);
}
.mp-filtro-group { display:flex;flex-direction:column;gap:0.25rem; }
.mp-filtro-label {
    font-size:0.58rem;font-weight:900;text-transform:uppercase;letter-spacing:0.1em;
    color:rgba(255,255,255,0.3);font-family:var(--font-display);
}
.mp-filtro-select {
    background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);
    border-radius:0.6rem;padding:0.4rem 0.6rem;font-size:0.74rem;color:white;
    font-family:inherit;color-scheme:dark;
}
.mp-filtro-select:focus { outline:none;border-color:rgba(201,168,76,0.4); }
.mp-filtro-clear {
    background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);
    color:#fca5a5;border-radius:0.6rem;padding:0.4rem 0.7rem;font-size:0.68rem;
    font-weight:700;cursor:pointer;transition:all 0.15s ease;white-space:nowrap;
}
.mp-filtro-clear:hover { background:rgba(239,68,68,0.18); }

.mp-loading {
    display:flex;flex-direction:column;align-items:center;gap:0.85rem;
    padding:3rem 0;color:rgba(255,255,255,0.4);font-size:0.8rem;font-weight:600;
}
.mp-spinner {
    width:32px;height:32px;border-radius:50%;
    border:3px solid rgba(201,168,76,0.15);border-top-color:#c9a84c;
    animation: mpSpin 0.8s linear infinite;
}
@keyframes mpSpin { to { transform: rotate(360deg); } }

.mp-empty {
    display:flex;flex-direction:column;align-items:center;gap:0.6rem;
    padding:3rem 1rem;text-align:center;
}
.mp-empty-icon { font-size:3rem;opacity:.15; }
.mp-empty-title { font-family:var(--font-display);font-weight:900;font-size:0.95rem;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.04em; }
.mp-empty-sub { font-size:0.78rem;color:rgba(255,255,255,0.25); }

.mp-card {
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);
    border-radius: 1rem; padding: 1rem 1.1rem; transition: border-color 0.15s ease;
}
.mp-card:hover { border-color: rgba(201,168,76,0.25); }
.mp-card-top {
    display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;margin-bottom:0.6rem;
}
.mp-card-codigo { font-family:var(--font-display);font-weight:900;font-size:0.85rem;color:#e2c47a;letter-spacing:0.03em; }
.mp-card-fecha { font-size:0.68rem;color:rgba(255,255,255,0.35);margin-top:2px; }
.mp-card-total { font-family:var(--font-display);font-weight:900;font-size:1.15rem;color:white;white-space:nowrap; }

.mp-badge {
    display:inline-flex;align-items:center;gap:0.3rem;
    padding:0.2rem 0.65rem;border-radius:999px;
    font-size:0.6rem;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;
    font-family:var(--font-display);white-space:nowrap;
}
.mp-badge-yellow { background:rgba(250,204,21,0.12);border:1px solid rgba(250,204,21,0.3);color:#facc15; }
.mp-badge-blue   { background:rgba(99,130,246,0.12);border:1px solid rgba(99,130,246,0.3);color:#a5b4fc; }
.mp-badge-green  { background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);color:#6ee7b7; }
.mp-badge-gray   { background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.5); }
.mp-badge-red    { background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#fca5a5; }

.mp-card-items {
    display:flex;flex-direction:column;gap:0.3rem;
    padding-top:0.6rem;border-top:1px solid rgba(255,255,255,0.06);
}
.mp-item-row {
    display:flex;justify-content:space-between;font-size:0.74rem;color:rgba(255,255,255,0.55);
}
.mp-item-name { display:flex;gap:0.4rem; }
.mp-item-qty { color:#c9a84c;font-weight:700; }
.mp-card-meta {
    display:flex;gap:0.5rem;margin-top:0.65rem;font-size:0.65rem;color:rgba(255,255,255,0.3);
    text-transform:uppercase;letter-spacing:0.06em;font-weight:700;
}

@media (max-width: 600px) {
    .mp-modal-body, .mp-modal-header { padding-left:1rem;padding-right:1rem; }
    .mp-filtros { padding-left:1rem;padding-right:1rem; }
    .mp-filtro-group { flex:1 1 auto;min-width:100px; }
}
.admin-btn:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    border-color: rgba(255,255,255,0.35);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* ═══════════════════════════════════════════════════
   ANIMACIONES
═══════════════════════════════════════════════════ */
@keyframes riseIn {
    from { opacity:0; transform:translateY(24px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(36px) scale(0.94); }
    to   { opacity:1; transform:translateY(0) scale(1); }
}
@keyframes lineGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
}
@keyframes blink {
    0%,100% { opacity:1; transform:scale(1); }
    50%     { opacity:0.35; transform:scale(0.7); }
}
@keyframes orbPulse {
    0%,100% { transform:translate(-50%,-50%) scale(1); opacity:1; }
    50%     { transform:translate(-50%,-50%) scale(1.15); opacity:0.7; }
}

/* ════════════════════════════
   RESPONSIVE INICIO KIOSCO
════════════════════════════ */
@media (max-width: 767px) {
    .main-content {
        padding: 2rem 1.1rem 1.25rem !important;
        gap: 1.5rem !important;
    }
    .inst-logo-block {
        gap: 0.85rem !important;
        flex-wrap: wrap;
        justify-content: center;
    }
    .inst-wordmark { font-size: 2.8rem !important; }
    .inst-divider  { height: 40px !important; }
    .inst-name-top { font-size: 0.82rem !important; }
    .inst-name-sub { font-size: 0.65rem !important; }
    .inst-badge    { padding: 0.35rem 0.9rem !important; }
    .inst-badge-text { font-size: 0.58rem !important; letter-spacing: 0.12em !important; }
    .headline-title { font-size: clamp(1.75rem, 8.5vw, 2.5rem) !important; }
    .headline-sub   { font-size: 0.85rem !important; }
    .sedes-grid {
        grid-template-columns: 1fr !important;
        gap: 0.9rem !important;
    }
    .sede-card {
        min-height: 200px !important;
        padding: 1.25rem !important;
        gap: 0.75rem !important;
    }
    .card-title  { font-size: 1.4rem !important; }
    .card-desc   { font-size: 0.78rem !important; }
    .card-icon-wrap { width: 56px !important; height: 56px !important; }
    .card-icon   { font-size: 1.7rem !important; }
    .card-cta    { padding: 0.6rem 1.1rem !important; font-size: 0.75rem !important; }
    .card-number { font-size: 4rem !important; }
    .admin-btn, .register-btn, .mis-pedidos-btn {
        font-size: 0.6rem !important;
        padding: 0.4rem 0.8rem !important;
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .sedes-grid { gap: 1rem !important; }
    .sede-card  { min-height: 260px !important; }
    .inst-wordmark { font-size: 3.5rem !important; }
    .headline-title { font-size: clamp(2rem, 4vw, 3rem) !important; }
}

</style>

@push('scripts')
<script>
// Al volver a la pantalla de selección de sede (inicio del kiosco), se
// limpia cualquier rastro de "categoría activa" guardado por un cliente
// anterior, para que el siguiente cliente siempre arranque viendo "Todos"
// cuando entre al menú.
sessionStorage.removeItem('istpet-kiosco-cat-activa');
</script>
@endpush

@auth
@if(auth()->user()->hasRole('usuario'))
@push('scripts')
<script>
const MIS_PEDIDOS_ROUTE = '{{ route("kiosco.mis-pedidos") }}';
const BADGE_COLOR_MAP = { yellow:'mp-badge-yellow', blue:'mp-badge-blue', green:'mp-badge-green', gray:'mp-badge-gray', red:'mp-badge-red' };
const METODO_LABEL_MAP = { efectivo: 'Efectivo', qr_deuna: 'QR DeUna' };

let _misPedidosData = [];
let _pollingInterval = null;

// Mensajes de notificación por cambio de estado
const ESTADO_NOTIF = {
    pagado:    { msg: 'Tu pedido fue cobrado. Espera el llamado para recogerlo.', color: '#10b981' },
    entregado: { msg: 'Tu pedido está listo. Pasa a recogerlo.',                 color: '#6366f1' },
    cancelado: { msg: 'Tu pedido fue cancelado. Consulta en el mostrador.',        color: '#ef4444' },
};

function abrirMisPedidos() {
    document.getElementById('modal-mis-pedidos').style.display = 'flex';
    cargarMisPedidos();
    iniciarPolling();
}

function cerrarMisPedidos() {
    document.getElementById('modal-mis-pedidos').style.display = 'none';
    detenerPolling();
}

function iniciarPolling() {
    detenerPolling(); // evitar duplicados
    _pollingInterval = setInterval(async () => {
        const modal = document.getElementById('modal-mis-pedidos');
        if (modal.style.display === 'none') { detenerPolling(); return; }
        await actualizarSilencioso();
    }, 8000); // cada 8 segundos
}

function detenerPolling() {
    if (_pollingInterval) { clearInterval(_pollingInterval); _pollingInterval = null; }
}

async function cargarMisPedidos() {
    const body = document.getElementById('mp-modal-body');
    body.innerHTML = `<div class="mp-empty"><div class="mp-empty-icon" style="font-size:1.8rem;animation:spin 1s linear infinite;"><svg class="w-8 h-8 mx-auto text-gray-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div></div>`;
    try {
        const res  = await fetch(MIS_PEDIDOS_ROUTE, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        _misPedidosData = data.pedidos || [];
        pintarListaMisPedidos(_misPedidosData);
    } catch (e) {
        body.innerHTML = `
            <div class="mp-empty">
                <div class="mp-empty-icon flex justify-center"><svg class="w-10 h-10 text-amber-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg></div>
                <div class="mp-empty-title">No se pudo cargar</div>
                <div class="mp-empty-sub">Intenta abrir el historial nuevamente.</div>
            </div>`;
    }
}

async function actualizarSilencioso() {
    try {
        const res  = await fetch(MIS_PEDIDOS_ROUTE, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const nuevos = data.pedidos || [];

        // Detectar cambios de estado para notificar
        nuevos.forEach(nuevo => {
            const anterior = _misPedidosData.find(p => p.codigo === nuevo.codigo);
            if (anterior && anterior.estado !== nuevo.estado && ESTADO_NOTIF[nuevo.estado]) {
                mostrarToast(ESTADO_NOTIF[nuevo.estado].msg, ESTADO_NOTIF[nuevo.estado].color);
            }
        });

        _misPedidosData = nuevos;

        // Repintar respetando filtros activos
        const metodo = document.getElementById('mp-filtro-metodo').value;
        const desde  = document.getElementById('mp-filtro-desde').value;
        const hasta  = document.getElementById('mp-filtro-hasta').value;
        const hayFiltro = metodo || desde || hasta;
        if (hayFiltro) {
            aplicarFiltrosMisPedidos();
        } else {
            pintarListaMisPedidos(_misPedidosData);
        }
    } catch (e) { /* silencioso */ }
}

function mostrarToast(mensaje, color = '#10b981') {
    const toast = document.createElement('div');
    toast.textContent = mensaje;
    toast.style.cssText = `
        position:fixed; bottom:2rem; left:50%; transform:translateX(-50%);
        background:${color}; color:#fff; font-weight:700; font-size:0.9rem;
        padding:1rem 1.5rem; border-radius:1rem; z-index:99999;
        box-shadow:0 8px 30px rgba(0,0,0,0.4); max-width:90vw; text-align:center;
        animation:toast-in 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.4s';
        setTimeout(() => toast.remove(), 400);
    }, 5000);
}

function aplicarFiltrosMisPedidos() {
    const metodo = document.getElementById('mp-filtro-metodo').value;
    const desde  = document.getElementById('mp-filtro-desde').value;
    const hasta  = document.getElementById('mp-filtro-hasta').value;

    const filtrados = _misPedidosData.filter(p => {
        if (metodo && p.metodo_pago !== metodo) return false;
        if (desde && p.fecha_iso < desde) return false;
        if (hasta && p.fecha_iso > hasta) return false;
        return true;
    });

    pintarListaMisPedidos(filtrados, true);
}

function limpiarFiltrosMisPedidos() {
    document.getElementById('mp-filtro-metodo').value = '';
    document.getElementById('mp-filtro-desde').value  = '';
    document.getElementById('mp-filtro-hasta').value  = '';
    pintarListaMisPedidos(_misPedidosData);
}

function pintarListaMisPedidos(lista, esFiltro = false) {
    const body = document.getElementById('mp-modal-body');

    if (!lista || lista.length === 0) {
        body.innerHTML = esFiltro ? `
            <div class="mp-empty">
                <div class="mp-empty-icon flex justify-center"><svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg></div>
                <div class="mp-empty-title">Sin resultados</div>
                <div class="mp-empty-sub">No hay pedidos que coincidan con esos filtros.</div>
            </div>` : `
            <div class="mp-empty">
                <div class="mp-empty-icon flex justify-center"><svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/></svg></div>
                <div class="mp-empty-title">Aún no tienes pedidos</div>
                <div class="mp-empty-sub">Cuando hagas tu primera compra aparecerá aquí.</div>
            </div>`;
        return;
    }

    body.innerHTML = lista.map(renderPedidoCard).join('');
}

function renderPedidoCard(p) {
    const badgeClass = BADGE_COLOR_MAP[p.estado_badge?.color] || 'mp-badge-gray';
    const itemsHtml = (p.items || []).map(i => `
        <div class="mp-item-row">
            <span class="mp-item-name"><span class="mp-item-qty">${i.cantidad}×</span> ${i.nombre}</span>
            <span>$${parseFloat(i.subtotal).toFixed(2)}</span>
        </div>
    `).join('');

    return `
    <div class="mp-card">
        <div class="mp-card-top">
            <div>
                <div class="mp-card-codigo">${p.codigo}</div>
                <div class="mp-card-fecha">${p.fecha} · ${p.hora}</div>
            </div>
            <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:0.35rem;">
                <div class="mp-card-total">$${parseFloat(p.total).toFixed(2)}</div>
                <span class="mp-badge ${badgeClass}">${p.estado_badge?.label || p.estado}</span>
            </div>
        </div>
        <div class="mp-card-items">${itemsHtml}</div>
        <div class="mp-card-meta">
            <span>${METODO_LABEL_MAP[p.metodo_pago] || p.metodo_pago}</span>
            ${p.sede ? `<span>· ${p.sede}</span>` : ''}
        </div>
    </div>`;
}
</script>
@endpush
@endif
@endauth

<style>
@keyframes toast-in {
    from { opacity:0; transform:translateX(-50%) translateY(1rem); }
    to   { opacity:1; transform:translateX(-50%) translateY(0); }
}
@keyframes spin {
    to { transform:rotate(360deg); }
}
</style>

@endsection