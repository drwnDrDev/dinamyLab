// Lógica pura de la edición de resultados (ED-04): qué cambió y si un valor sale del rango.
// Sin DOM, para poder probarla con node:test. El cableado está en edicionResultadosDom.js.

function limpiar(valor) {
    return valor === null || valor === undefined ? '' : String(valor).trim();
}

// El servidor recorta los espacios (TrimStrings), así que aquí se compara igual.
export function haCambiado(original, actual) {
    return limpiar(original) !== limpiar(actual);
}

function aNumero(valor) {
    const texto = limpiar(valor).replace(',', '.');
    if (texto === '' || !/^[-+]?(\d+\.?\d*|\.\d+)(e[-+]?\d+)?$/i.test(texto)) {
        return null;
    }
    const numero = Number(texto);
    return Number.isFinite(numero) ? numero : null;
}

/**
 * 'bajo' | 'alto' | 'normal', o null cuando no se puede evaluar
 * (sin referencia, campo vacío o valor no numérico).
 */
export function estadoRango(valor, min, max) {
    const tieneMin = typeof min === 'number' && Number.isFinite(min);
    const tieneMax = typeof max === 'number' && Number.isFinite(max);
    if (!tieneMin && !tieneMax) {
        return null;
    }

    const numero = aNumero(valor);
    if (numero === null) {
        return null;
    }

    if (tieneMin && numero < min) return 'bajo';
    if (tieneMax && numero > max) return 'alto';
    return 'normal';
}

/**
 * @param {{id, nombre, original, actual, min, max}[]} filas
 * @returns {{id, nombre, anterior, nuevo, rango}[]} solo los parámetros modificados
 */
export function resumirCambios(filas) {
    return filas
        .filter((fila) => haCambiado(fila.original, fila.actual))
        .map((fila) => ({
            id: fila.id,
            nombre: fila.nombre,
            anterior: limpiar(fila.original),
            nuevo: limpiar(fila.actual),
            rango: estadoRango(fila.actual, fila.min, fila.max),
        }));
}
