<?php

namespace App\Services;

class ParseadorListaPersonas
{
    /**
     * Parsea una lista de personas en formato CSV
     * Formato esperado: "Nombres Apellidos, Número_Documento"
     * El número de documento es opcional
     *
     * @param string $contenido Contenido separado por saltos de línea
     * @param string $tipoDocumentoDefault Tipo de documento por defecto (ej: 'CC')
     * @return array
     */
    public static function parsear(string $contenido, string $tipoDocumentoDefault = 'CC'): array
    {
        $lineas = preg_split('/\r\n|\r|\n/', trim($contenido));
        $personas = [];

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea)) {
                continue;
            }

            $persona = self::parsearLinea($linea, $tipoDocumentoDefault);
            if ($persona) {
                $personas[] = $persona;
            }
        }

        return $personas;
    }

    /**
     * Parsea una línea individual
     *
     * @param string $linea
     * @param string $tipoDocumentoDefault
     * @return array|null
     */
    private static function parsearLinea(string $linea, string $tipoDocumentoDefault): ?array
    {
        // Dividir por comas
        $partes = array_map('trim', explode(',', $linea));

        if (empty($partes[0])) {
            return null;
        }

        $nombresCompletos = $partes[0];
        $numeroDocumento = isset($partes[1]) ? trim($partes[1]) : '';

        // Parsear nombres y apellidos
        $nombreData = self::parsearNombresApellidos($nombresCompletos);

        return [
            'tipo_documento' => $tipoDocumentoDefault,
            'numero_documento' => $numeroDocumento ?: null,
            'primer_nombre' => $nombreData['primer_nombre'] ?? '',
            'segundo_nombre' => $nombreData['segundo_nombre'] ?? '',
            'primer_apellido' => $nombreData['primer_apellido'] ?? '',
            'segundo_apellido' => $nombreData['segundo_apellido'] ?? '',
            'nombres_completos' => $nombresCompletos,
            'existente' => false,
        ];
    }

    /**
     * Intenta parsear nombres y apellidos de una cadena
     * Estrategia: busca patrones comunes
     *
     * @param string $nombrompleto
     * @return array
     */
    private static function parsearNombresApellidos(string $nombreCompleto): array
    {
        $palabras = preg_split('/\s+/', trim($nombreCompleto));
        $palabras = array_filter($palabras);

        if (count($palabras) < 2) {
            // Si solo hay una palabra, asumir que es nombre
            return [
                'primer_nombre' => $palabras[0] ?? '',
                'segundo_nombre' => '',
                'primer_apellido' => '',
                'segundo_apellido' => '',
            ];
        }

        // Palabras comunes que indican apellido
        $indicadoresApellido = [
            'de', 'del', 'la', 'las', 'los', 'el', 'van', 'von', 'der', 'den', 'etc',
        ];

        $indices = [];
        foreach ($palabras as $i => $palabra) {
            if (!in_array(strtolower($palabra), $indicadoresApellido)) {
                $indices[] = $i;
            }
        }

        // Heurística: si hay 4+ palabras significativas, probablemente sea:
        // Primer nombre, segundo nombre, primer apellido, segundo apellido
        // Si hay 3, probablemente sea: nombre, apellido, segundo apellido
        // Si hay 2, probablemente sea: nombre, apellido

        $result = [
            'primer_nombre' => '',
            'segundo_nombre' => '',
            'primer_apellido' => '',
            'segundo_apellido' => '',
        ];

        $countPalabras = count($palabras);

        if ($countPalabras == 2) {
            $result['primer_nombre'] = $palabras[0];
            $result['primer_apellido'] = $palabras[1];
        } elseif ($countPalabras == 3) {
            $result['primer_nombre'] = $palabras[0];
            $result['primer_apellido'] = $palabras[1];
            $result['segundo_apellido'] = $palabras[2];
        } elseif ($countPalabras >= 4) {
            $result['primer_nombre'] = $palabras[0];
            $result['segundo_nombre'] = $palabras[1];
            $result['primer_apellido'] = $palabras[2];
            $result['segundo_apellido'] = $palabras[3];
        }

        return $result;
    }

    /**
     * Enriquece los datos parseados con información de personas existentes
     *
     * @param array $personasParseadas
     * @return array
     */
    public static function enriquecerConDatosExistentes(array $personasParseadas): array
    {
        foreach ($personasParseadas as &$persona) {
            if (!empty($persona['numero_documento'])) {
                // Aquí se podría agregar lógica para buscar la persona en la BD
                // por ahora solo marcamos que podría existir
                $persona['puede_buscar'] = true;
            }
        }

        return $personasParseadas;
    }
}
