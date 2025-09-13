import {dom,appState} from './variables.js'


const createCieSelectOptions = (examen, remover = null) => {

       const cieOptions = (examen.cieOptions || []).map(cie => ({
            value: cie.codigo,
            text: `${cie.codigo} - ${cie.nombre}`
        }));

        if(cieOptions.length === 0 && appState.defaultCies.length > 0) {
            appState.defaultCies.forEach(cie => {
                cieOptions.push({value: cie.codigo, text: `${cie.codigo} - ${cie.nombre}`});
            });
        }


        if (remover) {
            const index = cieOptions.findIndex(cie => cie.value === remover);
            if (index !== -1) {
                cieOptions.splice(index, 1);
            }
        }
        if(cieOptions.length === 0){
            cieOptions.push({value: '', text: 'Buscar CIE...'});
        }


    return cieOptions.map(opt => `<option value="${opt.value}">${opt.text}</option>`).join('\n');
}

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


                    <label for="cie_principal[${examen.id}]" class="col-span-2 flex w-24 h-10 text-sm ">
                    <select name="cie_principal[${examen.id}]" id="cie_principal[${examen.id}]"
                     class=" w-32 ml-1 px-1 py-0.5 border border-borders rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ${examen.cantidad > 0 ? '' : 'disabled'}>
                        ${createCieSelectOptions(examen)}
                    </select>
                    </label>

                <label for="cie_secundario[${examen.id}]" class="col-span-2 flex justify-center text-sm">

                    <select name="cie_secundario[${examen.id}]" id="cie_secundario[${examen.id}]"
                    class="w-full ml-1 px-1 py-0.5 border border-borders rounded focus:outline-none focus:ring-2 focus:ring-primary"
                    ${examen.cantidad > 0 ? 'aria-disabled="false"' : 'disabled'}>
                        ${createCieSelectOptions(examen, examen.ciePrincipal ?? null)}
                    </select>

                </label>


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
