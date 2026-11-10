// Notificaciones tipo toast: lógica pura (sin DOM) y API pública `toast()`.
// El render vive en toastDom.js; este módulo se puede probar con node --test.

export const TOAST_EVENT = 'dinamy:toast';
export const TOAST_MAX = 4;

export const TOAST_DEFAULTS = {
    success: { title: 'Operación completada', role: 'status', live: 'polite', duration: 5000 },
    info: { title: 'Información', role: 'status', live: 'polite', duration: 5000 },
    warning: { title: 'Atención', role: 'alert', live: 'assertive', duration: null },
    error: { title: 'No se pudo completar la solicitud', role: 'alert', live: 'assertive', duration: null },
};

// Completa un toast con los valores de su tipo. Warning y error nunca se
// cierran solos: un aviso clínico no debe desaparecer sin que se lea.
export function normalizeToast({ type, title, message, duration, key } = {}) {
    const tipo = Object.hasOwn(TOAST_DEFAULTS, type) ? type : 'info';
    const base = TOAST_DEFAULTS[tipo];
    const persistente = base.duration === null;

    return {
        type: tipo,
        title: title || base.title,
        message: typeof message === 'string' ? message.trim() : String(message ?? '').trim(),
        role: base.role,
        live: base.live,
        key: typeof key === 'string' && key ? key : null,
        duration: persistente ? null : (Number.isFinite(duration) && duration > 0 ? duration : base.duration),
    };
}

export function createToastStore({
    max = TOAST_MAX,
    setTimer = (fn, ms) => setTimeout(fn, ms),
    clearTimer = (id) => clearTimeout(id),
    now = () => Date.now(),
} = {}) {
    let toasts = [];
    let siguienteId = 1;
    const suscriptores = new Set();
    // id -> { timer, restante, inicio }
    const relojes = new Map();

    const emitir = () => {
        const copia = toasts.slice();
        suscriptores.forEach((fn) => fn(copia));
    };

    const detenerReloj = (id) => {
        const reloj = relojes.get(id);
        if (reloj?.timer != null) {
            clearTimer(reloj.timer);
            reloj.timer = null;
        }
    };

    const iniciarReloj = (id, ms) => {
        detenerReloj(id);
        relojes.set(id, { timer: setTimer(() => dismiss(id), ms), restante: ms, inicio: now() });
    };

    function dismiss(id) {
        if (!toasts.some((t) => t.id === id)) {
            return;
        }
        detenerReloj(id);
        relojes.delete(id);
        toasts = toasts.filter((t) => t.id !== id);
        emitir();
    }

    // Hace espacio quitando primero el más antiguo que se cierra solo, luego
    // el warning más antiguo. Los errores nunca se descartan.
    const hacerEspacio = () => {
        while (toasts.length > max) {
            const victima =
                toasts.find((t) => t.duration !== null) ?? toasts.find((t) => t.type === 'warning');
            if (!victima) {
                return;
            }
            detenerReloj(victima.id);
            relojes.delete(victima.id);
            toasts = toasts.filter((t) => t.id !== victima.id);
        }
    };

    function add(entrada) {
        const toast = normalizeToast(entrada);
        if (!toast.message) {
            return null;
        }

        // Un toast con la misma `key` reemplaza al anterior (p. ej. el éxito
        // de un formulario reemplaza su error previo).
        const anterior = toast.key ? toasts.find((t) => t.key === toast.key) : null;
        if (anterior && (anterior.type !== toast.type || anterior.message !== toast.message)) {
            detenerReloj(anterior.id);
            relojes.delete(anterior.id);
            toasts = toasts.filter((t) => t.id !== anterior.id);
        }

        const repetido = toasts.find((t) => t.type === toast.type && t.message === toast.message);
        if (repetido) {
            if (repetido.duration !== null) {
                iniciarReloj(repetido.id, repetido.duration);
            }
            return repetido.id;
        }

        const nuevo = { id: siguienteId++, ...toast };
        toasts = [...toasts, nuevo];
        if (nuevo.duration !== null) {
            iniciarReloj(nuevo.id, nuevo.duration);
        }
        hacerEspacio();
        emitir();

        return nuevo.id;
    }

    // Cierra el toast que tenga esa `key` (si hay uno visible).
    function dismissKey(key) {
        const t = key ? toasts.find((x) => x.key === key) : null;
        if (t) dismiss(t.id);
    }

    function pause(id) {
        const reloj = relojes.get(id);
        if (!reloj || reloj.timer == null) {
            return;
        }
        detenerReloj(id);
        reloj.restante = Math.max(0, reloj.restante - (now() - reloj.inicio));
    }

    function resume(id) {
        const reloj = relojes.get(id);
        if (!reloj || reloj.timer != null) {
            return;
        }
        iniciarReloj(id, reloj.restante);
    }

    function subscribe(fn) {
        suscriptores.add(fn);
        return () => suscriptores.delete(fn);
    }

    return { add, dismiss, dismissKey, pause, resume, subscribe, list: () => toasts.slice() };
}

// API pública para las islas React y el JS de las vistas. Despacha un evento
// en window para que funcione aunque varios entries de Vite carguen el módulo;
// si el contenedor aún no está montado, el toast queda en cola.
export function toast(message, options = {}) {
    if (typeof window === 'undefined') {
        return;
    }
    const detalle = { ...options, message };

    if (window.__dinamyToasts) {
        window.dispatchEvent(new CustomEvent(TOAST_EVENT, { detail: detalle }));
    } else {
        (window.__dinamyToastQueue ??= []).push(detalle);
    }
}

// Cierra desde fuera el toast que se creó con esa `key`.
toast.dismiss = (key) => {
    if (typeof window === 'undefined' || !key) {
        return;
    }
    if (window.__dinamyToasts) {
        window.dispatchEvent(new CustomEvent(TOAST_EVENT, { detail: { dismiss: key } }));
    } else if (window.__dinamyToastQueue) {
        window.__dinamyToastQueue = window.__dinamyToastQueue.filter((t) => t.key !== key);
    }
};

for (const tipo of Object.keys(TOAST_DEFAULTS)) {
    toast[tipo] = (message, options = {}) => toast(message, { ...options, type: tipo });
}

export default toast;
