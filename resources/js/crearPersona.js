
import { dom } from './variables.js';
import { handleBuscarDocumento,validacionTiposDocumento,handleBuscarMunicipio } from './manejadorEventos.js';
import { displayEps, displayPaieses,displayDocumentos } from './formularioPersona.js';


const init = async () => {


    // Usar querySelectorAll para asignar el mismo evento a múltiples elementos
    document.querySelector('input[name="numero_documento"]').addEventListener('blur', handleBuscarDocumento);

    document.querySelector('select[name="pais"]').addEventListener('focus',() => displayPaieses(document.querySelector('select[name="pais"]')));
    document.querySelector('input[name="eps"]').addEventListener('focus', () =>   {
             displayEps(dom.listaEps);
        });;
    document.querySelector('select[name="tipo_documento"]').addEventListener('focus', () => {
            displayDocumentos(document.querySelector('select[name="tipo_documento"]'));
        });
    document.querySelector('select[name="tipo_documento"]').addEventListener('change', (e) => {
            validacionTiposDocumento(e.target);
        });

   handleBuscarMunicipio(document.querySelector('input[name="municipioBusqueda"]'));


}

document.addEventListener('DOMContentLoaded', init);
