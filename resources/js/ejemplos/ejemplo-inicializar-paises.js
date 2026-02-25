// Script de ejemplo para inicializar datos en localhost
// Ejecutar en la consola del navegador una sola vez

// Opción 1: Usando los datos predefinidos
import { inicializarPaisesLocalStorage } from '/resources/js/utils/paisesLocalStorage.js';
inicializarPaisesLocalStorage();

// Opción 2: Cargar desde una API (si existe)
async function cargarPaisesDelServidor() {
  try {
    const response = await fetch('/api/paises');
    const data = await response.json();
    const paises = data.map(p => p.nombre); // Ajusta según tu estructura de datos
    localStorage.setItem('paises', JSON.stringify(paises));
    console.log('Países cargados del servidor');
  } catch (error) {
    console.error('Error cargando países:', error);
  }
}

// Opción 3: Datos personalizados
const misPaises = [
  'Colombia',
  'Argentina',
  'Brasil',
  'Chile',
  'Ecuador',
  'Perú',
  'Venezuela',
  'México',
  'España',
];
localStorage.setItem('paises', JSON.stringify(misPaises));
console.log('Países personalizados cargados');

// Verificar que se guardó correctamente
console.log('Países en localStorage:', JSON.parse(localStorage.getItem('paises')));
