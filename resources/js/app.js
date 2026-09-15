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
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };

    const toast = document.createElement('div');
    toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl border
                       shadow-xl text-sm font-medium pointer-events-auto
                       animate-fade-in min-w-[280px] ${colors[type] || colors.info}`;
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span><span>${message}</span>`;

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