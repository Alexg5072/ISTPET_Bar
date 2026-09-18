import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// ── Toast global ──────────────────────────────────────────────────────
window.showToast = function(message, type = 'success', duration = 3000) {
    const container = document.getElementById('toast-container')
        || (() => {
            const el = document.createElement('div');
            el.id = 'toast-container';
            el.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(el);
            return el;
        })();

    const colors = {
        success: 'bg-emerald-900 border-emerald-500 text-emerald-100',
        error:   'bg-red-900 border-red-500 text-red-100',
        warning: 'bg-yellow-900 border-yellow-500 text-yellow-100',
        info:    'bg-blue-900 border-blue-500 text-blue-100',
    };
    const icons = {
        success: `<svg class="w-4 h-4 flex-shrink-0 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
        error:   `<svg class="w-4 h-4 flex-shrink-0 text-red-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
        warning: `<svg class="w-4 h-4 flex-shrink-0 text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
        info:    `<svg class="w-4 h-4 flex-shrink-0 text-blue-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
    };

    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl border
                       shadow-xl text-sm font-medium pointer-events-auto
                       animate-fade-in min-w-[280px] ${colors[type] || colors.info}`;
    toast.innerHTML = `${icons[type] || icons.info}<span>${message}</span>`;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 350);
    }, duration);
};

// ── Confirmar antes de eliminar ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm || '¿Estás seguro?')) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});