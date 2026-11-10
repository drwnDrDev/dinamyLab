// Render de los toasts en el DOM (vanilla, sin React, para cualquier página).
// Usa las plantillas <template data-toast-template> que pinta
// layouts/notifications.blade.php, así los estilos viven en un solo lugar.

import { TOAST_EVENT, createToastStore } from './toast.js';

const CLASES_OCULTO = ['opacity-0', 'translate-x-4'];
const DURACION_SALIDA = 200;

const leerToastServidor = (el) => ({
    type: el.dataset.toastType,
    title: el.querySelector('[data-toast-title]')?.textContent.trim(),
    message: el.querySelector('[data-toast-message]')?.textContent.trim(),
});

function crearElemento(toast, store) {
    const plantilla = document.querySelector(`template[data-toast-template="${toast.type}"]`);
    const el = plantilla?.content.firstElementChild?.cloneNode(true);
    if (!el) {
        return null;
    }

    el.dataset.toastId = String(toast.id);
    el.querySelector('[data-toast-title]').textContent = toast.title;
    el.querySelector('[data-toast-message]').textContent = toast.message;

    const cerrar = el.querySelector('[data-toast-close]');
    cerrar.hidden = false;
    cerrar.addEventListener('click', () => store.dismiss(toast.id));

    // Pausar mientras el usuario lo lee o lo tiene enfocado.
    el.addEventListener('mouseenter', () => store.pause(toast.id));
    el.addEventListener('mouseleave', () => {
        if (!el.contains(document.activeElement)) store.resume(toast.id);
    });
    el.addEventListener('focusin', () => store.pause(toast.id));
    el.addEventListener('focusout', (e) => {
        if (!el.contains(e.relatedTarget) && !el.matches(':hover')) store.resume(toast.id);
    });
    el.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') store.dismiss(toast.id);
    });

    return el;
}

function quitarConAnimacion(el) {
    el.classList.add(...CLASES_OCULTO);
    const reducido = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
    setTimeout(() => el.remove(), reducido ? 0 : DURACION_SALIDA);
}

export function mountToasts(root = document.getElementById('toast-root')) {
    if (!root || window.__dinamyToasts) {
        return window.__dinamyToasts ?? null;
    }

    const store = createToastStore();
    const visibles = new Map(); // id -> elemento

    store.subscribe((lista) => {
        const ids = new Set(lista.map((t) => t.id));

        for (const [id, el] of visibles) {
            if (!ids.has(id)) {
                visibles.delete(id);
                quitarConAnimacion(el);
            }
        }

        for (const toast of lista) {
            if (visibles.has(toast.id)) continue;
            const el = crearElemento(toast, store);
            if (!el) continue;
            el.classList.add(...CLASES_OCULTO);
            root.appendChild(el);
            visibles.set(toast.id, el);
            requestAnimationFrame(() => el.classList.remove(...CLASES_OCULTO));
        }
    });

    window.__dinamyToasts = store;
    window.addEventListener(TOAST_EVENT, (e) => {
        const detalle = e.detail ?? {};
        if (detalle.dismiss) {
            store.dismissKey(detalle.dismiss);
        } else {
            store.add(detalle);
        }
    });

    // Los flash que pintó el servidor pasan al store (botón cerrar y temporizador).
    const delServidor = [...root.querySelectorAll('[data-toast]:not([data-toast-id])')];
    delServidor.forEach((el) => el.remove());
    delServidor.map(leerToastServidor).forEach((t) => store.add(t));

    // Toasts pedidos antes de montar.
    (window.__dinamyToastQueue ?? []).splice(0).forEach((t) => store.add(t));

    return store;
}
