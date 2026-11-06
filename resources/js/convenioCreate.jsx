import React from 'react';
import { createRoot } from 'react-dom/client';
import ConvenioForm from './components/ConvenioForm';


// Obtener el contenedor del DOM donde se montará el componente
const rootElement = document.getElementById('convenio-form-root');

if (rootElement) {
  const documentosElement = document.getElementById('documentos-data');
  let documentos = [];

  // Si hay datos embebidos en el HTML, usarlos
  if (documentosElement) {
    try {
      documentos = JSON.parse(documentosElement.textContent);
    } catch (error) {
      console.error('Error al parsear documentos:', error);
    }
  }

  const root = createRoot(rootElement);
  root.render(<ConvenioForm documentos={documentos} />);
} else {
  console.warn('No se encontró el elemento con ID "convenio-form-root"');
}
