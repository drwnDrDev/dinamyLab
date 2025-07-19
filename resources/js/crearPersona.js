
import { appState, dom } from './variables.js';

import {  handleBuscarDocumento} from './manejadorEventos.js';
import { displayEps, displayPaieses,displayMunicipios,dispalyDocumentos } from './formularioPersona.js';


const init = async () => {



    // Usar querySelectorAll para asignar el mismo evento a múltiples elementos
    document.querySelectorAll('input[name="numero_documento"]').forEach(input => {
        input.addEventListener('blur', handleBuscarDocumento);
    });

    document.querySelectorAll('select[name="pais"]').forEach(currenFormPais => {
        currenFormPais.addEventListener('focus',() => displayPaieses(currenFormPais));

    });
    document.querySelectorAll('input[name="eps"]').forEach(currenFormEps  => {
        currenFormEps.addEventListener('focus', () =>   {
             displayEps(dom.listaEps);
        });
    });
    document.querySelectorAll('select[name="tipo_documento"]').forEach(currentFormMunicipio => {
        currentFormMunicipio.addEventListener('focus', () => {
            dispalyDocumentos(currentFormMunicipio);
        });
        currentFormMunicipio.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            if (selectedOption) {
                const codRips = selectedOption.dataset.valor;
                const tipoDoc = appState.tiposDocumento.find(tipo => tipo.cod_rips === codRips);
                console.log('Tipo de documento seleccionado:', tipoDoc);
                if (!tipoDoc.cod_dian || tipoDoc.requiere_acudiente) {
                    console.log('Este tipo de documento requiere un acudiente o no tiene un código DIAN asociado.');
                }
            }
        }
        );
    }
    );
    document.querySelectorAll('input[name="municipioBusqueda"]').forEach(currentBusquedaFormMunicipio => {

        currentBusquedaFormMunicipio.addEventListener('input', () => {
            if (currentBusquedaFormMunicipio.value.length > 2) {
            const municipioBusquedaDiv = currentBusquedaFormMunicipio.form.querySelector('.municipio-busqueda');
            const searchValue = currentBusquedaFormMunicipio.value.toLowerCase();
            appState.filteredMunicipios = appState.municipios.filter(municipio =>
                municipio.municipio.toLowerCase().includes(searchValue) ||
                municipio.departamento.toLowerCase().includes(searchValue)
            );


            if (appState.filteredMunicipios.length > 0) {
                municipioBusquedaDiv.classList.remove('hidden');
            } else {
                municipioBusquedaDiv.classList.add('hidden');
            }
        }

            currentBusquedaFormMunicipio.innerHTML = ''; // Limpiar opciones

        }
        );


    }
    );

    document.querySelectorAll('select[name="municipio"]').forEach(currentFormMunicipio => {
        currentFormMunicipio.addEventListener('focus',()=>displayMunicipios(currentFormMunicipio))
    })


}

document.addEventListener('DOMContentLoaded', init);
