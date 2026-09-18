<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Admin — ISTPET Bar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@500;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="h-full" style="background:#080d1e;overflow:hidden;">

{{-- ░░ FONDO ░░ --}}
<div class="login-bg">
    <div class="login-grid"></div>
    <div class="login-orb login-orb-1"></div>
    <div class="login-orb login-orb-2"></div>
    <div class="login-orb login-orb-3"></div>
    <div class="login-particles" id="particles"></div>
    <div class="login-vignette"></div>
</div>

{{-- ░░ LÍNEA SUPERIOR ░░ --}}
<div class="login-topbar">
    <div class="login-topbar-line"></div>
    <div class="login-topbar-glow"></div>
</div>

{{-- ░░ LAYOUT ░░ --}}
<div class="login-layout">

    {{-- ══ PANEL IZQUIERDO ══ --}}
    <div class="login-brand">

        {{-- Decoración esquina --}}
        <div class="brand-corner brand-corner-tl"></div>
        <div class="brand-corner brand-corner-br"></div>

        <div class="brand-inner">

            {{-- Logo --}}
            <div class="brand-logo-block">
                <div class="brand-logo-pill">
                    <div class="brand-wordmark">
                        <span class="bw-ist">IST</span><span class="bw-pet">PET</span>
                    </div>
                    <div class="brand-logo-sep"></div>
                    <div class="brand-logo-text">
                        <div class="blt-top">Bar Institucional</div>
                        <div class="blt-sub">Sistema de pedidos v2.0</div>
                    </div>
                </div>
            </div>

            {{-- Headline --}}
            <div class="brand-headline">
                <div class="brand-eyebrow">
                    <div class="eyebrow-line"></div>
                    <span>Administración</span>
                    <div class="eyebrow-line"></div>
                </div>
                <h1 class="brand-title">
                    Panel de<br><em>Control</em>
                </h1>
                <p class="brand-desc">
                    Accede al sistema con tu cuenta de administrador o con tu cuenta de estudiante/usuario del kiosco.
                </p>
            </div>

            {{-- User type cards --}}
            <div style="display:flex;flex-direction:column;gap:0.75rem;">

                {{-- Card Admin --}}
                <div class="feature-card feature-card-1" style="border-color:rgba(201,168,76,0.15);background:rgba(201,168,76,0.04);">
                    <div class="feature-icon flex items-center justify-center text-amber-400">
                        <x-admin.icon name="lock" class="w-5 h-5" />
                    </div>
                    <div style="flex:1;">
                        <div class="feature-title" style="color:#c9a84c;">Personal del Bar / Admin</div>
                        <div class="feature-sub">Superadmin · Admin · Cajero · Visor</div>
                        <div style="font-size:0.6rem;color:rgba(255,255,255,0.2);margin-top:3px;">Gestiona pedidos, stock, caja y reportes</div>
                    </div>
                    <span style="font-size:0.6rem;font-family:var(--font-display);font-weight:900;text-transform:uppercase;letter-spacing:0.08em;color:rgba(201,168,76,0.5);border:1px solid rgba(201,168,76,0.2);padding:2px 8px;border-radius:999px;white-space:nowrap;">Admin</span>
                </div>

                {{-- Card Usuario --}}
                <div class="feature-card feature-card-2" style="border-color:rgba(99,130,246,0.15);background:rgba(99,130,246,0.04);">
                    <div class="feature-icon flex items-center justify-center text-indigo-300">
                        <x-admin.icon name="user" class="w-5 h-5" />
                    </div>
                    <div style="flex:1;">
                        <div class="feature-title" style="color:#a5b4fc;">Estudiantes / Usuarios</div>
                        <div class="feature-sub">Acceso al módulo de fiados y beneficios</div>
                        <div style="font-size:0.6rem;color:rgba(255,255,255,0.2);margin-top:3px;">¿No tienes cuenta? Regístrate gratis</div>
                    </div>
                    <a href="{{ route('register') }}" onclick="event.stopPropagation()"
                       style="font-size:0.6rem;font-family:var(--font-display);font-weight:900;text-transform:uppercase;letter-spacing:0.08em;color:rgba(165,180,252,0.7);border:1px solid rgba(99,130,246,0.2);padding:2px 8px;border-radius:999px;white-space:nowrap;text-decoration:none;transition:all 0.2s;"
                       onmouseover="this.style.color='#a5b4fc';this.style.borderColor='rgba(99,130,246,0.5)'"
                       onmouseout="this.style.color='rgba(165,180,252,0.7)';this.style.borderColor='rgba(99,130,246,0.2)'">
                        Registrarse →
                    </a>
                </div>

                {{-- Info --}}
                <div style="padding:0.65rem 0.85rem;border-radius:0.75rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);">
                    <div style="font-size:0.62rem;color:rgba(255,255,255,0.2);line-height:1.6;">
                        <strong style="color:rgba(255,255,255,0.35);">Tip:</strong> Si eres estudiante y aún no tienes cuenta,
                        usa el botón <em style="color:#a5b4fc;">Registrarse</em> para crear tu perfil con tu cédula y carrera.
                    </div>
                </div>
            </div>

            {{-- Sedes --}}
            <div class="brand-sedes">
                <div class="sede-chip flex items-center gap-1.5">
                    <x-admin.icon name="instituto" class="w-3.5 h-3.5 text-amber-400" />
                    <span>Instituto Tecnológico</span>
                </div>
                <div class="sede-chip flex items-center gap-1.5">
                    <x-admin.icon name="car" class="w-3.5 h-3.5 text-blue-400" />
                    <span>Escuela de Conducción</span>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ PANEL DERECHO ══ --}}
    <div class="login-form-panel">
        <div class="login-card">

            {{-- Badge --}}
            <div class="login-badge">
                <span class="login-badge-dot"></span>
                <span>Acceso seguro</span>
                <span class="login-badge-dot" style="animation-delay:.6s"></span>
            </div>

            {{-- Ícono animado --}}
            <div class="login-icon-wrap">
                <div class="login-icon-ring login-icon-ring-outer"></div>
                <div class="login-icon-ring login-icon-ring-inner"></div>
                <div class="login-icon flex items-center justify-center text-amber-400">
                    <x-admin.icon name="lock" class="w-6 h-6" />
                </div>
            </div>

            <h2 class="login-title">Iniciar sesión</h2>
            <p class="login-desc">Estudiante, personal o administrador — todos aquí</p>

            {{-- Errores --}}
            @if($errors->any())
            <div class="login-error flex items-start gap-2">
                <x-admin.icon name="alert" class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" />
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="login-fields">
                @csrf

                <div class="field-group">
                    <label class="field-label">Correo electrónico</label>
                    <div class="field-wrap">
                        <span class="field-icon flex items-center justify-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="field-input" placeholder="admin@istpet.edu.ec"
                               pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                               title="Ingresa un correo válido con @ y dominio (ej: usuario@istpet.edu.ec)"
                               required autofocus autocomplete="email">
                        <div class="field-focus-bar"></div>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Contraseña</label>
                    <div class="field-wrap">
                        <span class="field-icon flex items-center justify-center text-gray-400">
                            <x-admin.icon name="lock" class="w-4 h-4" />
                        </span>
                        <input type="password" name="password" id="pwd-input"
                               class="field-input" placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" class="field-eye" onclick="togglePwd()" tabindex="-1"
                                title="Mostrar/ocultar contraseña">
                            <span id="eye-icon" class="flex items-center justify-center text-gray-400">
                                <x-admin.icon name="eye" class="w-4 h-4" />
                            </span>
                        </button>
                        <div class="field-focus-bar"></div>
                    </div>
                </div>

                {{-- Remember me mejorado --}}
                <label class="remember-toggle">
                    <input type="checkbox" name="remember" id="remember-check" class="remember-hidden">
                    <div class="remember-track" id="remember-track">
                        <div class="remember-thumb"></div>
                    </div>
                    <span class="remember-label-text">Mantener sesión iniciada</span>
                </label>

                <button type="submit" class="login-btn" id="login-btn">
                    <span id="btn-text">Ingresar al Panel</span>
                    <div id="btn-spinner" class="btn-spinner" style="display:none;">
                        <div class="spinner-ring"></div>
                    </div>
                    <svg id="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </form>

            <div class="login-footer" style="display:flex;flex-direction:column;gap:0.5rem;align-items:center;">
                <a href="{{ route('kiosco.inicio') }}" class="login-back-link">
                    ← Volver al kiosco
                </a>
                <span style="font-size:0.72rem;color:rgba(255,255,255,0.15);">
                    ¿Estudiante sin cuenta?
                    <a href="{{ route('register') }}" style="color:#a5b4fc;text-decoration:none;font-weight:700;" onmouseover="this.style.color='#c7d2fe'" onmouseout="this.style.color='#a5b4fc'">Regístrate aquí →</a>
                </span>
            </div>


        </div>
    </div>
</div>

<style>
:root {
    --gold: #c9a84c;
    --gold-l: #e2c47a;
    --gold-d: #9a7328;
    --navy: #080d1e;
    --navy-2: #0f1635;
    --ease-bounce: cubic-bezier(0.34,1.56,0.64,1);
    --ease-out: cubic-bezier(0.22,1,0.36,1);
    --font-display: 'Inter Tight', sans-serif;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Barlow', sans-serif; }

/* ══ FONDO ══ */
.login-bg { position:fixed;inset:0;z-index:0;pointer-events:none; }
.login-grid {
    position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(201,168,76,0.035) 1px,transparent 1px),
        linear-gradient(90deg,rgba(201,168,76,0.035) 1px,transparent 1px);
    background-size:48px 48px;
    mask-image:radial-gradient(ellipse 85% 85% at 50% 50%,black 20%,transparent 80%);
    animation: gridDrift 20s linear infinite;
}
@keyframes gridDrift {
    0%   { background-position: 0 0; }
    100% { background-position: 48px 48px; }
}
.login-orb { position:absolute;border-radius:50%;filter:blur(100px); }
.login-orb-1 { width:600px;height:600px;top:-160px;left:-120px;background:radial-gradient(circle,rgba(27,42,107,0.55),transparent 70%);animation:orbFloat1 12s ease-in-out infinite; }
.login-orb-2 { width:450px;height:450px;bottom:-100px;right:-60px;background:radial-gradient(circle,rgba(27,42,107,0.4),transparent 70%);animation:orbFloat2 15s ease-in-out infinite; }
.login-orb-3 { width:280px;height:280px;top:45%;left:42%;transform:translate(-50%,-50%);background:radial-gradient(circle,rgba(201,168,76,0.07),transparent 70%);animation:orbPulse 6s ease-in-out infinite; }
.login-vignette { position:absolute;inset:0;background:radial-gradient(ellipse 90% 90% at 50% 50%,transparent 40%,rgba(8,13,30,0.65) 100%); }
@keyframes orbFloat1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,20px)} }
@keyframes orbFloat2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,15px)} }
@keyframes orbPulse  { 0%,100%{transform:translate(-50%,-50%) scale(1);opacity:1} 50%{transform:translate(-50%,-50%) scale(1.2);opacity:.6} }

/* Partículas */
.login-particles { position:absolute;inset:0; }
.particle {
    position:absolute;border-radius:50%;
    background:var(--gold);
    animation:particleFloat linear infinite;
    opacity:0;
}
@keyframes particleFloat {
    0%   { transform:translateY(100vh) scale(0); opacity:0; }
    10%  { opacity:1; }
    90%  { opacity:0.6; }
    100% { transform:translateY(-10vh) scale(1); opacity:0; }
}

/* Top bar */
.login-topbar { position:fixed;top:0;left:0;right:0;z-index:10; }
.login-topbar-line { height:2px;background:linear-gradient(90deg,transparent,var(--gold) 20%,#f0d98a 50%,var(--gold) 80%,transparent);animation:lineShimmer 3s ease-in-out infinite; }
.login-topbar-glow { height:20px;background:linear-gradient(to bottom,rgba(201,168,76,0.22),transparent); }
@keyframes lineShimmer {
    0%,100% { opacity:1; }
    50%     { opacity:0.6; }
}

/* ══ LAYOUT ══ */
.login-layout { position:relative;z-index:5;min-height:100vh;display:flex; }

/* ══ PANEL IZQUIERDO ══ */
.login-brand {
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:3rem 3.5rem;
    position:relative;
    overflow:hidden;
    border-right:1px solid rgba(255,255,255,0.04);
}
.brand-corner {
    position:absolute;
    width:80px;height:80px;
    border:1.5px solid rgba(201,168,76,0.2);
    pointer-events:none;
}
.brand-corner-tl { top:2rem;left:2rem;border-right:none;border-bottom:none;border-radius:6px 0 0 0; }
.brand-corner-br { bottom:2rem;right:2rem;border-left:none;border-top:none;border-radius:0 0 6px 0; }

.brand-inner {
    max-width:460px;width:100%;
    display:flex;flex-direction:column;gap:2.2rem;
    animation:riseIn 0.9s var(--ease-out) both;
}

/* Logo pill */
.brand-logo-pill {
    display:inline-flex;align-items:center;gap:1rem;
    padding:0.75rem 1.25rem;
    border-radius:1rem;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.07);
    align-self:flex-start;
}
.brand-wordmark { font-family:var(--font-display);font-weight:900;font-size:2.4rem;line-height:1;letter-spacing:-0.02em; }
.bw-ist { color:var(--gold); }
.bw-pet { color:var(--gold-l); }
.brand-logo-sep { width:1px;height:40px;background:linear-gradient(to bottom,transparent,rgba(201,168,76,0.4) 30%,rgba(201,168,76,0.4) 70%,transparent); }
.blt-top { font-family:var(--font-display);font-weight:800;font-size:0.8rem;color:white;text-transform:uppercase;letter-spacing:0.06em;line-height:1.2; }
.blt-sub { font-size:0.62rem;color:var(--gold);font-weight:700;letter-spacing:0.1em;text-transform:uppercase;margin-top:3px; }

/* Headline */
.brand-eyebrow {
    display:flex;align-items:center;gap:0.65rem;
    color:var(--gold);font-size:0.65rem;font-weight:800;
    text-transform:uppercase;letter-spacing:0.22em;
    font-family:var(--font-display);
}
.eyebrow-line { flex:1;height:1px;background:linear-gradient(90deg,transparent,var(--gold)); }
.eyebrow-line:last-child { background:linear-gradient(90deg,var(--gold),transparent); }
.brand-title {
    font-family:var(--font-display);font-weight:900;
    font-size:clamp(2.8rem,4.5vw,4rem);
    color:white;text-transform:uppercase;
    letter-spacing:0.02em;line-height:0.95;
    margin-top:0.5rem;
}
.brand-title em { font-style:normal;color:var(--gold); }
.brand-desc { font-size:0.9rem;color:rgba(255,255,255,0.32);line-height:1.7;margin-top:0.5rem; }

/* Feature cards */
.brand-features {
    display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;
}
.feature-card {
    display:flex;align-items:center;gap:0.75rem;
    padding:0.85rem 1rem;
    border-radius:0.875rem;
    border:1px solid rgba(255,255,255,0.06);
    background:rgba(255,255,255,0.02);
    transition:border-color 0.2s ease,background 0.2s ease;
    animation:riseIn 0.8s var(--ease-out) both;
}
.feature-card:hover { border-color:rgba(201,168,76,0.2);background:rgba(201,168,76,0.04); }
.feature-card-1 { animation-delay:.15s; }
.feature-card-2 { animation-delay:.22s; }
.feature-card-3 { animation-delay:.29s; }
.feature-card-4 { animation-delay:.36s; }
.feature-icon { font-size:1.35rem;flex-shrink:0; }
.feature-title { font-family:var(--font-display);font-weight:800;font-size:0.8rem;color:white;letter-spacing:0.02em; }
.feature-sub { font-size:0.62rem;color:rgba(255,255,255,0.28);margin-top:1px; }

/* Sedes */
.brand-sedes { display:flex;gap:0.65rem;flex-wrap:wrap; }
.sede-chip {
    display:inline-flex;align-items:center;gap:0.4rem;
    padding:0.38rem 0.85rem;border-radius:999px;
    font-size:0.7rem;font-weight:700;
    background:rgba(201,168,76,0.06);
    border:1px solid rgba(201,168,76,0.15);
    color:rgba(255,255,255,0.4);
    font-family:var(--font-display);
    text-transform:uppercase;letter-spacing:0.06em;
    animation:riseIn 0.8s var(--ease-out) 0.45s both;
}

/* ══ PANEL DERECHO ══ */
.login-form-panel {
    width:420px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;
    padding:2rem 2.5rem;
    background:rgba(8,13,30,0.7);
    backdrop-filter:blur(24px);
    border-left:1px solid rgba(255,255,255,0.05);
}
.login-card {
    width:100%;display:flex;flex-direction:column;
    align-items:center;gap:1.1rem;
    animation:riseIn 0.9s var(--ease-out) 0.12s both;
}

/* Badge */
.login-badge {
    display:flex;align-items:center;gap:0.55rem;
    padding:0.38rem 1.1rem;border-radius:999px;
    background:rgba(201,168,76,0.07);border:1px solid rgba(201,168,76,0.2);
    font-size:0.6rem;font-weight:800;text-transform:uppercase;
    letter-spacing:0.18em;color:rgba(255,255,255,0.32);
    font-family:var(--font-display);
}
.login-badge-dot { width:5px;height:5px;border-radius:50%;background:var(--gold);animation:blink 2s ease-in-out infinite; }

/* Ícono */
.login-icon-wrap { position:relative;width:68px;height:68px;display:flex;align-items:center;justify-content:center; }
.login-icon-ring {
    position:absolute;border-radius:50%;
    border:1px solid rgba(201,168,76,0.25);
}
.login-icon-ring-outer { inset:0;animation:ringPulse 3s ease-in-out infinite; }
.login-icon-ring-inner { inset:8px;background:rgba(201,168,76,0.06);animation:ringPulse 3s ease-in-out infinite reverse; }
.login-icon { font-size:1.9rem;position:relative;z-index:1;animation:iconBob 4s ease-in-out infinite; }
@keyframes ringPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(1.08)} }
@keyframes iconBob   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }

/* Títulos */
.login-title { font-family:var(--font-display);font-weight:900;font-size:1.65rem;color:white;text-transform:uppercase;letter-spacing:0.04em;text-align:center; }
.login-desc  { font-size:0.78rem;color:rgba(255,255,255,0.28);text-align:center;margin-top:-0.4rem; }

/* Error */
.login-error {
    width:100%;display:flex;gap:0.65rem;align-items:flex-start;
    padding:0.85rem 1rem;border-radius:0.75rem;
    background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);
    color:#fca5a5;font-size:0.82rem;
}

/* Campos */
.login-fields { width:100%;display:flex;flex-direction:column;gap:1rem; }
.field-group  { display:flex;flex-direction:column;gap:0.3rem; }
.field-label  { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.14em;color:rgba(255,255,255,0.25);font-family:var(--font-display); }
.field-wrap   { position:relative;display:flex;align-items:center; }
.field-icon   { position:absolute;left:0.85rem;font-size:0.9rem;pointer-events:none;z-index:1; }
.field-input {
    width:100%;padding:0.8rem 2.6rem 0.8rem 2.6rem;
    border-radius:0.75rem;font-size:0.85rem;color:white;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.08);
    transition:border-color 0.2s ease,box-shadow 0.2s ease,background 0.2s ease;
    font-family:'Barlow',sans-serif;
}
.field-input::placeholder { color:rgba(255,255,255,0.18); }
.field-input:focus {
    outline:none;
    background:rgba(201,168,76,0.04);
    border-color:rgba(201,168,76,0.4);
    box-shadow:0 0 0 3px rgba(201,168,76,0.08);
}
.field-focus-bar {
    position:absolute;bottom:0;left:12px;right:12px;height:2px;
    background:linear-gradient(90deg,var(--gold-d),var(--gold),var(--gold-l));
    border-radius:0 0 2px 2px;transform:scaleX(0);
    transition:transform 0.3s var(--ease-out);transform-origin:left;
}
.field-input:focus ~ .field-focus-bar { transform:scaleX(1); }
.field-eye { position:absolute;right:0.75rem;background:transparent;border:none;cursor:pointer;font-size:0.88rem;color:rgba(255,255,255,0.22);transition:color 0.15s ease;padding:0.25rem; }
.field-eye:hover { color:rgba(255,255,255,0.6); }

/* Toggle recordarme */
.remember-toggle {
    display:flex;align-items:center;gap:0.75rem;
    cursor:pointer;user-select:none;
    align-self:flex-start;
}
.remember-hidden { display:none; }
.remember-track {
    width:40px;height:22px;border-radius:999px;
    background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);
    position:relative;flex-shrink:0;
    transition:background 0.25s ease,border-color 0.25s ease,box-shadow 0.25s ease;
}
.remember-track.checked {
    background:rgba(201,168,76,0.25);
    border-color:rgba(201,168,76,0.5);
    box-shadow:0 0 12px rgba(201,168,76,0.2);
}
.remember-thumb {
    width:16px;height:16px;border-radius:50%;
    background:rgba(255,255,255,0.35);
    position:absolute;top:2px;left:2px;
    transition:transform 0.25s var(--ease-bounce),background 0.25s ease;
}
.remember-track.checked .remember-thumb {
    transform:translateX(18px);
    background:var(--gold);
}
.remember-label-text { font-size:0.78rem;color:rgba(255,255,255,0.32);font-weight:500; }

/* Botón */
.login-btn {
    width:100%;display:flex;align-items:center;justify-content:center;gap:0.6rem;
    padding:0.95rem 1.5rem;border-radius:0.875rem;border:none;cursor:pointer;
    font-family:var(--font-display);font-weight:900;font-size:0.88rem;
    text-transform:uppercase;letter-spacing:0.1em;color:#0a1020;
    background:linear-gradient(135deg,var(--gold-d) 0%,var(--gold) 50%,var(--gold-l) 100%);
    background-size:200% 100%;background-position:right center;
    box-shadow:0 8px 28px rgba(201,168,76,0.3),inset 0 1px 0 rgba(255,255,255,0.15);
    transition:transform 0.25s var(--ease-bounce),box-shadow 0.25s ease,background-position 0.4s ease;
    position:relative;overflow:hidden;
}
.login-btn::before {
    content:'';position:absolute;inset:0;
    background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 50%);
    pointer-events:none;
}
.login-btn:hover {
    transform:translateY(-3px);
    box-shadow:0 16px 40px rgba(201,168,76,0.42),inset 0 1px 0 rgba(255,255,255,0.2);
    background-position:left center;
}
.login-btn:active { transform:scale(0.98); }
.login-btn svg { transition:transform 0.2s ease; }
.login-btn:hover svg { transform:translateX(4px); }
.btn-spinner { display:none;align-items:center;justify-content:center; }
.spinner-ring {
    width:18px;height:18px;border-radius:50%;
    border:2.5px solid rgba(10,16,32,0.2);
    border-top-color:#0a1020;
    animation:spin 0.7s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }

/* Footer */
.login-footer { text-align:center; }
.login-back-link { font-size:0.72rem;color:rgba(255,255,255,0.15);text-decoration:none;transition:color 0.15s ease;font-weight:600; }
.login-back-link:hover { color:rgba(255,255,255,0.45); }

/* Dev badge */
.login-dev-badge {
    width:100%;padding:0.85rem 1rem;border-radius:0.75rem;
    background:rgba(201,168,76,0.04);border:1px solid rgba(201,168,76,0.12);
}
.dev-title { font-size:0.6rem;font-weight:900;text-transform:uppercase;letter-spacing:0.14em;color:var(--gold);font-family:var(--font-display);margin-bottom:0.5rem; }
.dev-rows { display:flex;flex-direction:column;gap:0.3rem; }
.dev-row  { display:flex;align-items:center;gap:0.5rem;font-size:0.7rem; }
.dev-role { color:rgba(255,255,255,0.25);min-width:58px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;font-size:0.6rem; }
.dev-cred { font-family:monospace;color:rgba(255,255,255,0.5);font-size:0.67rem;background:rgba(255,255,255,0.04);padding:2px 6px;border-radius:4px; }

/* Animaciones */
@keyframes riseIn { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }
@keyframes blink  { 0%,100%{opacity:1} 50%{opacity:0.28} }

/* Responsive */
@media (max-width:780px) {
    .login-brand { display:none; }
    .login-form-panel { width:100%; }
}
</style>

<script>
// Toggle password
function togglePwd() {
    const input = document.getElementById('pwd-input');
    const icon  = document.getElementById('eye-icon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    if (input.type === 'password') {
        icon.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    } else {
        icon.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>';
    }
}

// Toggle switch recordarme
(function() {
    const cb    = document.getElementById('remember-check');
    const track = document.getElementById('remember-track');
    if (!cb || !track) return;
    track.addEventListener('click', function() {
        cb.checked = !cb.checked;
        track.classList.toggle('checked', cb.checked);
    });
})();

// Spinner al enviar
document.querySelector('form').addEventListener('submit', function() {
    const btn    = document.getElementById('login-btn');
    const text   = document.getElementById('btn-text');
    const arrow  = document.getElementById('btn-arrow');
    const spinner= document.getElementById('btn-spinner');
    btn.disabled = true;
    text.textContent = 'Ingresando...';
    arrow.style.display  = 'none';
    spinner.style.display= 'flex';
});

// Partículas flotantes
(function() {
    const container = document.getElementById('particles');
    if (!container) return;
    for (let i = 0; i < 18; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 3 + 1.5;
        p.style.cssText = [
            `width:${size}px`,
            `height:${size}px`,
            `left:${Math.random() * 100}%`,
            `animation-duration:${Math.random() * 14 + 10}s`,
            `animation-delay:${Math.random() * 12}s`,
            `opacity:0`,
        ].join(';');
        container.appendChild(p);
    }
})();
</script>

</body>
</html>