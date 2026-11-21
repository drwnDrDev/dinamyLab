export const normalizarBusquedaRemision = (texto = '') => String(texto)
    .normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();

export function filtrarRemisiones(catalogo, busqueda) {
    const termino = normalizarBusquedaRemision(busqueda);
    return catalogo.filter(remision => normalizarBusquedaRemision(`${remision.cup} ${remision.nombre}`).includes(termino));
}

export function crearRemisionSeleccionada(remision, tarifa, precio = tarifa.precio) {
    return {
        valor_remision_id: tarifa.id,
        ips_externa_id: tarifa.ips_externa_id,
        cup: remision.cup,
        nombre: remision.nombre,
        valor: precio === '' ? '' : Number(precio),
        valor_remitido: tarifa.valor_remitido,
        destino: tarifa.ips_externa.nombre,
    };
}
