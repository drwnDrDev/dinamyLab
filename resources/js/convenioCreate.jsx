import React from 'react';
import { createRoot } from 'react-dom/client';
import ConvenioForm from './components/ConvenioForm';
import { inicializarPaisesLocalStorage } from './utils/paisesLocalStorage';

// Inicializar países en localStorage si no existen
inicializarPaisesLocalStorage();

// Obtener documentos que se pasarán como prop (simulados aquí, pero pueden venir del backend)
const documentosDefault = [
  { cod_dian: '91', nombre: 'NIT' },
  { cod_dian: '11', nombre: 'Cédula de Ciudadanía' },
  { cod_dian: '12', nombre: 'Cédula de Extranjería' },
  { cod_dian: '21', nombre: 'Tarjeta de Identidad' },
  { cod_dian: '22', nombre: 'Documento de Identificación Personal' },
];

// Obtener el contenedor del DOM donde se montará el componente
const rootElement = document.getElementById('convenio-form-root');

if (rootElement) {
  const documentosElement = document.getElementById('documentos-data');
  let documentos = documentosDefault;

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
