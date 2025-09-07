import {dom,appState} from './variables.js'

 const createExamenItemElement = (examen) => {
        const item = document.createElement('div');
        item.className = 'examen-item grid grid-cols-9 gap-2 border-b border-gray-200 dark:border-gray-700';
        item.dataset.examenId = examen.id; // Usar data-attributes es más limpio
        const totalActual = (examen.currenTotal ?? (examen.valor * (examen.cantidad || 0))).toFixed(2);
        item.innerHTML =`
                <p class="text-lg font-semibold col-span-3" aria-label="Nombre del examen">${examen.nombre}</p>
                <input type="number" id="examen-${examen.id}" name="examenes[${examen.id}]"
                   class="flex w-20 px-2 py-1 text-center rounded border border-borders bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                   min="0" value="${examen.cantidad || 0}" step="1">

                <p class="col-span-2 flex w-24 h-10 text-sm ">
                    <label for="cie_principal">
                    <select name="cie_principal[${examen.id}]"
                     class="w-32 ml-1 px-1 py-0.5 border border-borders rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ${examen.cantidad > 0 ? '' : 'disabled'}>
                        <option value="12056">Z017 - EXAMEN DE LABORATORIO</option>
                        <option value="B00">Z006 -EXAMEN PARA COMPARACION Y CONTROL NORMALES EN PROGRAMA DE INVESTIGACION CLINICA</option>

                    </select>
                    </label>
                </p>
                <p class="col-span-2 flex justify-center text-sm">

                    <select name="cie_secundario[${examen.id}]"
                    class="w-full ml-1 px-1 py-0.5 border border-borders rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ${examen.cantidad > 0 ? 'aria-disabled="false"' : 'disabled'}>
                        <option value="12056">Z017 - EXAMEN DE LABORATORIO</option>
                        <option value="B00">Z006 -EXAMEN PARA COMPARACION Y CONTROL NORMALES EN PROGRAMA DE INVESTIGACION CLINICA</option>
                    </select>

                </p>


            <p id="precio-${examen.id}"
                class="text-sm ${examen.currenTotal ? 'text-gray-900' : 'text-gray-500'} precio" >
                    $ ${totalActual}
            </p>

        `;
        return item;
    };


   export const renderExamenes = (listaExamenes) => {
        const cabecera = document.createElement('div');
        cabecera.className = 'grid grid-cols-9 gap-2 font-bold border-b-2 border-gray-300 dark:border-gray-600 pb-2 mb-2';
        cabecera.innerHTML = `
            <span class="col-span-3">Examen</span>
            <span class="text-center">Cantidad</span>
            <span class="col-span-2 text-center">CIE Principal</span>
            <span class="col-span-2 text-center">CIE Secundario</span>
            <span class="text-center">Precio</span>
        `;
        dom.examenesContainer.innerHTML = ''; // Limpiar eficientemente el contenedor.
        dom.examenesContainer.scrollTop = 0; // Resetear scroll al top.
        dom.examenesContainer.appendChild(cabecera); //
        const fragment = document.createDocumentFragment(); // Usar un fragmento para mejor rendimiento.
        listaExamenes.forEach(examen => fragment.appendChild(createExamenItemElement(examen)));
        dom.examenesContainer.appendChild(fragment);
        updateTotalExamenes();
    };

export const updateTotalExamenes = () => {
        const total = appState.examenesVisibles.reduce((sum, ex) => sum + (ex.currenTotal || 0), 0);
        if (dom.totalExamenesSpan) {
            dom.totalExamenesSpan.textContent = `Total: $ ${total.toFixed(2)}`;
        }

    };
