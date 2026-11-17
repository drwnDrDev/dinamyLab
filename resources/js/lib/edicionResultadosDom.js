// Cableado de la vista resultados.edit (ED-04). Contrato de marcado:
//
// form[data-edicion-resultados]                    (data-abrir-revision="true" reabre el diálogo tras un error)
//   tr[data-parametro]                             data-id, data-nombre, data-original, data-min, data-max
//     [data-campo]                                 input, select o textarea con name="resultados[id]"
//     [data-rango-nuevo]                           aviso de rango del valor nuevo
//   [data-contador]                                texto "N cambios"
//   button[data-revisar]                           abre el diálogo de revisión
//   dialog[data-dialogo-revision]
//     tbody[data-resumen]                          filas anterior → nuevo
//     textarea[name=observacion]                   motivo; es required solo con el diálogo abierto
//                                                  (si no, el Enter en un campo chocaría con un required oculto)
//     button[data-cerrar]                          vuelve a la edición
//     button[data-confirmar] (type=submit)         envía el formulario
import { estadoRango, haCambiado, resumirCambios } from './edicionResultados';
import { toast } from './toast';

const ETIQUETA_RANGO = { bajo: '↓ Bajo', alto: '↑ Alto' };

function aLimite(valor) {
    if (valor === undefined || valor === null || valor === '') return null;
    const numero = Number(valor);
    return Number.isFinite(numero) ? numero : null;
}

function leerFila(fila) {
    const campo = fila.querySelector('[data-campo]');
    return {
        fila,
        campo,
        id: fila.dataset.id,
        nombre: fila.dataset.nombre,
        original: fila.dataset.original ?? '',
        actual: campo?.value ?? '',
        min: aLimite(fila.dataset.min),
        max: aLimite(fila.dataset.max),
    };
}

function pintarRango(elemento, rango) {
    if (!elemento) return;
    const fuera = rango === 'bajo' || rango === 'alto';
    elemento.textContent = fuera ? ETIQUETA_RANGO[rango] : '';
    elemento.hidden = !fuera;
}

function textoContador(cantidad) {
    if (cantidad === 0) return 'Sin cambios';
    return cantidad === 1 ? '1 cambio' : `${cantidad} cambios`;
}

function celda(texto, clase) {
    const td = document.createElement('td');
    td.textContent = texto;
    if (clase) td.className = clase;
    return td;
}

function pintarResumen(tbody, cambios) {
    tbody.replaceChildren(
        ...cambios.map((cambio) => {
            const tr = document.createElement('tr');
            tr.className = 'border-b border-borders/60 align-top';
            tr.append(
                celda(cambio.nombre, 'py-2 pr-3 font-medium uppercase'),
                celda(cambio.anterior || 'Vacío', 'py-2 pr-3 text-gray-500 line-through decoration-gray-400 dark:text-gray-400'),
            );
            const nuevo = celda(cambio.nuevo || 'Vacío', 'py-2 pr-3 font-semibold');
            if (cambio.rango === 'bajo' || cambio.rango === 'alto') {
                const aviso = document.createElement('span');
                aviso.className = 'ml-2 rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-200';
                aviso.textContent = `${ETIQUETA_RANGO[cambio.rango]} · fuera de rango`;
                nuevo.append(aviso);
            }
            tr.append(nuevo);
            return tr;
        }),
    );
}

export function montarEdicionResultados(form) {
    const filas = [...form.querySelectorAll('[data-parametro]')];
    const contador = form.querySelector('[data-contador]');
    const botonRevisar = form.querySelector('[data-revisar]');
    const dialogo = form.querySelector('[data-dialogo-revision]');
    const resumen = dialogo?.querySelector('[data-resumen]');
    const motivo = dialogo?.querySelector('[name="observacion"]');
    let enviando = false;

    const cambiosActuales = () => resumirCambios(filas.map(leerFila));

    function refrescar() {
        let cantidad = 0;
        filas.forEach((fila) => {
            const datos = leerFila(fila);
            const modificado = haCambiado(datos.original, datos.actual);
            if (modificado) cantidad += 1;
            fila.dataset.modificado = String(modificado);
            pintarRango(
                fila.querySelector('[data-rango-nuevo]'),
                modificado ? estadoRango(datos.actual, datos.min, datos.max) : null,
            );
        });
        if (contador) contador.textContent = textoContador(cantidad);
        if (botonRevisar) botonRevisar.disabled = cantidad === 0;
        return cantidad;
    }

    function abrirRevision() {
        const cambios = cambiosActuales();
        if (cambios.length === 0) {
            toast.warning('No hay cambios para guardar.', { key: 'edicion-resultados' });
            return;
        }
        pintarResumen(resumen, cambios);
        if (motivo) motivo.required = true;
        dialogo.showModal();
        motivo?.focus();
    }

    form.addEventListener('input', refrescar);
    form.addEventListener('change', refrescar);
    botonRevisar?.addEventListener('click', abrirRevision);
    dialogo?.querySelector('[data-cerrar]')?.addEventListener('click', () => dialogo.close());
    dialogo?.addEventListener('close', () => {
        if (motivo && !enviando) motivo.required = false;
    });

    // Enter en un campo envía el formulario: se fuerza a pasar por la revisión.
    form.addEventListener('submit', (evento) => {
        const desdeDialogo = evento.submitter?.hasAttribute('data-confirmar') && dialogo?.open;
        if (!desdeDialogo) {
            evento.preventDefault();
            abrirRevision();
            return;
        }
        if (cambiosActuales().length === 0) {
            evento.preventDefault();
            dialogo.close();
            return;
        }
        enviando = true;
        evento.submitter.disabled = true;
        evento.submitter.textContent = 'Guardando…';
    });

    window.addEventListener('beforeunload', (evento) => {
        if (!enviando && cambiosActuales().length > 0) {
            evento.preventDefault();
            evento.returnValue = '';
        }
    });

    refrescar();
    if (form.dataset.abrirRevision === 'true' && dialogo) {
        abrirRevision();
    }
}

export function montarEdicionesResultados(raiz = document) {
    raiz.querySelectorAll('form[data-edicion-resultados]').forEach(montarEdicionResultados);
}
