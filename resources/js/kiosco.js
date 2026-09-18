import Alpine from 'alpinejs';

// ── Toast de notificaciones ──────────────────────────────────────────
window.showToast = function(message, type = 'success', duration = 3000) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position:fixed;top:1.25rem;right:1.25rem;z-index:99999;display:flex;flex-direction:column;gap:0.5rem;';
        document.body.appendChild(container);
    }
    const colors = { success: '#22c55e', error: '#ef4444', info: '#3b82f6' };
    const icons  = {
        success: `<svg style="width:1.2rem;height:1.2rem;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
        error:   `<svg style="width:1.2rem;height:1.2rem;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
        info:    `<svg style="width:1.2rem;height:1.2rem;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`
    };
    const toast  = document.createElement('div');
    toast.style.cssText = `display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1.1rem;
        border-radius:0.875rem;background:#1e2130;border:1px solid ${colors[type] ?? colors.info}33;
        color:white;font-family:var(--font-display,sans-serif);font-weight:700;font-size:0.85rem;
        box-shadow:0 8px 28px rgba(0,0,0,0.4);min-width:220px;max-width:340px;
        animation:toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;`;
    toast.innerHTML = `${icons[type] ?? icons.info}<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(20px)';
        setTimeout(() => toast.remove(), 320);
    }, duration);
};

if (!document.getElementById('toast-keyframes')) {
    const s = document.createElement('style');
    s.id = 'toast-keyframes';
    s.textContent = '@keyframes toastIn{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:translateX(0)}}';
    document.head.appendChild(s);
}

// ── Store Alpine del carrito ─────────────────────────────────────────
Alpine.store('carrito', {
    items:  [],
    count:  0,
    total:  0,
    vacio:  true,
    cargando: false,

    async agregar(tipo, itemId, nombre, stockMax) {
        this.cargando = true;
        try {
            // Verificar stock local antes del request
            const existente = this.items.find(i => i.tipo === tipo && String(i.id) === String(itemId));
            const cantidadActual = existente ? existente.cantidad : 0;
            const maxStock = stockMax ?? existente?.stock_max;
            if (maxStock !== undefined && cantidadActual >= maxStock) {
                window.showToast?.(`No hay más stock de "${nombre}"`, 'error');
                this.cargando = false;
                return;
            }
            const res = await fetch('/kiosco/carrito/agregar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ tipo, item_id: itemId, cantidad: 1 }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Error al agregar');
            this.sincronizar(data.carrito);
            window.showToast?.(`${nombre} agregado`, 'success');
            this.animarCarrito();
        } catch (err) {
            window.showToast?.(err.message, 'error');
        } finally {
            this.cargando = false;
        }
    },

    async cambiarCantidad(tipo, itemId, delta) {
        // delta +1 → agregar, -1 → quitar
        if (delta > 0) {
            const item = this.items.find(i => i.tipo === tipo && String(i.id) === String(itemId));
            const nombre = item ? item.nombre : '';
            // Bloquear si alcanzó el stock máximo
            if (item && item.stock_max !== undefined && item.cantidad >= item.stock_max) {
                window.showToast?.('Ya tienes el máximo disponible de "' + item.nombre + '"', 'error');
                return;
            }
            await this.agregar(tipo, itemId, nombre);
            return;
        }
        // Quitar
        const res = await fetch('/kiosco/carrito/quitar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ tipo, item_id: itemId, delta }),
        });
        const data = await res.json();
        this.sincronizar(data.carrito);
    },

    async limpiar() {
        const res = await fetch('/kiosco/carrito/limpiar', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        const data = await res.json();
        this.sincronizar(data.carrito);
    },

    sincronizar(carrito) {
        this.items = carrito.items;
        this.count = carrito.count;
        this.total = carrito.total;
        this.vacio = carrito.vacio;
    },

    animarCarrito() {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        badge.classList.add('scale-125');
        setTimeout(() => badge.classList.remove('scale-125'), 250);
    },

    async init() {
        const res  = await fetch('/kiosco/carrito/datos');
        const data = await res.json();
        this.sincronizar(data.carrito);
    },
});

// ── Modo reposo / promociones ────────────────────────────────────────
class ModoReposo {
    constructor(tiempoMs = 120000) {
        this.tiempoMs     = tiempoMs;
        this.timer        = null;
        this.slideTimer   = null;
        this.slides       = [];
        this.currentSlide = 0;
        this.activo       = false;
        this._agregarHandle = null;
    }

    init(slides, tiempoMs, slideMs) {
        this.slides    = slides;
        this.tiempoMs  = tiempoMs || this.tiempoMs;
        this.slideMs   = slideMs || 5000; // duración por defecto de cada slide
        this.buildOverlay();
        this.resetTimer();

        ['touchstart','mousemove','click','keydown','scroll'].forEach(ev => {
            document.addEventListener(ev, () => {
                this.resetTimer();
                if (this.activo) this.ocultar();
            }, { passive: true });
        });
    }

    buildOverlay() {
        // Si ya existe pero está vacío (creado por el layout), eliminarlo
        const existing = document.getElementById('promo-overlay');
        if (existing && existing.children.length > 1) return;
        if (existing) existing.remove();

        // ── Overlay raíz ──────────────────────────────────────────────
        const overlay = document.createElement('div');
        overlay.id = 'promo-overlay';
        overlay.style.cssText = `
            position:fixed;inset:0;z-index:9999;display:none;
            cursor:pointer;overflow:hidden;
            font-family:'Inter Tight','Barlow',sans-serif;
        `;

        // ── Fondo blur detrás ──────────────────────────────────────────
        const bgBlur = document.createElement('div');
        bgBlur.style.cssText = 'position:absolute;inset:0;backdrop-filter:blur(2px);background:rgba(5,8,18,0.45);z-index:0;';
        overlay.appendChild(bgBlur);

        // ── Slides ─────────────────────────────────────────────────────
        this.slides.forEach((slide, i) => {
            const el = document.createElement('div');
            el.id = `promo-slide-${i}`;
            el.style.cssText = `
                position:absolute;inset:0;
                display:flex;flex-direction:column;
                align-items:center;justify-content:center;
                text-align:center;
                opacity:${i === 0 ? '1' : '0'};
                transition:opacity 0.8s cubic-bezier(0.22,1,0.36,1);
                z-index:1;
            `;

            // Fondo de la slide
            if (slide.imagen_url) {
                el.style.backgroundImage    = `url('${slide.imagen_url}')`;
                el.style.backgroundSize     = 'cover';
                el.style.backgroundPosition = 'center';
            } else {
                el.style.background = 'linear-gradient(145deg,#0a1028 0%,#111b4a 50%,#0d1535 100%)';
            }

            // Capas decorativas
            el.innerHTML = `
                <!-- Gradiente de oscurecimiento -->
                <div style="position:absolute;inset:0;
                    background:linear-gradient(to bottom,
                    rgba(0,0,0,0.55) 0%,
                    rgba(0,0,0,0.30) 30%,
                    rgba(0,0,0,0.50) 65%,
                    rgba(0,0,0,0.82) 100%);
                    z-index:0;pointer-events:none;"></div>

                <!-- Grid decorativo -->
                <div style="position:absolute;inset:0;
                    background-image:
                        linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),
                        linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);
                    background-size:52px 52px;
                    z-index:0;pointer-events:none;"></div>

                <!-- Orb decorativo -->
                <div style="position:absolute;width:500px;height:500px;
                    border-radius:50%;filter:blur(100px);
                    background:radial-gradient(circle,rgba(201,168,76,0.08),transparent 70%);
                    top:50%;left:50%;transform:translate(-50%,-50%);
                    z-index:0;pointer-events:none;"></div>

                <!-- Contenido principal -->
                <div class="promo-content-inner" style="position:relative;z-index:2;
                    max-width:680px;padding:2rem;
                    display:flex;flex-direction:column;align-items:center;gap:1.1rem;">

                    ${slide.imagen_url ? '' : `
                    <div style="filter:drop-shadow(0 6px 20px rgba(0,0,0,0.5));
                        animation:promoBob 3s ease-in-out infinite;">
                        <svg style="width:4rem;height:4rem;color:#c9a84c;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line>
                        </svg>
                    </div>`}

                    <!-- Badge PROMO -->
                    <div style="display:inline-flex;align-items:center;gap:0.5rem;
                        padding:0.3rem 1rem;border-radius:999px;
                        background:rgba(201,168,76,0.25);border:1px solid rgba(201,168,76,0.6);
                        font-size:0.62rem;font-weight:900;text-transform:uppercase;
                        letter-spacing:0.2em;color:rgba(201,168,76,0.95);
                        text-shadow:0 1px 4px rgba(0,0,0,0.95), 0 2px 10px rgba(0,0,0,0.8);">
                        <span style="width:5px;height:5px;border-radius:50%;background:#e2c47a;
                            box-shadow:0 0 6px rgba(201,168,76,0.9), 0 0 12px rgba(201,168,76,0.6);
                            animation:promoBlink 1.5s ease-in-out infinite;"></span>
                        Promoción especial
                        <span style="width:5px;height:5px;border-radius:50%;background:#e2c47a;
                            box-shadow:0 0 6px rgba(201,168,76,0.9), 0 0 12px rgba(201,168,76,0.6);
                            animation:promoBlink 1.5s ease-in-out infinite;animation-delay:.5s;"></span>
                    </div>

                    <!-- Título -->
                    <h2 style="font-weight:900;font-size:clamp(2.2rem,5vw,4rem);
                        color:white;text-transform:uppercase;letter-spacing:0.04em;
                        line-height:1.0;text-shadow:0 2px 4px rgba(0,0,0,0.9), 0 6px 30px rgba(0,0,0,0.8), 0 0 60px rgba(0,0,0,0.5);margin:0;">
                        ${slide.titulo}
                    </h2>

                    <!-- Descripción -->
                    ${slide.descripcion ? `
                    <p style="font-size:clamp(0.95rem,2vw,1.2rem);color:rgba(255,255,255,0.92);text-shadow:0 2px 8px rgba(0,0,0,0.9), 0 4px 20px rgba(0,0,0,0.7);
                        line-height:1.6;max-width:480px;margin:0;">
                        ${slide.descripcion}
                    </p>` : ''}

                    <!-- Precio destacado -->
                    ${slide.precio_destacado ? `
                    <div style="font-weight:900;font-size:clamp(3rem,7vw,6rem);
                        color:#c9a84c;line-height:1;
                        text-shadow:0 2px 4px rgba(0,0,0,0.9), 0 0 60px rgba(201,168,76,0.6), 0 4px 20px rgba(0,0,0,0.8);">
                        $${String(slide.precio_destacado).replace(/[^0-9.]/g,'')}
                    </div>` : ''}

                    <!-- Botones productos -->
                    <div style="display:flex;flex-direction:column;align-items:center;gap:0.75rem;margin-top:0.5rem;width:100%;max-width:520px;">

                        ${slide.productos && slide.productos.length > 0 ? (() => {
                            // Calcular precio total de los productos disponibles
                            const prods = slide.productos.filter(p => p.disponible !== false);
                            const total = prods.reduce((s, p) => s + parseFloat(p.precio || 0), 0);
                            const ids   = prods.map(p => p.id).join(',');
                            const noms  = prods.map(p => p.nombre).join(', ');
                            const btnLabel = prods.length > 1
                                ? `Pedir combo · $${total.toFixed(2)}`
                                : `Pedir ${prods[0]?.nombre || ''} · $${total.toFixed(2)}`;

                            return prods.length > 0 ? `
                            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;justify-content:center;">
                                ${prods.map(p => `<span style="padding:0.25rem 0.75rem;border-radius:999px;
                                    background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.28);
                                    font-size:0.72rem;color:rgba(255,255,255,0.90); text-shadow:0 1px 6px rgba(0,0,0,0.95), 0 2px 12px rgba(0,0,0,0.8);">
                                    ${p.nombre} · $${parseFloat(p.precio).toFixed(2)}
                                </span>`).join('')}
                            </div>
                            <button
                                data-ids="${ids}"
                                data-nombres="${noms}"
                                onclick="event.stopPropagation();promoAgregarMultiple(this)"
                                style="display:inline-flex;align-items:center;gap:0.6rem;
                                    padding:0.9rem 2rem;border-radius:0.9rem;border:none;
                                    cursor:pointer;font-weight:900;font-size:0.92rem;
                                    text-transform:uppercase;letter-spacing:0.08em;
                                    color:#0a1020;font-family:inherit;
                                    background:linear-gradient(135deg,#9a7328,#c9a84c,#e2c47a);
                                    box-shadow:0 8px 30px rgba(201,168,76,0.4);">
                                ${btnLabel}
                            </button>` : '';
                        })() : ''}

                        <div style="display:inline-flex;align-items:center;gap:0.45rem;
                            padding:0.65rem 1.2rem;border-radius:0.9rem;
                            background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.22);
                            font-size:0.72rem;font-weight:700;color:rgba(255,255,255,0.75); text-shadow:0 1px 6px rgba(0,0,0,0.95), 0 2px 12px rgba(0,0,0,0.8);
                            text-transform:uppercase;letter-spacing:0.1em;pointer-events:none;">
                            Toca afuera para cerrar
                        </div>
                    </div>
                </div>
            `;
            overlay.appendChild(el);
        });

        // ── Barra de progreso del slide ────────────────────────────────
        const progressBar = document.createElement('div');
        progressBar.id = 'promo-progress';
        progressBar.style.cssText = `
            position:absolute;top:0;left:0;height:3px;width:0%;z-index:10;
            background:linear-gradient(90deg,#9a7328,#c9a84c,#e2c47a);
            transition:width linear;border-radius:0 3px 3px 0;
            box-shadow:0 0 12px rgba(201,168,76,0.6);
        `;
        overlay.appendChild(progressBar);

        // ── Dots de navegación ─────────────────────────────────────────
        if (this.slides.length > 1) {
            const dots = document.createElement('div');
            dots.id = 'promo-dots';
            dots.style.cssText = `
                position:absolute;bottom:2.2rem;left:50%;transform:translateX(-50%);
                display:flex;gap:0.5rem;z-index:10;
            `;
            this.slides.forEach((_, i) => {
                const d = document.createElement('div');
                d.id = `pdot-${i}`;
                d.style.cssText = `
                    height:6px;border-radius:999px;
                    transition:all 0.3s ease;cursor:pointer;
                    ${i === 0
                        ? 'width:24px;background:#c9a84c;box-shadow:0 0 10px rgba(201,168,76,0.5);'
                        : 'width:6px;background:rgba(255,255,255,0.25);'}
                `;
                d.onclick = (e) => { e.stopPropagation(); this.goToSlide(i); };
                dots.appendChild(d);
            });
            overlay.appendChild(dots);
        }

        // ── Indicador de sede ──────────────────────────────────────────
        const sedeTag = document.createElement('div');
        sedeTag.style.cssText = `
            position:absolute;top:1.2rem;left:1.5rem;z-index:10;
            display:flex;align-items:center;gap:0.5rem;
            padding:0.35rem 0.85rem;border-radius:999px;
            background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);
            font-size:0.62rem;font-weight:800;text-transform:uppercase;
            letter-spacing:0.12em;color:rgba(255,255,255,0.75); text-shadow:0 1px 6px rgba(0,0,0,0.95), 0 2px 12px rgba(0,0,0,0.8);
        `;
        sedeTag.id = 'promo-sede-tag';
        overlay.appendChild(sedeTag);

        // CSS animaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes promoBob   { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
            @keyframes promoBlink { 0%,100%{opacity:1} 50%{opacity:0.25} }
            @keyframes promoFadeIn{ from{opacity:0;transform:scale(0.97)} to{opacity:1;transform:scale(1)} }
            #promo-overlay.visible { animation:promoFadeIn 0.5s cubic-bezier(0.22,1,0.36,1) both; }
        `;
        document.head.appendChild(style);

        document.body.appendChild(overlay);
    }

    mostrar() {
        if (!this.slides.length) return;
        this.activo = true;
        const overlay = document.getElementById('promo-overlay');
        if (!overlay) return;

        overlay.style.display = 'block';
        overlay.classList.add('visible');
        this.currentSlide = 0;
        this._showSlide(0);
        this._scheduleNext();
    }

    ocultar() {
        this.activo = false;
        const overlay = document.getElementById('promo-overlay');
        if (!overlay) return;
        overlay.style.opacity = '0';
        overlay.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.style.opacity = '';
            overlay.style.transition = '';
            overlay.classList.remove('visible');
        }, 500);
        clearInterval(this.slideTimer);
        clearTimeout(this.slideTimeout);
        this._stopProgress();
        this.resetTimer();
    }

    _scheduleNext() {
        clearInterval(this.slideTimer);
        clearTimeout(this.slideTimeout);
        if (this.slides.length <= 1) return;
        const slideDur = this.slides[this.currentSlide]?.duracion;
        const dur = slideDur ? slideDur * 1000 : this.slideMs;
        this._startProgress(dur);
        this.slideTimeout = setTimeout(() => {
            if (!this.activo) return;
            const next = (this.currentSlide + 1) % this.slides.length;
            this._showSlide(next);
            this._scheduleNext();
        }, dur);
    }

    _startProgress(dur) {
        const bar = document.getElementById('promo-progress');
        if (!bar) return;
        bar.style.transition = 'none';
        bar.style.width = '0%';
        // Double rAF ensures the reset is painted before animating
        requestAnimationFrame(() => requestAnimationFrame(() => {
            bar.style.transition = `width ${dur}ms linear`;
            bar.style.width = '100%';
        }));
    }

    _stopProgress() {
        const bar = document.getElementById('promo-progress');
        if (bar) { bar.style.transition = 'none'; bar.style.width = '0%'; }
    }

    nextSlide() {
        const next = (this.currentSlide + 1) % this.slides.length;
        this._showSlide(next);
        this._scheduleNext();
    }

    _showSlide(i) {
        this.currentSlide = i;
        this.slides.forEach((_, idx) => {
            const slide = document.getElementById(`promo-slide-${idx}`);
            const dot   = document.getElementById(`pdot-${idx}`);
            if (slide) slide.style.opacity = idx === i ? '1' : '0';
            if (dot) {
                dot.style.width      = idx === i ? '24px' : '6px';
                dot.style.background = idx === i
                    ? '#c9a84c'
                    : 'rgba(255,255,255,0.25)';
                dot.style.boxShadow  = idx === i
                    ? '0 0 10px rgba(201,168,76,0.5)'
                    : 'none';
            }
        });
        this.currentSlide = i;
    }

    resetTimer() {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.mostrar(), this.tiempoMs);
    }
}

// Agregar múltiples productos desde promo
window.promoAgregarMultiple = async function(btn) {
    const ids    = (btn.dataset.ids || '').split(',').filter(Boolean);
    const noms   = (btn.dataset.nombres || '').split(',');
    if (!ids.length) return;

    btn.disabled = true;
    btn.innerHTML = 'Agregando...';

    for (let i = 0; i < ids.length; i++) {
        await Alpine.store('carrito').agregar('producto', ids[i].trim(), (noms[i] || '').trim());
    }

    btn.innerHTML = '¡Listo! ' + ids.length + (ids.length > 1 ? ' productos' : ' producto') + ' agregado' + (ids.length > 1 ? 's' : '');
    btn.style.background = 'linear-gradient(135deg,#064e3b,#10b981)';

    setTimeout(() => {
        const overlay = document.getElementById('promo-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                overlay.style.display = 'none';
                overlay.style.opacity = '';
                overlay.style.transition = '';
            }, 500);
        }
    }, 700);
};

window.ModoReposo = ModoReposo;
window.Alpine     = Alpine;
Alpine.start();