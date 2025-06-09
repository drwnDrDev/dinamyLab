const examen = document.getElementById('form-examenes');
console.log('Formulario de examen:', examen);
const orden = document.getElementById('numero-orden').value;

examen.addEventListener('submit', function(event) {
  alert('Enviando datos del formulario');
  event.preventDefault(); // Evita el envío del formulario
  const formData = new FormData(examen);
  const data = Object.fromEntries(formData.entries());
  const url = examen.getAttribute('action');
  axios.post(url, data, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Order-Number': orden, // Agrega el número de orden al encabezado
            'method': 'PATCH' // Método PATCH para actualizar datos
        }
    }).then(response => {
      // Maneja la respuesta exitosa
      console.log('Datos actualizados:', response.data);
      alert('Datos actualizados correctamente');
    }
    )
    .catch(error => {
      // Maneja el error
      console.error('Error al actualizar los datos:', error);
      alert('Error al actualizar los datos');
    }
  );
});

function imprimirSeccion(selector) {
    alert('Imprimiendo sección: ' + selector);
  const contenido = document.querySelector(selector).innerHTML;
  const ventana = window.open('', '_blank');
  ventana.document.write(`
    <html>
      <head>
        <title>Impresión</title>
        <style>
          body { font-family: sans-serif; padding: 20px; }
            h1 { text-align: center; }
            p { margin: 10px 0; }
            .ticket { border: 1px solid #000; padding: 10px; }
            .ticket h1 { font-size: 24px; }
            .ticket p { font-size: 16px; }
            .grid {
              display: grid;
              grid-template-columns: repeat(3, 1fr);
              gap: 10px;
            }
        </style>
      </head>
      <body>
        ${contenido}
      </body>
    </html>
  `);
  ventana.document.close();
  ventana.focus();
  ventana.print();
  ventana.close();
}
function copiarAlPortapapeles(selector) {
  const contenido = document.querySelector(selector).innerText;
  navigator.clipboard.writeText(contenido)
    .then(() => {
      alert('Contenido copiado al portapapeles');
    })
    .catch(err => {
      console.error('Error al copiar al portapapeles: ', err);
    });
}

document.getElementById('imprimir').addEventListener('click', function() {
  imprimirSeccion('#ticket');
});