@extends('layouts.kiosco')
@section('title', 'Método de Pago — ISTPET Bar')

@section('content')
<div class="min-h-screen flex flex-col" id="pago-bg"
     style="background: radial-gradient(circle, rgba(99,130,255,0.12) 1.2px, transparent 1.2px),
                        linear-gradient(160deg, #080d1e 0%, #0f1635 50%, #080d1e 100%);
            background-size: 18px 18px, 100% 100%;">

    {{-- Línea dorada top --}}
    <div class="h-px w-full"
         style="background: linear-gradient(90deg, transparent, #c9a84c 30%, #e2c47a 50%, #c9a84c 70%, transparent);"></div>

    {{-- ══ HEADER — mismo diseño que menú ══ --}}
    <header class="flex items-center justify-between px-6 py-3 flex-shrink-0"
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
        <div class="hidden sm:flex items-center gap-2 px-5 py-2 rounded-full"
             style="background: rgba(201,168,76,0.12); border: 1px solid rgba(201,168,76,0.35);">
            <div class="w-2 h-2 rounded-full" style="background:#c9a84c;"></div>
            <span class="font-display font-bold text-sm uppercase tracking-wider" style="color:#e2c47a;">
                {{ $sede->slug === 'instituto' ? '🏛️ Instituto Traversari' : '🚗 Escuela Conducción' }}
            </span>
        </div>

        {{-- Toggle tema --}}
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

        {{-- Reloj + volver --}}
        <div class="flex items-center gap-4">
            <span class="font-display font-bold text-lg hidden sm:inline"
                  style="color: rgba(255,255,255,0.65);" id="kiosco-clock"></span>
            <a href="{{ route('kiosco.menu', ['sede' => $sede->slug]) }}"
               style="display:flex;align-items:center;gap:0.4rem;padding:0.45rem 0.875rem;border-radius:9999px;
                      font-family:var(--font-display);font-weight:700;font-size:0.72rem;text-transform:uppercase;
                      letter-spacing:0.06em;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.15);
                      color:rgba(255,255,255,0.55);text-decoration:none;transition:all 0.2s;white-space:nowrap;"
               onmouseover="this.style.background='rgba(255,255,255,0.14)';this.style.color='white'"
               onmouseout="this.style.background='rgba(255,255,255,0.07)';this.style.color='rgba(255,255,255,0.55)'">
                ← Volver al menú
            </a>
        </div>
    </header>

    {{-- Contenido --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-6">
        <div class="w-full mx-auto" style="max-width:920px;">

            {{-- Pasos + Título --}}
            <div class="text-center mb-7">
                <div class="inline-flex items-center gap-2 mb-3">
                    <div class="paso-circulo w-8 h-8 rounded-full flex items-center justify-center font-display font-black text-sm"
                        style="background:rgba(201,168,76,0.12);border:1px solid rgba(201,168,76,0.35);color:#c9a84c;">1</div>
                    <span class="paso-label font-display font-bold text-xs uppercase tracking-wider paso-text-muted">Elegir</span>
                    <div class="paso-linea w-8 h-px paso-line"></div>
                    <div class="paso-circulo w-8 h-8 rounded-full flex items-center justify-center font-display font-black text-sm"
                        style="background:linear-gradient(135deg,#a07d2e,#c9a84c);color:#0b1133;box-shadow:0 0 20px rgba(201,168,76,0.4);">2</div>
                    <span class="paso-label font-display font-bold text-xs uppercase tracking-wider" style="color:#c9a84c;">Pagar</span>
                    <div class="paso-linea w-8 h-px paso-line"></div>
                    <div class="paso-circulo w-8 h-8 rounded-full flex items-center justify-center font-display font-black text-sm paso-inactive">3</div>
                    <span class="paso-label font-display font-bold text-xs uppercase tracking-wider paso-text-inactive">Recibir</span>
                </div>
                <h1 class="font-display font-black uppercase titulo-pago"
                    style="font-size:clamp(2rem,3.5vw,2.8rem);letter-spacing:0.04em;">
                    ¿Cómo deseas <span style="color:#c9a84c;">pagar</span>?
                </h1>
            </div>

            {{-- GRID principal --}}
            @if(!$efectivoHabilitado && !$qrHabilitado)
            <div style="max-width:520px;margin:0 auto;text-align:center;
                        background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.09);
                        border-radius:1.5rem;padding:3rem 2.5rem;">
                <div style="font-size:4rem;margin-bottom:1rem;">🚫</div>
                <div class="font-display font-black uppercase titulo-pago"
                     style="font-size:1.4rem;letter-spacing:0.04em;margin-bottom:0.75rem;">
                    Sin métodos de pago
                </div>
                <p class="texto-desc" style="font-size:0.9rem;line-height:1.65;margin-bottom:2rem;">
                    Por el momento no hay métodos de pago disponibles en el kiosco.<br>
                    Por favor acércate al personal del bar para realizar tu pedido.
                </p>
                <a href="{{ route('kiosco.menu', ['sede' => $sede->slug]) }}"
                   class="btn-volver"
                   style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.9rem 2rem;
                          border-radius:0.875rem;font-family:var(--font-display);font-weight:900;
                          font-size:0.85rem;text-transform:uppercase;letter-spacing:0.05em;
                          text-decoration:none;transition:all 0.2s;">
                    ← Volver al menú
                </a>
            </div>
            @else
            @php
                $metodosCount = ($efectivoHabilitado ? 1 : 0) + ($qrHabilitado ? 1 : 0);
            @endphp

            <div class="pago-grid">

                {{-- ── EFECTIVO ── --}}
                @if($efectivoHabilitado)
                <div id="card-efectivo" onclick="selectMethod('efectivo',this)"
                    class="metodo-card"
                    style="position:relative;border-radius:1.5rem;cursor:pointer;
                            transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);
                            padding:1.75rem 1.5rem;
                            display:flex;flex-direction:column;align-items:center;text-align:center;gap:1rem;">
                    <div style="position:absolute;inset:0;border-radius:1.5rem;background:radial-gradient(circle at 70% 20%,rgba(251,191,36,0.05),transparent 65%);pointer-events:none;"></div>
                    <div class="method-check" style="display:none;position:absolute;top:1rem;right:1rem;
                                width:2rem;height:2rem;border-radius:50%;background:#c9a84c;color:#0b1133;
                                font-weight:900;font-size:1rem;align-items:center;justify-content:center;">✓</div>
                    <div style="width:5rem;height:5rem;border-radius:1rem;display:flex;align-items:center;
                                justify-content:center;font-size:2.5rem;
                                background:rgba(251,191,36,0.09);border:1px solid rgba(251,191,36,0.18);">💵</div>
                    <div>
                        <div class="font-display font-black uppercase titulo-pago" style="font-size:1.5rem;letter-spacing:0.03em;margin-bottom:0.35rem;">Efectivo</div>
                        <p class="texto-desc" style="font-size:0.85rem;line-height:1.55;">
                            Genera tu comprobante y cancela en caja al retirar tu pedido.
                        </p>
                    </div>
                    <div style="padding:0.4rem 1rem;border-radius:0.75rem;
                                background:rgba(251,191,36,0.07);border:1px solid rgba(251,191,36,0.18);">
                        <span style="font-size:0.7rem;color:#fbbf24;font-family:var(--font-display);font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">
                            💡 Pagas al retirar en caja
                        </span>
                    </div>
                </div>
                @endif

                {{-- ── QR DEUNA ── --}}
                @if($qrHabilitado)
                <div id="card-qr" onclick="selectMethod('qr',this)"
                    class="metodo-card"
                    style="position:relative;border-radius:1.5rem;cursor:pointer;
                            transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);
                            padding:1.75rem 1.5rem;
                            display:flex;flex-direction:column;align-items:center;text-align:center;gap:1rem;">
                    <div style="position:absolute;inset:0;border-radius:1.5rem;background:radial-gradient(circle at 70% 20%,rgba(59,130,246,0.05),transparent 65%);pointer-events:none;"></div>
                    <div class="method-check" style="display:none;position:absolute;top:1rem;right:1rem;
                                width:2rem;height:2rem;border-radius:50%;background:#c9a84c;color:#0b1133;
                                font-weight:900;font-size:1rem;align-items:center;justify-content:center;">✓</div>
                    <div style="width:5rem;height:5rem;border-radius:1rem;display:flex;align-items:center;
                                justify-content:center;font-size:2.5rem;
                                background:rgba(59,130,246,0.09);border:1px solid rgba(59,130,246,0.18);">📱</div>
                    <div>
                        <div class="font-display font-black uppercase titulo-pago" style="font-size:1.5rem;letter-spacing:0.03em;margin-bottom:0.35rem;">QR DeUna</div>
                        <p class="texto-desc" style="font-size:0.85rem;line-height:1.55;">
                            Escanea el código QR con tu app DeUna y transfiere al instante.
                        </p>
                    </div>
                    <div style="padding:0.4rem 1rem;border-radius:0.75rem;
                                background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.18);">
                        <span style="font-size:0.7rem;color:#93c5fd;font-family:var(--font-display);font-weight:800;text-transform:uppercase;letter-spacing:0.08em;">
                            📱 Verificación por el bar
                        </span>
                    </div>
                </div>
                @endif

                {{-- ── PANEL DERECHO ── --}}
                <div class="pago-panel panel-card" style="border-radius:1.5rem;overflow:hidden;">

                    {{-- DEFAULT --}}
                    <div id="panel-default" style="padding:1.25rem;">
                        <div class="font-display font-black" style="font-size:0.68rem;text-transform:uppercase;
                            letter-spacing:0.12em;color:rgba(255,255,255,0.3);margin-bottom:0.75rem;">📋 Tu pedido</div>
                        <div style="display:flex;flex-direction:column;gap:0.45rem;margin-bottom:0.75rem;">
                            @foreach($carrito as $item)
                            <div style="display:flex;justify-content:space-between;align-items:start;">
                                <div>
                                    <div class="font-display font-bold uppercase titulo-pago" style="font-size:0.82rem;line-height:1.2;">{{ $item['nombre'] }}</div>
                                    <div class="texto-desc" style="font-size:0.7rem;">${{ number_format($item['precio'],2) }} × {{ $item['cantidad'] }}</div>
                                </div>
                                <div class="font-display font-bold" style="color:#c9a84c;font-size:0.85rem;">${{ number_format($item['precio']*$item['cantidad'],2) }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="panel-divider" style="padding-top:0.6rem;margin-bottom:1rem;
                                    display:flex;justify-content:space-between;align-items:center;">
                            <span class="font-display font-black uppercase tracking-wider titulo-pago">Total</span>
                            <span class="font-display font-black" style="font-size:1.75rem;color:#c9a84c;">${{ number_format($total,2) }}</span>
                        </div>
                        <div style="padding:0.875rem;border-radius:0.875rem;margin-bottom:0.875rem;
                                    background:rgba(201,168,76,0.06);border:1px solid rgba(201,168,76,0.15);">
                            <div class="font-display font-black" style="font-size:0.62rem;text-transform:uppercase;letter-spacing:0.1em;color:rgba(201,168,76,0.7);margin-bottom:0.5rem;">👆 Selecciona un método</div>
                            @foreach(['Elige Efectivo o QR arriba','Sigue las instrucciones del panel','Confirma con el botón dorado'] as $i=>$s)
                            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.3rem;">
                                <span style="width:1.1rem;height:1.1rem;border-radius:50%;background:rgba(201,168,76,0.2);color:#c9a84c;
                                            font-size:0.6rem;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $i+1 }}</span>
                                <span class="texto-desc" style="font-size:0.75rem;">{{ $s }}</span>
                            </div>
                            @endforeach
                        </div>
                        <button disabled class="btn-disabled" style="width:100%;padding:0.9rem;border-radius:0.875rem;
                            cursor:not-allowed;font-family:var(--font-display);
                            font-weight:900;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.05em;">
                            Selecciona un método
                        </button>
                    </div>

                    {{-- EFECTIVO --}}
                    <div id="panel-efectivo" style="display:none;padding:1.25rem;">
                        <div class="font-display font-black" style="font-size:0.68rem;text-transform:uppercase;
                            letter-spacing:0.12em;color:rgba(251,191,36,0.7);margin-bottom:0.75rem;">💵 Pago en Efectivo</div>
                        @foreach([['🧾','Genera tu comprobante','Presiona confirmar para obtener tu número de pedido.'],['🏪','Ve a la caja','Acércate a la ventanilla con tu número de pedido.'],['💵','Cancela el valor','Entrega $'.number_format($total,2).' al personal del bar.'],['🍽️','Recibe tu pedido','El personal preparará tu pedido de inmediato.']] as $s)
                        <div class="paso-item" style="display:flex;align-items:start;gap:0.6rem;padding:0.55rem;border-radius:0.75rem;margin-bottom:0.4rem;">
                            <div style="width:2rem;height:2rem;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;
                                        font-size:1rem;flex-shrink:0;background:rgba(251,191,36,0.08);">{{ $s[0] }}</div>
                            <div>
                                <div class="font-display font-bold uppercase titulo-pago" style="font-size:0.72rem;">{{ $s[1] }}</div>
                                <div class="texto-desc" style="font-size:0.68rem;line-height:1.4;">{{ $s[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                        <div class="panel-divider" style="padding-top:0.65rem;margin:0.75rem 0;
                                    display:flex;justify-content:space-between;align-items:center;">
                            <span class="font-display font-black uppercase titulo-pago" style="font-size:0.85rem;">Total a pagar</span>
                            <span class="font-display font-black" style="font-size:1.75rem;color:#c9a84c;">${{ number_format($total,2) }}</span>
                        </div>
                        <form action="{{ route('kiosco.pedido.store') }}" method="POST" id="form-efectivo">
                            @csrf
                            <input type="hidden" name="metodo_pago" value="efectivo">
                            <button type="button" onclick="confirmarPago('efectivo')"
                                    style="width:100%;padding:1rem;border-radius:0.875rem;border:none;cursor:pointer;
                                        background:linear-gradient(135deg,#a07d2e,#c9a84c,#e2c47a);
                                        color:#0b1133;font-family:var(--font-display);font-weight:900;
                                        font-size:0.9rem;text-transform:uppercase;letter-spacing:0.05em;
                                        box-shadow:0 8px 28px rgba(201,168,76,0.35);">
                                💵 Confirmar — Ir a pagar en caja
                            </button>
                        </form>
                    </div>

                    {{-- QR --}}
                    <div id="panel-qr" style="display:none;padding:1.25rem;">
                        <div class="font-display font-black" style="font-size:0.68rem;text-transform:uppercase;
                            letter-spacing:0.12em;color:rgba(147,197,253,0.7);margin-bottom:0.75rem;">📱 Pago con QR DeUna</div>
                        <div class="panel-inner-box" style="border-radius:0.875rem;padding:0.65rem 0.75rem;margin-bottom:0.75rem;">
                            @foreach($carrito as $item)
                            <div style="display:flex;justify-content:space-between;padding:0.2rem 0;">
                                <span class="texto-desc" style="font-size:0.75rem;">{{ $item['nombre'] }} ×{{ $item['cantidad'] }}</span>
                                <span style="font-size:0.75rem;color:#c9a84c;font-weight:700;">${{ number_format($item['precio']*$item['cantidad'],2) }}</span>
                            </div>
                            @endforeach
                            <div class="panel-divider" style="margin-top:0.4rem;padding-top:0.4rem;
                                        display:flex;justify-content:space-between;">
                                <span class="font-display font-black uppercase titulo-pago" style="font-size:0.75rem;">Total</span>
                                <span class="font-display font-black" style="color:#c9a84c;font-size:1rem;">${{ number_format($total,2) }}</span>
                            </div>
                        </div>
                        <div style="text-align:center;padding:0.6rem;border-radius:0.875rem;margin-bottom:0.75rem;
                                    background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);">
                            <div style="font-size:0.62rem;color:rgba(201,168,76,0.6);font-family:var(--font-display);
                                        font-weight:800;text-transform:uppercase;letter-spacing:0.1em;">Monto a transferir</div>
                            <div class="font-display font-black" style="font-size:1.75rem;color:#c9a84c;">${{ number_format($total,2) }}</div>
                        </div>
                        <div style="padding:0.5rem 0.75rem;border-radius:0.75rem;margin-bottom:0.75rem;
                                    background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.18);">
                            <p style="font-size:0.80rem;color:rgba(239,68,68,0.75);line-height:1.6;text-align:center;">
                                ⚠️ El personal verificará tu pago antes de entregar el pedido.
                            </p>
                        </div>
                        <button type="button" onclick="mostrarQR()"
                                style="width:100%;padding:1rem;border-radius:0.875rem;border:none;cursor:pointer;
                                    background:linear-gradient(135deg,#1e3a8a,#3b82f6);
                                    color:white;font-family:var(--font-display);font-weight:900;
                                    font-size:0.9rem;text-transform:uppercase;letter-spacing:0.04em;
                                    box-shadow:0 8px 28px rgba(59,130,246,0.3);transition:all 0.2s;">
                            📱 Mostrar código QR para pagar
                        </button>
                        <form action="{{ route('kiosco.pedido.store') }}" method="POST" id="form-qr" style="display:none;">
                            @csrf
                            <input type="hidden" name="metodo_pago" value="qr_deuna">
                        </form>
                    </div>

                </div>
            </div>
            @endif

            @if($errors->any())
            <div style="margin-top:1rem;padding:1rem;border-radius:1rem;text-align:center;
                        background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);">
                @foreach($errors->all() as $error)
                    <p style="color:rgb(252,165,165);font-size:0.875rem;">{{ $error }}</p>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ══ POPUP QR ══ --}}
    <div id="popup-qr" style="display:none;position:fixed;inset:0;z-index:9998;
                               align-items:center;justify-content:center;
                               background:rgba(0,0,0,0.85);backdrop-filter:blur(14px);">
        <div style="background:linear-gradient(145deg,#1a1f30,#0f1430);
                    border:1px solid rgba(201,168,76,0.35);border-radius:2rem;
                    padding:2.5rem 2rem;max-width:400px;width:92%;text-align:center;
                    box-shadow:0 32px 80px rgba(0,0,0,0.7),0 0 0 1px rgba(201,168,76,0.1);
                    animation:scaleIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;">
            <div class="font-display font-black text-white uppercase" style="font-size:1.4rem;letter-spacing:0.04em;margin-bottom:0.35rem;">
                📱 Escanea con DeUna
            </div>
            <p style="color:rgba(255,255,255,0.4);font-size:0.82rem;margin-bottom:1.5rem;">
                Abre tu app DeUna → Escanear QR → Confirma <strong style="color:#c9a84c;">${{ number_format($total,2) }}</strong>
            </p>
            <div style="display:flex;justify-content:center;margin-bottom:1.5rem;">
                <div style="position:relative;">
                    <div style="width:220px;height:220px;border-radius:1.25rem;overflow:hidden;
                                background:white;padding:0.75rem;
                                box-shadow:0 0 0 2px rgba(201,168,76,0.6),0 12px 40px rgba(0,0,0,0.6);">
                        @if($qrActivo && $qrActivo->qr_url)
                            <img src="{{ $qrActivo->qr_url }}" alt="QR DeUna"
                                 style="width:100%;height:100%;object-fit:contain;"
                                 draggable="false" oncontextmenu="return false">
                        @else
                            <img src="{{ asset('storage/qr/DEUNA.jpg') }}" alt="QR DeUna"
                                 style="width:100%;height:100%;object-fit:contain;"
                                 draggable="false" oncontextmenu="return false"
                                 onerror="this.parentElement.innerHTML='<div style=\'display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;\'><div style=\'font-size:3rem;\'>📱</div><div style=\'font-size:0.7rem;color:#9ca3af;font-weight:700;margin-top:0.5rem;\'>QR no configurado</div></div>'">
                        @endif
                    </div>
                    <div style="position:absolute;top:-4px;left:-4px;width:16px;height:16px;border-top:3px solid #c9a84c;border-left:3px solid #c9a84c;border-radius:4px 0 0 0;"></div>
                    <div style="position:absolute;top:-4px;right:-4px;width:16px;height:16px;border-top:3px solid #c9a84c;border-right:3px solid #c9a84c;border-radius:0 4px 0 0;"></div>
                    <div style="position:absolute;bottom:-4px;left:-4px;width:16px;height:16px;border-bottom:3px solid #c9a84c;border-left:3px solid #c9a84c;border-radius:0 0 0 4px;"></div>
                    <div style="position:absolute;bottom:-4px;right:-4px;width:16px;height:16px;border-bottom:3px solid #c9a84c;border-right:3px solid #c9a84c;border-radius:0 0 4px 0;"></div>
                </div>
            </div>
            <div style="padding:0.75rem;border-radius:1rem;margin-bottom:1.25rem;
                        background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);">
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.35);font-family:var(--font-display);
                             font-weight:800;text-transform:uppercase;letter-spacing:0.1em;">Titular de la cuenta</div>
                <div class="font-display font-black text-white" style="font-size:1rem;margin-top:0.15rem;">
                    <span id="titular-deuna-nombre">{{ $titularDeuna ?? $qrActivo->titular ?? 'Bar ISTPET' }}</span>
                </div>
            </div>
            <button type="button" onclick="yaTransferi()"
                    style="width:100%;padding:1.1rem;border-radius:1rem;border:none;cursor:pointer;
                           background:linear-gradient(135deg,#166534,#22c55e);
                           color:white;font-family:var(--font-display);font-weight:900;
                           font-size:0.95rem;text-transform:uppercase;letter-spacing:0.05em;
                           box-shadow:0 8px 28px rgba(34,197,94,0.35);margin-bottom:0.75rem;">
                ✅ Ya transferí el monto
            </button>
            <button type="button" onclick="cerrarQR()"
                    style="width:100%;padding:0.75rem;border-radius:1rem;border:1px solid rgba(255,255,255,0.1);
                           background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.4);cursor:pointer;
                           font-family:var(--font-display);font-weight:700;font-size:0.8rem;
                           text-transform:uppercase;letter-spacing:0.05em;">
                ← Cancelar / Volver
            </button>
        </div>
    </div>

    {{-- ══ POPUP TICKET ══ --}}
    <div id="popup-ticket" style="display:none;position:fixed;inset:0;z-index:9999;
                                   align-items:center;justify-content:center;
                                   background:rgba(0,0,0,0.88);backdrop-filter:blur(16px);">
        <div style="background:linear-gradient(145deg,#1e2130,#0f1635);
                    border:1px solid rgba(201,168,76,0.3);border-radius:2rem;
                    padding:3rem 2.5rem;max-width:420px;width:92%;text-align:center;
                    box-shadow:0 32px 80px rgba(0,0,0,0.7),0 0 0 1px rgba(201,168,76,0.1);
                    animation:scaleIn 0.4s cubic-bezier(0.34,1.56,0.64,1) both;">
            <div id="popup-icon" style="width:6rem;height:6rem;border-radius:50%;margin:0 auto 1.5rem;
                        display:flex;align-items:center;justify-content:center;font-size:3rem;
                        background:linear-gradient(135deg,rgba(201,168,76,0.15),rgba(201,168,76,0.05));
                        border:2px solid rgba(201,168,76,0.4);box-shadow:0 0 40px rgba(201,168,76,0.2);">🎟️</div>
            <div class="font-display font-black text-white uppercase" id="popup-titulo"
                 style="font-size:1.8rem;letter-spacing:0.04em;margin-bottom:0.5rem;">
                ¡Pedido registrado!
            </div>
            <p id="popup-subtitulo" style="color:rgba(255,255,255,0.5);font-size:0.95rem;line-height:1.6;margin-bottom:1.5rem;">
                Tu pedido está siendo procesado.
            </p>
            <div style="padding:1rem;border-radius:1rem;margin-bottom:1rem;
                        background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);">
                <div style="font-size:0.62rem;color:rgba(255,255,255,0.35);font-family:var(--font-display);
                             font-weight:800;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.25rem;">Número de pedido</div>
                <div class="font-display font-black" id="popup-codigo"
                     style="font-size:2.5rem;color:#c9a84c;text-shadow:0 0 30px rgba(201,168,76,0.4);">—</div>
            </div>
            <div id="popup-extra" style="display:none;margin-bottom:1rem;"></div>
            <div style="background:rgba(255,255,255,0.06);border-radius:9999px;height:4px;overflow:hidden;margin-bottom:0.75rem;">
                <div id="popup-progress" style="height:100%;border-radius:9999px;width:100%;
                            background:linear-gradient(90deg,#a07d2e,#c9a84c,#e2c47a);transition:width linear;"></div>
            </div>
            <div style="font-size:0.78rem;color:rgba(255,255,255,0.3);" id="popup-contador">
                Volviendo al inicio en 5 segundos...
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let selectedMethod = null;
const efectivoHabilitado = {{ $efectivoHabilitado ? 'true' : 'false' }};
const qrHabilitado       = {{ $qrHabilitado ? 'true' : 'false' }};

/* ── RELOJ ── */
function initClock() {
    const el = document.getElementById('kiosco-clock');
    if (!el) return;
    const upd = () => {
        el.textContent = new Date().toLocaleTimeString('es-EC', {hour:'2-digit', minute:'2-digit', hour12:true});
    };
    upd();
    setInterval(upd, 1000);
}

function selectMethod(method, el) {
    selectedMethod = method;
    window.selectedMethod = method; // expuesto para el script de auto-refresh
    ['card-efectivo','card-qr'].forEach(id => {
        const c = document.getElementById(id);
        if (!c) return;
        c.style.borderColor = '';
        c.style.background  = '';
        c.style.transform   = '';
        c.style.boxShadow   = '';
        const chk = c.querySelector('.method-check');
        if (chk) chk.style.display = 'none';
    });
    el.style.borderColor = '#c9a84c';
    el.style.background  = 'rgba(201,168,76,0.07)';
    el.style.transform   = 'translateY(-4px)';
    el.style.boxShadow   = '0 16px 48px rgba(0,0,0,0.25),0 0 0 1px rgba(201,168,76,0.18)';
    const chk = el.querySelector('.method-check');
    if (chk) chk.style.display = 'flex';

    ['panel-default','panel-efectivo','panel-qr'].forEach(id => {
        const p = document.getElementById(id);
        if (p) p.style.display = 'none';
    });
    const target = method === 'qr' ? 'panel-qr' : 'panel-efectivo';
    const panel  = document.getElementById(target);
    if (panel) panel.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    if (efectivoHabilitado && !qrHabilitado) {
        const card = document.getElementById('card-efectivo');
        if (card) selectMethod('efectivo', card);
    } else if (qrHabilitado && !efectivoHabilitado) {
        const card = document.getElementById('card-qr');
        if (card) selectMethod('qr', card);
    }
});

function mostrarQR() { document.getElementById('popup-qr').style.display = 'flex'; }
function cerrarQR()  { document.getElementById('popup-qr').style.display = 'none'; }
function yaTransferi() { document.getElementById('popup-qr').style.display = 'none'; confirmarPago('qr'); }

function confirmarPago(method) {
    const formId   = method === 'qr' ? 'form-qr' : 'form-efectivo';
    const form     = document.getElementById(formId);
    const formData = new FormData(form);
    document.querySelectorAll('button[onclick*="confirmarPago"], button[onclick*="yaTransferi"]').forEach(b => {
        b.disabled = true; b.style.opacity = '0.6';
    });
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Error al procesar el pedido');
        mostrarPopupTicket(method, '#' + data.numero_pedido);
    })
    .catch(err => {
        document.querySelectorAll('button[onclick*="confirmarPago"], button[onclick*="yaTransferi"]').forEach(b => {
            b.disabled = false; b.style.opacity = '1';
        });
        alert('Error: ' + (err.message || 'No se pudo procesar el pedido'));
    });
}

function mostrarPopupTicket(method, codigo) {
    const popup     = document.getElementById('popup-ticket');
    const icono     = document.getElementById('popup-icon');
    const titulo    = document.getElementById('popup-titulo');
    const subtitulo = document.getElementById('popup-subtitulo');
    const codigoEl  = document.getElementById('popup-codigo');
    const extra     = document.getElementById('popup-extra');
    const progress  = document.getElementById('popup-progress');
    const contador  = document.getElementById('popup-contador');
    codigoEl.textContent = codigo;
    if (method === 'qr') {
        icono.textContent     = '📱';
        titulo.textContent    = '¡Pago enviado!';
        subtitulo.textContent = 'El personal verificará tu transferencia DeUna. Espera con tu número de pedido.';
        extra.innerHTML       = `<div style="padding:0.75rem;border-radius:0.875rem;background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);"><p style="font-size:0.8rem;color:rgba(147,197,253,0.85);line-height:1.5;">📋 Muestra este número al personal del bar para verificar tu pago.</p></div>`;
        extra.style.display   = 'block';
    } else {
        icono.textContent     = '🧾';
        titulo.textContent    = '¡Pedido registrado!';
        subtitulo.textContent = 'Dirígete a la caja con tu número de pedido. ¡El personal te atenderá!';
        extra.innerHTML       = `<div style="padding:0.75rem;border-radius:0.875rem;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.2);"><p style="font-size:0.8rem;color:rgba(251,191,36,0.85);line-height:1.5;">💵 Recuerda llevar el monto exacto al cancelar en caja.</p></div>`;
        extra.style.display   = 'block';
    }
    popup.style.display = 'flex';
    let segundos = 5;
    progress.style.transition = 'none';
    progress.style.width = '100%';
    setTimeout(() => { progress.style.transition = `width ${segundos}s linear`; progress.style.width = '0%'; }, 80);
    const intervalo = setInterval(() => {
        segundos--;
        contador.textContent = `Volviendo al inicio en ${segundos} segundo${segundos !== 1 ? 's' : ''}...`;
        if (segundos <= 0) { clearInterval(intervalo); window.location.href = '{{ route("kiosco.inicio") }}'; }
    }, 1000);
}

/* ── TEMA ── */
var _themeDebounce = false;

function applyKioscoThemeUI(t) {
    var icon  = document.getElementById('kiosco-theme-icon');
    var label = document.getElementById('kiosco-theme-label-btn');
    var btn   = document.getElementById('kiosco-theme-btn');
    var bg    = document.getElementById('pago-bg');

    if (t === 'dark') {
        if (icon)  icon.textContent  = '☀️';
        if (label) label.textContent = 'Claro';
        if (btn) {
            btn.style.color      = '#ffd700';
            btn.style.textShadow = '0 0 8px rgba(255,215,0,0.6)';
            btn.style.background = 'rgba(201,168,76,0.32)';
            btn.style.borderColor = 'rgba(201,168,76,0.75)';
        }
        if (bg) {
            bg.style.background     = 'radial-gradient(circle, rgba(99,130,255,0.12) 1.2px, transparent 1.2px), linear-gradient(160deg, #080d1e 0%, #0f1635 50%, #080d1e 100%)';
            bg.style.backgroundSize = '18px 18px, 100% 100%';
        }
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        if (icon)  icon.textContent  = '🌙';
        if (label) label.textContent = 'Oscuro';
        if (btn) {
            btn.style.color      = '#cbd630';
            btn.style.textShadow = 'none';
            btn.style.background = 'rgba(201,168,76,0.35)';
            btn.style.borderColor = 'rgba(201,168,76,0.8)';
        }
        if (bg) {
            bg.style.background     = 'radial-gradient(circle, rgba(27,42,107,0.22) 1.2px, transparent 1.2px), linear-gradient(170deg, #ffffff 0%, #eef3ff 30%, #dce8ff 60%, #eef3ff 80%, #ffffff 100%)';
            bg.style.backgroundSize = '18px 18px, 100% 100%';
        }
        document.documentElement.setAttribute('data-theme', 'light');
    }
}

function toggleThemeKiosco() {
    if (_themeDebounce) return;
    _themeDebounce = true;
    var btn = document.getElementById('kiosco-theme-btn');
    if (btn) { btn.style.opacity = '0.5'; btn.style.pointerEvents = 'none'; }
    setTimeout(function() { _themeDebounce = false; if (btn) { btn.style.opacity = '1'; btn.style.pointerEvents = ''; } }, 3000);
    var cur  = document.documentElement.getAttribute('data-theme') || 'dark';
    var next = cur === 'dark' ? 'light' : 'dark';
    localStorage.setItem('istpet-theme', next);
    applyKioscoThemeUI(next);
}

(function(){
    var t = localStorage.getItem('istpet-theme') || 'dark';
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            applyKioscoThemeUI(t);
        });
    } else {
        applyKioscoThemeUI(t);
    }
})();

/* El reloj va siempre en window load para garantizar que el DOM está completo */
window.addEventListener('load', function() { initClock(); });
</script>

{{-- Polling en segundo plano: aquí NO recargamos nada visualmente, para no
     interrumpir al cliente mientras paga. Solo mantiene la "huella" al día
     para que al volver al menú no se dispare un refresh de golpe. --}}
<script src="{{ asset('js/kiosco-realtime.js') }}" data-sede-id="{{ $sede->id }}"></script>
<script>
/* Si el admin desactiva/activa un método de pago mientras el cliente está
   en esta pantalla, refrescamos SOLO si todavía no eligió un método
   (para no interrumpirlo si ya está pagando o esperando confirmación). */
window.addEventListener('kiosco:cambio', function () {
    if (!window.selectedMethod) {
        location.reload();
    }
});

// Actualiza el titular DeUna cada 15 segundos por si el admin lo cambia en configuración
function actualizarTitularDeuna() {
    fetch('/kiosco/api/config-publica')
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('titular-deuna-nombre');
            if (el && data.titular_deuna) el.textContent = data.titular_deuna;
        })
        .catch(() => {});
}
setInterval(actualizarTitularDeuna, 15000);
</script>

<style>
@keyframes pulse   { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
@keyframes scaleIn { from{opacity:0;transform:scale(0.88);} to{opacity:1;transform:scale(1);} }

/* ── Modo oscuro (default) ── */
.titulo-pago        { color: #ffffff; }
.texto-desc         { color: rgba(255,255,255,0.42); }
.metodo-card        { position: relative; overflow: hidden; border: 2px solid rgba(255,255,255,0.08); background: linear-gradient( 180deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.02) 100% ); box-shadow: 0 10px 35px rgba(0,0,0,0.30), 0 0 25px rgba(201,168,76,0.08), inset 0 1px 0 rgba(255,255,255,0.05); }.panel-card         { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); }
.panel-divider      { border-top: 1px solid rgba(255,255,255,0.07); }
.panel-inner-box    { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); }
.paso-item          { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
.paso-text-muted    { color: rgba(255,255,255,0.3); }
.paso-text-inactive { color: rgba(255,255,255,0.25); }
.paso-inactive      { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.25); }
.paso-line          { background: rgba(255,255,255,0.12); }
.btn-disabled       { border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.25); }
.btn-volver         { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.6); }

/* ── Modo claro ── */
[data-theme="light"] .titulo-pago     { color: #0f1635 !important; }
[data-theme="light"] .texto-desc      { color: #3a4a7a !important; }

/* Cards método — fondo blanco con gradiente y sombra elegante */
[data-theme="light"] .metodo-card {
    border: 2px solid rgba(0,0,0,0.10) !important;

[data-theme="light"] .metodo-card {
    border: 2px solid rgba(0,0,0,0.08) !important;

    background:
        linear-gradient(
            180deg,
            #ffffff 0%,
            #eef2f8 45%,
            #d8dde8 100%
        ) !important;

    box-shadow:
        0 12px 35px rgba(0,0,0,0.15),
        inset 0 1px 0 rgba(255,255,255,0.9),
        inset 0 -25px 40px rgba(0,0,0,0.05);
        }

    box-shadow:
        0 10px 35px rgba(0,0,0,0.18),
        0 3px 10px rgba(0,0,0,0.08),
        inset 0 1px 0 rgba(255,255,255,0.8);
}
/* Degradado oscuro sutil en parte inferior para dar profundidad */
[data-theme="light"] .metodo-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1.4rem;
    pointer-events: none;

    background:
        linear-gradient(
            to bottom,
            rgba(255,255,255,0.15) 0%,
            rgba(255,255,255,0) 35%,
            rgba(0,0,0,0.08) 100%
        );
}

/* Panel derecho — mismo cristal neutro */
[data-theme="light"] .panel-card {
    background:
        linear-gradient(
            180deg,
            #ffffff 0%,
            #f1f4f9 35%,
            #e3e8f1 70%,
            #cfd6e3 100%
        ) !important;

    border: 1px solid rgba(0,0,0,0.08) !important;

    box-shadow:
        0 12px 35px rgba(0,0,0,0.15),
        inset 0 1px 0 rgba(255,255,255,0.9),
        inset 0 -25px 40px rgba(0,0,0,0.05);
}

/* TODOS los textos dentro de cards y panel en modo claro */
[data-theme="light"] .metodo-card *,
[data-theme="light"] .panel-card * {
    color: #1b2a6b !important;
}
/* Excepciones: precios dorados, badge de alerta, badges de info se preservan */
[data-theme="light"] .metodo-card [style*="#fbbf24"],
[data-theme="light"] .metodo-card [style*="#93c5fd"],
[data-theme="light"] .metodo-card [style*="#c9a84c"],
[data-theme="light"] .metodo-card [style*="#e2c47a"],
[data-theme="light"] .panel-card  [style*="#c9a84c"],
[data-theme="light"] .panel-card  [style*="#e2c47a"] { color: inherit !important; }

/* Descripción un poco más suave que el título */
[data-theme="light"] .metodo-card p.texto-desc { color: #3a4a7a !important; }
[data-theme="light"] .metodo-card p            { color: #3a4a7a !important; }

/* Precios y totales en dorado se mantienen */
[data-theme="light"] .panel-card [style*="color:#c9a84c"]  { color: #b8892a !important; }
[data-theme="light"] .panel-card [style*="color: #c9a84c"] { color: #b8892a !important; }

[data-theme="light"] .panel-divider   { border-top: 1px solid rgba(27,42,107,0.10) !important; }
[data-theme="light"] .panel-inner-box { background: rgba(27,42,107,0.04) !important; border: 1px solid rgba(27,42,107,0.10) !important; }
[data-theme="light"] .paso-item       { background: rgba(27,42,107,0.03) !important; border: 1px solid rgba(27,42,107,0.08) !important; }
[data-theme="light"] .paso-text-muted    { color: #3a4a7a !important; }
[data-theme="light"] .paso-text-inactive { color: rgba(27,42,107,0.4) !important; }
[data-theme="light"] .paso-inactive   { background: rgba(27,42,107,0.06) !important; border: 1px solid rgba(27,42,107,0.15) !important; color: rgba(27,42,107,0.4) !important; }
[data-theme="light"] .paso-line       { background: rgba(27,42,107,0.15) !important; }
[data-theme="light"] .btn-disabled    { border: 1px solid rgba(27,42,107,0.15) !important; background: rgba(27,42,107,0.05) !important; color: rgba(27,42,107,0.35) !important; }
[data-theme="light"] .btn-volver      { background: rgba(27,42,107,0.07) !important; border: 1px solid rgba(27,42,107,0.15) !important; color: rgba(27,42,107,0.65) !important; }

/* Botón QR en modo claro — azul marino elegante */
[data-theme="light"] #panel-qr button[onclick="mostrarQR()"] {
    background: linear-gradient(135deg, #1b2a6b, #2d4aad) !important;
    box-shadow: 0 8px 28px rgba(27,42,107,0.40) !important;
    color: #ffffff !important;
}

/* ── Layout ── */
.pago-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 340px;
    gap: 1.25rem;
    align-items: start;
    max-width: 900px;
    margin: 0 auto;
}
@media (max-width: 860px) {
    .pago-grid { grid-template-columns: 1fr 1fr; max-width: 600px; }
    .pago-panel { grid-column: 1 / -1; }
}
@media (max-width: 520px) {
    .pago-grid { grid-template-columns: 1fr; max-width: 100%; gap: 0.875rem; }
    .pago-panel { grid-column: 1; }
    .paso-label   { display: none !important; }
    .paso-linea   { width: 1.5rem !important; }
    .paso-circulo { width: 1.75rem !important; height: 1.75rem !important; font-size: 0.65rem !important; }
    #popup-qr > div, #popup-ticket > div { padding: 1.5rem 1.25rem !important; border-radius: 1.25rem !important; width: 95% !important; }
    #popup-titulo  { font-size: 1.4rem !important; }
    #popup-codigo  { font-size: 2rem !important; }
    #popup-icon    { width: 4.5rem !important; height: 4.5rem !important; font-size: 2.2rem !important; margin-bottom: 1rem !important; }
}
</style>
@endpush