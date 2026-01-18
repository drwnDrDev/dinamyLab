# Módulo Cargador de Lista de Personas

## Descripción

Este módulo permite cargar una lista de personas en formato CSV (separadas por comas y saltos de línea) y precargar automáticamente los datos en el formulario `FormPersona`. Es especialmente útil para cargar múltiples personas de una sola vez, como pacientes, acompañantes, etc.

## Características

- **Parseo automático**: Interpreta nombres, apellidos y número de documento
- **Búsqueda de personas existentes**: Si el documento existe en la BD, trae todos los datos
- **Interfaz intuitiva**: Muestra resultados parseados para seleccionar la persona deseada
- **Flexible**: Soporta documentos opcionales y diferentes tipos de documento

## Formato de entrada esperado

```
Nombres Apellidos, Número de Documento
Carlos Ramirez, 1012555321
Luiz Alberto Diaz, 10101010
Zonia Ramirez Fierro,
Liliana Diaz Marun, 123123654
```

### Reglas de parsing:

- **Cada línea** contiene: `Nombres y Apellidos, Número de Documento`
- **El número de documento** es **opcional** (puede dejar vacío después de la coma)
- **Los espacios se ignoran** automáticamente
- **Las líneas vacías** se descartan

## Componentes

### 1. Backend

#### Servicio: `ParseadorListaPersonas` 
Ubicación: `/app/Services/ParseadorListaPersonas.php`

```php
ParseadorListaPersonas::parsear($contenido, $tipoDocumento = 'CC')
```

Métodos principales:
- `parsear(string $contenido, string $tipoDocumentDefault)`: Parsea el contenido y retorna un array de personas
- `enriquecerConDatosExistentes(array $personasParseadas)`: Busca las personas en BD

#### Controlador: `ListaPersonasController`
Ubicación: `/app/Http/Controllers/Api/ListaPersonasController.php`

Endpoint:
```
POST /api/personas/parsear-lista
```

Body:
```json
{
  "contenido": "Carlos Ramirez, 1012555321\nLuiz Alberto Diaz, 10101010",
  "tipo_documento": "CC"
}
```

Respuesta:
```json
{
  "message": "Lista parseada correctamente",
  "data": [
    {
      "id": null,
      "tipo_documento": "CC",
      "numero_documento": "1012555321",
      "primer_nombre": "Carlos",
      "segundo_nombre": "",
      "primer_apellido": "Ramirez",
      "segundo_apellido": "",
      "fecha_nacimiento": "",
      "sexo": "",
      "pais_origen": "170",
      "telefono": "",
      "zona": "02",
      "pais_residencia": "170",
      "correo": "",
      "eps": "",
      "tipo_afiliacion": "",
      "existente": false,
      "nombres_completos": "Carlos Ramirez"
    }
  ],
  "total": 1
}
```

### 2. Frontend

#### Componente: `CargadorListaPersonas`
Ubicación: `/resources/js/components/CargadorListaPersonas.jsx`

Props:
- `onPersonasLoaded` (function): Callback cuando se selecciona una persona
- `perfil` (string): Tipo de perfil a cargar (ej: "Paciente", "Acompañante")

#### Hook: `useListaPersonas`
Ubicación: `/resources/js/components/hooks/useListaPersonas.js`

Métodos:
- `cargarPersona(persona)`: Formatea una persona para usar en FormPersona
- `limpiar()`: Limpia el estado

## Cómo usar

### Opción 1: Integración en CrearOrdenComponent

```jsx
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';

const CrearOrdenComponent = () => {
    const [persona, setPersona] = useState(null);
    const { cargarPersona } = useListaPersonas();

    const handlePersonaSeleccionada = (personaDelCargador) => {
        const personaFormateada = cargarPersona(personaDelCargador);
        setPersona(personaFormateada);
    };

    return (
        <div>
            {!persona ? (
                <>
                    <CargadorListaPersonas 
                        onPersonasLoaded={handlePersonaSeleccionada}
                        perfil="Paciente"
                    />
                </>
            ) : (
                <FormPersona persona={persona} setPersona={setPersona} perfil="Paciente" />
            )}
        </div>
    );
};
```

### Opción 2: Componente independiente

```jsx
import CargadorListaPersonas from './CargadorListaPersonas';

const MisPersonas = () => {
    const handlePersonasLoaded = (persona) => {
        console.log('Persona cargada:', persona);
        // Aquí puedes hacer lo que quieras con la persona
    };

    return (
        <CargadorListaPersonas 
            onPersonasLoaded={handlePersonasLoaded}
            perfil="Acompañante"
        />
    );
};
```

## Lógica de parsing de nombres

El algoritmo intenta distribuir las palabras en:
- Primer nombre
- Segundo nombre (opcional)
- Primer apellido
- Segundo apellido (opcional)

### Ejemplos:

| Entrada | Primer Nombre | Segundo Nombre | Primer Apellido | Segundo Apellido |
|---------|---------------|----------------|-----------------|------------------|
| Carlos Ramirez | Carlos | | Ramirez | |
| Carlos Alberto Ramirez | Carlos | Alberto | Ramirez | |
| Carlos Alberto Ramirez López | Carlos | Alberto | Ramirez | López |
| Juan de la Cruz | Juan | | de | la |

## Búsqueda de personas existentes

Si el usuario proporciona un número de documento, el controlador:
1. Busca la persona en la BD por `numero_documento`
2. Si existe, retorna todos sus datos (fecha nacimiento, sexo, teléfono, etc.)
3. Si no existe, retorna solo los datos parseados

Esto permite:
- **Completar automáticamente** todos los campos si la persona ya está en el sistema
- **Pre-llenar solo lo necesario** si es una persona nueva

## Validación

### En el frontend:
- El contenido no puede estar vacío
- Se valida el formato básicamente verificando que no sea una línea vacía

### En el backend:
- Se valida que el `contenido` sea requerido y string
- El `tipo_documento` es opcional (por defecto 'CC')

## Tipos de documento soportados

- `CC` - Cédula de Ciudadanía
- `CE` - Cédula de Extranjería
- `PA` - Pasaporte
- `PE` - Permiso Especial
- `TI` - Tarjeta de Identidad
- `RC` - Registro Civil

(O cualquier otro código RIPS disponible en la tabla `tipos_documentos`)

## Rutas disponibles

En `routes/api.php`:
```php
Route::post('personas/parsear-lista', [ListaPersonasController::class, 'parsearLista']);
```

## Consideraciones de seguridad

- ✅ Usa autenticación `auth:sanctum` en la API
- ✅ Valida el contenido en el servidor
- ✅ Los datos se buscan en la BD, no se asumen
- ✅ CSRF token requerido (via axios.defaults.withCredentials)

## Ejemplo completo

```jsx
import React, { useState } from 'react';
import CargadorListaPersonas from './CargadorListaPersonas';
import FormPersona from './FormPersona';
import { useListaPersonas } from './hooks/useListaPersonas';

const MiComponente = () => {
    const [persona, setPersona] = useState(null);
    const { cargarPersona } = useListaPersonas();
    const [mostrarCargador, setMostrarCargador] = useState(true);

    const handlePersonaSeleccionada = (personaDelCargador) => {
        const personaFormateada = cargarPersona(personaDelCargador);
        setPersona(personaFormateada);
        setMostrarCargador(false);
    };

    return (
        <div>
            {mostrarCargador && !persona ? (
                <>
                    <CargadorListaPersonas 
                        onPersonasLoaded={handlePersonaSeleccionada}
                        perfil="Paciente"
                    />
                    <button onClick={() => setMostrarCargador(false)}>
                        O crear forma manual
                    </button>
                </>
            ) : null}

            {!mostrarCargador && !persona ? (
                <>
                    <FormPersona 
                        persona={null}
                        setPersona={setPersona}
                        perfil="Paciente"
                    />
                    <button onClick={() => setMostrarCargador(true)}>
                        ← Cargar desde lista
                    </button>
                </>
            ) : null}

            {persona && (
                <>
                    <div className="p-4 bg-green-100 rounded">
                        <h2>Persona seleccionada:</h2>
                        <p>{persona.data.primer_nombre} {persona.data.primer_apellido}</p>
                    </div>
                    <FormPersona 
                        persona={persona}
                        setPersona={setPersona}
                        perfil="Paciente"
                    />
                </>
            )}
        </div>
    );
};

export default MiComponente;
```

## Troubleshooting

### "Error al parsear la lista"
- Verifica que el contenido no esté vacío
- Asegúrate de usar saltos de línea (Enter) entre personas

### "No se encuentran personas"
- Probablemente no haya ningún número de documento o no estén registrados en la BD
- El módulo puede seguir creando nuevas personas, solo que sin datos previos

### "La persona existe pero no trae datos"
- Verifica que el número de documento coincida exactamente (sin espacios)
- Asegúrate de que la persona está correctamente registrada en la BD

## API detallada

### ParseadorListaPersonas

```php
// Parsear contenido
$personas = ParseadorListaPersonas::parsear(
    "Carlos Ramirez, 1012555321\nZonia Fierro,",
    'CC'
);

// Resultado
[
    [
        'tipo_documento' => 'CC',
        'numero_documento' => '1012555321',
        'primer_nombre' => 'Carlos',
        'segundo_nombre' => '',
        'primer_apellido' => 'Ramirez',
        'segundo_apellido' => '',
        'nombres_completos' => 'Carlos Ramirez',
        'existente' => false,
    ],
    [
        'tipo_documento' => 'CC',
        'numero_documento' => null,
        'primer_nombre' => 'Zonia',
        'segundo_nombre' => '',
        'primer_apellido' => 'Fierro',
        'segundo_apellido' => '',
        'nombres_completos' => 'Zonia Fierro',
        'existente' => false,
    ]
]
```

## Extensiones futuras

- [ ] Importar desde archivo Excel/CSV
- [ ] Validación de edad por tipo de documento
- [ ] Búsqueda difusa (fuzzy matching) para nombres similares
- [ ] Caché de búsquedas recientes
- [ ] Edición en línea de los datos parseados
- [ ] Guardado en lote de múltiples personas

