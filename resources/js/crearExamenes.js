import {dom,appState} from './variables.js'

 const createExamenItemElement = (examen) => {
        const item = document.createElement('div');
        item.className = 'examen-item flex items-center justify-between gap-2 p-2 border border-borders rounded-sm shadow-sm';
        item.dataset.examenId = examen.id; // Usar data-attributes es más limpio

        const valorInicial = parseFloat(examen.valor).toFixed(2);
        const totalActual = (examen.currenTotal ?? (examen.valor * (examen.cantidad || 0))).toFixed(2);

        item.innerHTML = `
            <div class="grid grid-cols-2 flex-grow">
                <label for="examen-${examen.id}" class="text-lg font-semibold">${examen.nombre}</label>
                <span id="precio-${examen.id}"
                class="text-sm ${examen.currenTotal ? 'text-gray-900' : 'text-gray-500'} precio" >
                    $ ${totalActual}
                </span>

                <span class="${examen.currenTotal ? 'hidden' : 'flex w-24 h-10 text-sm'}" >
                    CIE principal:
                    <select name="cie_principal[${examen.id}]" class="w-32 ml-1 px-1 py-0.5 border border-borders rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Seleccione</option>
                        <option value="A00">A00 - Cólera</option>
                        <option value="B00">B00 - Herpesviral [herpes simple] infecciones</option>
                        <option value="C00">C00 - Neoplasia maligna de labio</option>
                        <option value="D00">D00 - Neoplasia in situ de cavidad bucal, esófago y estómago</option>
                        <option value="E00">E00 - Trastornos por deficiencia de yodo</option>
                        <option value="F00">F00 - Demencia en la enfermedad de Alzheimer</option>
                    </select>
                    </label>
                </span>
            </div>
            <input type="number" id="examen-${examen.id}" name="examenes[${examen.id}]"
                   class="flex w-20 px-2 py-1 text-center rounded border border-borders bg-white focus:outline-none focus:ring-2 focus:ring-primary"
                   min="0" value="${examen.cantidad || 0}" step="1">

        `;
        return item;
    };


   export const renderExamenes = (listaExamenes) => {
        dom.examenesContainer.innerHTML = ''; // Limpiar eficientemente el contenedor.
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
