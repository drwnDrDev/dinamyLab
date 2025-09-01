
import { dom } from './variables.js';
import { handleBuscarDocumento,handleGuardarPersona, validacionTiposDocumento, handleBuscarMunicipio } from './manejadorEventos.js';
import { displayEps, displayPaieses, displayDocumentos } from './formularioPersona.js';


const init = async () => {

    document.querySelector('input[name="numero_documento"]').addEventListener('blur', handleBuscarDocumento);
    dom.crearPaciente.addEventListener('submit', async(e) => {
        e.preventDefault();
        const result = await handleGuardarPersona(e);
        if(result && dom.guardarOtro.checked){
            alert("Paciente guardado exitosamente.");
            dom.crearPaciente.reset();
            document.querySelector('input[name="numero_documento"]').focus();
            return;
        }
        if(result){
            window.location.href = `/personas/${result.id}`;
        }

    });
    document.querySelector('select[name="pais_residencia"]').addEventListener('focus',() => displayPaieses(document.querySelector('select[name="pais_residencia"]')));
    document.querySelector('select[name="pais"]').addEventListener('focus', () => displayPaieses(document.querySelector('select[name="pais"]')));
    document.querySelector('input[name="eps"]').addEventListener('focus', () => {
        displayEps(dom.listaEps);
    });
    document.querySelector('select[name="tipo_documento"]').addEventListener('focus', () => {
        displayDocumentos(document.querySelector('select[name="tipo_documento"]'));
    });
    document.querySelector('select[name="tipo_documento"]').addEventListener('change', (e) => {
        validacionTiposDocumento(e.target);
    });

    handleBuscarMunicipio(document.querySelector('input[name="municipioBusqueda"]'));


}

document.addEventListener('DOMContentLoaded', init);
