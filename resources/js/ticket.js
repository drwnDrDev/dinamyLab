

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