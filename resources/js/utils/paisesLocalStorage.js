// Lista de 193 países reconocidos por las Naciones Unidas
export const PAISES_LISTA = [
  'Afganistán',
  'Albania',
  'Alemania',
  'Andorra',
  'Angola',
  'Antigua y Barbuda',
  'Arabia Saudita',
  'Argelia',
  'Argentina',
  'Armenia',
  'Australia',
  'Austria',
  'Azerbaiyán',
  'Bahamas',
  'Bangladés',
  'Barbados',
  'Baréin',
  'Bélgica',
  'Belice',
  'Benín',
  'Bielorrusia',
  'Birmania',
  'Bolivia',
  'Bosnia y Herzegovina',
  'Botsuana',
  'Brasil',
  'Brunéi',
  'Bulgaria',
  'Burkina Faso',
  'Burundi',
  'Bután',
  'Cabo Verde',
  'Camboya',
  'Camerún',
  'Canadá',
  'Catar',
  'Chad',
  'Chile',
  'China',
  'Chipre',
  'Ciudad del Vaticano',
  'Colombia',
  'Comoras',
  'Congo',
  'Corea del Norte',
  'Corea del Sur',
  'Costa de Marfil',
  'Costa Rica',
  'Croacia',
  'Cuba',
  'Dinamarca',
  'Dominica',
  'Ecuador',
  'Egipto',
  'El Salvador',
  'Emiratos Árabes Unidos',
  'Eslovaquia',
  'Eslovenia',
  'España',
  'Estados Unidos',
  'Estonia',
  'Esuatini',
  'Etiopía',
  'Filipinas',
  'Finlandia',
  'Fiyi',
  'Francia',
  'Gabón',
  'Gambia',
  'Georgia',
  'Ghana',
  'Gibraltar',
  'Grecia',
  'Groenlandia',
  'Guadalupe',
  'Guam',
  'Guatemala',
  'Guayana Francesa',
  'Guernsey',
  'Guinea',
  'Guinea Ecuatorial',
  'Guinea-Bisáu',
  'Guyana',
  'Haití',
  'Honduras',
  'Hong Kong',
  'Hungría',
  'India',
  'Indonesia',
  'Irak',
  'Irán',
  'Irlanda',
  'Isla de Man',
  'Islandia',
  'Islas Åland',
  'Islas Caimán',
  'Islas Cocos',
  'Islas Cook',
  'Islas Feroe',
  'Islas Georgias del Sur',
  'Islas Heard y McDonald',
  'Islas Mariana del Norte',
  'Islas Marshall',
  'Islas Pitcairn',
  'Islas Salomón',
  'Islas Turcos y Caicos',
  'Islas Vírgenes Británicas',
  'Islas Vírgenes Estadounidenses',
  'Israel',
  'Italia',
  'Jamaica',
  'Japón',
  'Jersey',
  'Jordania',
  'Kazajistán',
  'Kenia',
  'Kirguistán',
  'Kiribati',
  'Kuwait',
  'Laos',
  'Lesoto',
  'Letonia',
  'Líbano',
  'Líbia',
  'Liechtenstein',
  'Lituania',
  'Luxemburgo',
  'Macao',
  'Macedonia',
  'Madagascar',
  'Malasia',
  'Malawi',
  'Maldivas',
  'Mali',
  'Malta',
  'Marruecos',
  'Martinica',
  'Mauricio',
  'Mauritania',
  'Mayotte',
  'México',
  'Micronesia',
  'Moldavia',
  'Mónaco',
  'Mongolia',
  'Montenegro',
  'Montserrat',
  'Mozambique',
  'Namibia',
  'Nauru',
  'Nepal',
  'Nicaragua',
  'Níger',
  'Nigeria',
  'Niue',
  'Noruega',
  'Nueva Caledonia',
  'Nueva Zelanda',
  'Omán',
  'Países Bajos',
  'Pakistán',
  'Palaos',
  'Palestina',
  'Panamá',
  'Papúa Nueva Guinea',
  'Paraguay',
  'Perú',
  'Polinesia Francesa',
  'Polonia',
  'Portugal',
  'Puerto Rico',
  'Qatar',
  'Réunion',
  'Reino Unido',
  'República Centroafricana',
  'República Checa',
  'República Dominicana',
  'República del Congo',
  'República Democrática del Congo',
  'Ruanda',
  'Rumania',
  'Rusia',
  'Sahara Occidental',
  'Samoa',
  'Samoa Americana',
  'San Cristóbal y Nieves',
  'San Marino',
  'San Vicente y las Granadinas',
  'Santa Elena',
  'Santa Lucía',
  'Santo Tomé y Príncipe',
  'Senegal',
  'Serbia',
  'Seychelles',
  'Sierra Leona',
  'Singapur',
  'Siria',
  'Somalia',
  'Sri Lanka',
  'Sudáfrica',
  'Sudán',
  'Sudán del Sur',
  'Suecia',
  'Suiza',
  'Surinam',
  'Tailandia',
  'Taiwán',
  'Tanzania',
  'Tayikistán',
  'Timor Oriental',
  'Togo',
  'Tokelau',
  'Tonga',
  'Trinidad y Tobago',
  'Túnez',
  'Turkmenistán',
  'Turquía',
  'Tuvalu',
  'Ucrania',
  'Uganda',
  'Uruguay',
  'Uzbekistán',
  'Vanuatu',
  'Venezuela',
  'Vietnam',
  'Wallis y Futuna',
  'Yemen',
  'Yibuti',
  'Zambia',
  'Zimbabue',
];

/**
 * Inicializa los países en localStorage
 * Ejecutar una sola vez o cuando sea necesario actualizar
 */
export function inicializarPaisesLocalStorage() {
  const paisesExistentes = localStorage.getItem('paises');
  if (!paisesExistentes) {
    localStorage.setItem('paises', JSON.stringify(PAISES_LISTA));
    console.log('Países inicializados en localStorage');
  }
}

/**
 * Obtiene los países del localStorage
 * @returns {string[]} Array de países
 */
export function obtenerPaisesLocalStorage() {
  const paisesLocal = localStorage.getItem('paises');
  if (paisesLocal) {
    try {
      return JSON.parse(paisesLocal);
    } catch (error) {
      console.error('Error al obtener países del localStorage:', error);
      return [];
    }
  }
  return [];
}

/**
 * Actualiza la lista de países en localStorage
 * @param {string[]} paises - Array de países a guardar
 */
export function actualizarPaisesLocalStorage(paises) {
  try {
    localStorage.setItem('paises', JSON.stringify(paises));
    console.log('Países actualizados en localStorage');
  } catch (error) {
    console.error('Error al actualizar países en localStorage:', error);
  }
}

/**
 * Filtra países por búsqueda
 * @param {string} busqueda - Término de búsqueda
 * @param {string[]} paises - Array de países a filtrar
 * @returns {string[]} Array filtrado
 */
export function filtrarPaises(busqueda, paises = []) {
  if (!busqueda || busqueda.length < 3) {
    return [];
  }
  return paises.filter((pais) =>
    pais.toLowerCase().includes(busqueda.toLowerCase())
  );
}
