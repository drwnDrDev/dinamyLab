import axios from "axios";

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
axios.defaults.headers.common['Accept'] = 'application/json';

const busqudaMunicipio = document.getElementById('busqudaMunicipio');
const numeroDocumento = document.getElementById('numero_documento');
const tipoDocumento = document.getElementById('tipo_documento');
const pais = document.getElementById('pais');
const nacional = pais?.value=="COL" || pais==null;


tipoDocumento.addEventListener('change', function (e) {
    const tipo = tipoDocumento.value;
    if (tipo === 'CC') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de cédula');
        pais.className = 'hidden';


    } else if (tipo === 'TI') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de tarjeta de identidad');
        pais.className = 'hidden';
    } else if (tipo === 'CE') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de cédula de extranjería');
         pais.className = 'hidden';
    } else if (tipo === 'PA') {
        numeroDocumento.setAttribute('maxlength', '10');
        numeroDocumento.setAttribute('placeholder', 'Número de pasaporte');
        pais.style.display = 'flex';
        pais.addEventListener('focus', function (e) {
            axios.get('/api/paises')
                .then(response => {
                  
                    if (response.data) {
                        
                        response.data.data.paises.forEach(p => {
                            const option = document.createElement('option');
                            option.value = p.codigo_iso;
                            option.textContent = p.nombre;
                            option.className= ['text-gray-900', 'dark:text-gray-100'];
                            
                            pais.appendChild(option);
                        });
                    } else {
                        alert("No se encontró el país");
                    }
                })
                .catch(error => {
                    console.error('Error fetching countries:', error);
                });
        });

    }
});

pais.addEventListener('change', function (e) {
    const paisSeleccionado = pais;
    

    if (paisSeleccionado !== 'COL') {
      numeroDocumento.value = paisSeleccionado.value;
    }
})
// let municipios = axios.get('/api/municipios')
//     .then(response => {
//         if (response.data) {
//             municipios = response.data;
//         } else {
//             alert("No se encontró el municipio");
//         }
//     })
//     .catch(error => {
//         console.error('Error fetching municipios:', error);
//     });


// // Función para mostrar resultados (puedes adaptarla a tu HTML)
// function mostrarResultados(resultados) {
//     // Ejemplo: mostrar en consola
//     console.log('Resultados:', resultados);
// }

// busqudaMunicipio.addEventListener('keyup', function (e) {
//     const busqueda = busqudaMunicipio.value.trim().toLowerCase();
//     if (busqueda.length === 0) {
//         mostrarResultados([]);
//         return;
//     }
//     const resultados = municipios.filter(municipio =>
//         municipio.nombre.toLowerCase().includes(busqueda)
//     );
//     mostrarResultados(resultados);
// });
