<?php

namespace App\Services;

use App\Support\StringHelper;

class NombreParser
{
    protected static array $particulas = [
        'de', 'del', 'la', 'las', 'los', 'san', 'santa',
        'hijo','hija'
      // puedes agregar más
    ];

    public static function limpiarEspacios(string $texto): string
    {
        return preg_replace('/\s+/', ' ', trim($texto));
    }

   public static function dividir(string $completo): array
{
    $completo = self::limpiarEspacios(strtolower($completo));
    $palabras = explode(' ', $completo);
    $grupos = [];
    $i = 0;

    while ($i < count($palabras)) {
        $grupo = [];

        // Agrupar partículas consecutivas
        while ($i < count($palabras) && in_array($palabras[$i], self::$particulas)) {
            $grupo[] = $palabras[$i];
            $i++;
        }

        // Asegurar que haya al menos una palabra fuerte (como "Cruz", "Pilar", etc.)
        if ($i < count($palabras)) {
            $grupo[] = $palabras[$i];
            $i++;
        }

        $grupos[] = implode(' ', $grupo);
    }

    return [
        'primero' => $grupos[0] ?? '',
        'resto' => count($grupos) > 1 ? implode(' ', array_slice($grupos, 1)) : '',
    ];
}

public static function parsearPersona(string $nombres, string $apellidos): array
    {
        $nombresSeparados = self::dividir($nombres);
        $apellidosSeparados = self::dividir($apellidos);

        return [
            'primer_nombre' => $nombresSeparados['primero'],
            'segundo_nombre' => $nombresSeparados['resto'],
            'primer_apellido' => $apellidosSeparados['primero'],
            'segundo_apellido' => $apellidosSeparados['resto'],
        ];
    }

    public static function formatear(array $partes): array
    {
        $nombreCompleto = trim("{$partes['primer_nombre']} {$partes['segundo_nombre']}");
        $apellidoCompleto = trim("{$partes['primer_apellido']} {$partes['segundo_apellido']}");

        $nombreFormateado = StringHelper::titlecaseConParticulas($nombreCompleto);
        $apellidoFormateado = StringHelper::titlecaseConParticulas($apellidoCompleto);

        $iniciales = implode('.', array_map(fn($w) => strtoupper(mb_substr($w, 0, 1)), explode(' ', "$nombreCompleto $apellidoCompleto"))) . '.';

        return [
            'nombre_completo' => "$nombreFormateado $apellidoFormateado",
            'apellido_nombre' => "$apellidoFormateado, $nombreFormateado",
            'nombre_corto' => ucfirst($partes['primer_nombre']) . ' ' . strtoupper(mb_substr($partes['segundo_nombre'], 0, 1)) . '.',
            'nombres'=>$nombreFormateado,
            'apellidos'=>$apellidoFormateado,
            'iniciales' => $iniciales,
        ];
    }
}
