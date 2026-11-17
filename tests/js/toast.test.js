import test from 'node:test';
import assert from 'node:assert/strict';
import {
    TOAST_DEFAULTS,
    createToastStore,
    normalizeToast,
} from '../../resources/js/lib/toast.js';

// Reloj y temporizadores falsos para controlar el auto-cierre.
function fakeTimers() {
    let actual = 0;
    let siguienteId = 1;
    const pendientes = new Map();

    return {
        now: () => actual,
        setTimer(fn, ms) {
            const id = siguienteId++;
            pendientes.set(id, { fn, at: actual + ms });
            return id;
        },
        clearTimer(id) {
            pendientes.delete(id);
        },
        advance(ms) {
            actual += ms;
            for (const [id, t] of [...pendientes]) {
                if (t.at <= actual) {
                    pendientes.delete(id);
                    t.fn();
                }
            }
        },
    };
}

test('aplica título, rol y duración por tipo', () => {
    const exito = normalizeToast({ type: 'success', message: 'Listo' });
    assert.equal(exito.title, 'Operación completada');
    assert.equal(exito.role, 'status');
    assert.equal(exito.live, 'polite');
    assert.equal(exito.duration, 5000);

    const error = normalizeToast({ type: 'error', message: 'Falló' });
    assert.equal(error.title, 'No se pudo completar la solicitud');
    assert.equal(error.role, 'alert');
    assert.equal(error.live, 'assertive');
    assert.equal(error.duration, null);

    assert.equal(normalizeToast({ type: 'warning', message: 'x' }).duration, null);
    assert.equal(normalizeToast({ type: 'info', message: 'x' }).duration, 5000);
});

test('un tipo desconocido se trata como info y el título se puede cambiar', () => {
    const t = normalizeToast({ type: 'raro', message: 'x', title: 'Propio' });
    assert.equal(t.type, 'info');
    assert.equal(t.title, 'Propio');
});

test('los errores nunca se cierran solos aunque se pida una duración', () => {
    assert.equal(normalizeToast({ type: 'error', message: 'x', duration: 3000 }).duration, null);
    assert.equal(normalizeToast({ type: 'success', message: 'x', duration: 3000 }).duration, 3000);
});

test('ignora mensajes vacíos', () => {
    const store = createToastStore(fakeTimers());
    assert.equal(store.add({ type: 'success', message: '   ' }), null);
    assert.equal(store.list().length, 0);
});

test('success e info se cierran solos; error y warning se quedan', () => {
    const timers = fakeTimers();
    const store = createToastStore(timers);

    store.add({ type: 'success', message: 'a' });
    store.add({ type: 'info', message: 'b' });
    store.add({ type: 'error', message: 'c' });
    store.add({ type: 'warning', message: 'd' });

    timers.advance(4999);
    assert.equal(store.list().length, 4);

    timers.advance(1);
    assert.deepEqual(store.list().map((t) => t.message), ['c', 'd']);

    timers.advance(60000);
    assert.equal(store.list().length, 2);
});

test('pausar conserva el tiempo restante y reanudar lo retoma', () => {
    const timers = fakeTimers();
    const store = createToastStore(timers);
    const id = store.add({ type: 'success', message: 'a' });

    timers.advance(3000);
    store.pause(id);
    timers.advance(10000);
    assert.equal(store.list().length, 1);

    store.resume(id);
    timers.advance(1999);
    assert.equal(store.list().length, 1);
    timers.advance(1);
    assert.equal(store.list().length, 0);
});

test('dismiss cierra y avisa a los suscriptores', () => {
    const store = createToastStore(fakeTimers());
    const vistos = [];
    store.subscribe((lista) => vistos.push(lista.length));

    const id = store.add({ type: 'error', message: 'a' });
    store.dismiss(id);
    store.dismiss(id); // idempotente

    assert.deepEqual(vistos, [1, 0]);
});

test('un mensaje repetido reinicia el temporizador en vez de apilarse', () => {
    const timers = fakeTimers();
    const store = createToastStore(timers);

    const id = store.add({ type: 'success', message: 'Guardado' });
    timers.advance(4000);
    const repetido = store.add({ type: 'success', message: 'Guardado' });

    assert.equal(repetido, id);
    assert.equal(store.list().length, 1);
    timers.advance(4000);
    assert.equal(store.list().length, 1);
    timers.advance(1000);
    assert.equal(store.list().length, 0);
});

test('con más del máximo descarta el más antiguo que se cierra solo', () => {
    const store = createToastStore({ ...fakeTimers(), max: 3 });

    store.add({ type: 'error', message: 'e1' });
    store.add({ type: 'success', message: 's1' });
    store.add({ type: 'success', message: 's2' });
    store.add({ type: 'info', message: 'i1' });

    assert.deepEqual(store.list().map((t) => t.message), ['e1', 's2', 'i1']);
});

test('nunca descarta un error para hacer espacio', () => {
    const store = createToastStore({ ...fakeTimers(), max: 2 });

    store.add({ type: 'error', message: 'e1' });
    store.add({ type: 'warning', message: 'w1' });
    store.add({ type: 'error', message: 'e2' });
    assert.deepEqual(store.list().map((t) => t.message), ['e1', 'e2']);

    store.add({ type: 'error', message: 'e3' });
    assert.deepEqual(store.list().map((t) => t.message), ['e1', 'e2', 'e3']);
});

test('expone los valores por defecto de los cuatro tipos', () => {
    assert.deepEqual(Object.keys(TOAST_DEFAULTS).sort(), ['error', 'info', 'success', 'warning']);
});

test('un toast con la misma key reemplaza al anterior aunque sea un error', () => {
    const store = createToastStore(fakeTimers());

    store.add({ type: 'error', message: 'Revisa el formulario', key: 'form' });
    store.add({ type: 'info', message: 'Otro aviso' });
    store.add({ type: 'success', message: 'Guardado', key: 'form' });

    assert.deepEqual(store.list().map((t) => t.message), ['Otro aviso', 'Guardado']);
});
