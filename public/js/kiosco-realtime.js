/**
 * kiosco-realtime.js
 * ──────────────────────────────────────────────────────────────────────────
 * Sistema ligero de "auto-actualización" para ISTPET Bar.
 *
 * Cómo funciona (para que quede claro si alguien más toca esto después):
 *
 *  1. Cada X segundos hace una petición MUY liviana a /kiosco/api/huella,
 *     que solo devuelve un hash corto (MD5) calculado en el servidor a
 *     partir de la fecha de "última modificación" de productos, categorías,
 *     combos, promociones y configuración.
 *
 *  2. Si ese hash es IGUAL al que ya tenía guardado, no hace nada más.
 *     (Esto es lo que lo hace "optimizado": NO descarga el catálogo
 *     completo en cada ciclo, solo un string corto).
 *
 *  3. Si el hash CAMBIÓ, dispara un evento personalizado en el navegador:
 *
 *         window.dispatchEvent(new CustomEvent('kiosco:cambio'))
 *
 *     Cada página que use este script escucha ese evento y decide qué
 *     recargar (productos, configuración de pagos, etc.), sin necesidad
 *     de que el usuario presione F5.
 *
 * Uso en una vista Blade:
 *
 *     <script src="{{ asset('js/kiosco-realtime.js') }}"
 *             data-sede-id="{{ $sede->id ?? '' }}"></script>
 *
 *     <script>
 *         window.addEventListener('kiosco:cambio', () => {
 *             cargarProductos(...); // lo que ya tengas en tu vista
 *         });
 *     </script>
 *
 * El atributo data-sede-id es opcional: si la página no tiene sede
 * (ej. una pantalla de admin que afecta a todas las sedes), se omite.
 */
(function () {
    'use strict';

    const SCRIPT_TAG   = document.currentScript;
    const SEDE_ID       = SCRIPT_TAG?.dataset?.sedeId || '';
    const INTERVALO_MS  = parseInt(SCRIPT_TAG?.dataset?.intervalo || '4000', 10);
    const URL_HUELLA     = '/kiosco/api/huella' + (SEDE_ID ? `?sede_id=${SEDE_ID}` : '');

    let huellaActual = null;
    let timer        = null;
    let enPausa      = false; // se pausa si la pestaña no está visible (ahorra peticiones)

    async function verificarCambios() {
        if (enPausa) return;
        try {
            const res  = await fetch(URL_HUELLA, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            if (huellaActual === null) {
                // Primera lectura: solo guardamos, no disparamos evento.
                huellaActual = data.huella;
                return;
            }

            if (data.huella !== huellaActual) {
                huellaActual = data.huella;
                window.dispatchEvent(new CustomEvent('kiosco:cambio', {
                    detail: { huella: data.huella, ts: data.ts }
                }));
            }
        } catch (e) {
            // Silencioso a propósito: un fallo de red puntual no debe
            // interrumpir el uso normal del kiosco/admin.
        }
    }

    function iniciar() {
        if (timer) return;
        verificarCambios(); // primera lectura inmediata
        timer = setInterval(verificarCambios, INTERVALO_MS);
    }

    function detener() {
        if (timer) clearInterval(timer);
        timer = null;
    }

    // Pausar el polling si la pestaña/pantalla no está visible
    // (ahorra peticiones cuando el kiosco está en reposo de pantalla
    // del sistema operativo, o si el admin cambia de pestaña).
    document.addEventListener('visibilitychange', function () {
        enPausa = document.hidden;
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }

    // Exponer un control mínimo por si alguna vista necesita pausar/reanudar
    // manualmente (ej. mientras el cliente está pagando, para no
    // interrumpirlo con un refresh a mitad del flujo de pago).
    window.KioscoRealtime = {
        pausar: function () { enPausa = true; },
        reanudar: function () { enPausa = false; },
        detener: detener,
        forzarVerificacion: verificarCambios,
    };
})();