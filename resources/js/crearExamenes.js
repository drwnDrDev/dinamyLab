 const createExamenItemElement = (examen) => {
        const item = document.createElement('div');
        item.className = 'examen-item flex items-center justify-between gap-2 p-2 border border-borders rounded-sm shadow-sm';
        item.dataset.examenId = examen.id; // Usar data-attributes es más limpio

        const valorInicial = parseFloat(examen.valor).toFixed(2);
        const totalActual = (examen.currenTotal ?? (examen.valor * (examen.cantidad || 0))).toFixed(2);

        item.innerHTML = `
            <div class="grid flex-grow">
                <label for="examen-${examen.id}" class="text-lg font-semibold">${examen.nombre}</label>
                <span id="precio-${examen.id}" class="text-sm ${examen.currenTotal ? 'text-gray-900' : 'text-gray-500'} precio" >
                    $ ${totalActual}
                </span>
            </div>
            <input type="number"  id="examen-${examen.id}" name="examenes[${examen.id}]"
                   class="w-20 px-2 py-1 text-center rounded border border-borders bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                   min="0" value="${examen.cantidad || 0}" step="1">
        `;
        return item;
    };


 export const renderExamenes = (listaExamenes,contenedor,totalSpan) => {
        contenedor.innerHTML = '';
        const fragment = document.createDocumentFragment(); // Usar un fragmento para mejor rendimiento.
        listaExamenes.forEach(examen => fragment.appendChild(createExamenItemElement(examen)));
        contenedor.appendChild(fragment);
        updateTotalExamenes(listaExamenes,totalSpan);
    };
const updateTotalExamenes = (exVisibles,totalSpan) => {
        const total = exVisibles.reduce((sum, ex) => sum + (ex.currenTotal || 0), 0);
        if (totalSpan) {
            totalSpan.textContent = `Total: $ ${total.toFixed(2)}`;
        }
    };
