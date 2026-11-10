<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Sexo extends Model
{
    public const FALLBACK = [
        'F' => 'Femenino',
        'I' => 'Indeterminado o Intersexual',
        'M' => 'Masculino',
    ];

    protected $table = 'sexos';

    protected $primaryKey = 'codigo';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'cod_sexo',
    ];

    /**
     * Opciones para selects y formularios: código canónico => nombre.
     *
     * @return array<string, string>
     */
    public static function opciones(): array
    {
        try {
            $opciones = static::query()
                ->orderBy('nombre')
                ->pluck('nombre', 'codigo')
                ->all();
        } catch (\Throwable) {
            $opciones = [];
        }

        return $opciones !== [] ? $opciones : self::FALLBACK;
    }

    public static function porCodigo(?string $codigo): ?self
    {
        if ($codigo === null || $codigo === '') {
            return null;
        }

        try {
            return static::query()->find($codigo);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Normaliza un código de persona al conjunto canónico F/M/I.
     */
    public static function normalizar(?string $codigo): ?string
    {
        $codigo = self::limpiar($codigo);

        if ($codigo === null) {
            return null;
        }

        return match ($codigo) {
            'H', 'M' => 'M',
            'F' => 'F',
            'I' => 'I',
            default => throw new InvalidArgumentException("Código de sexo no válido: {$codigo}"),
        };
    }

    /**
     * Traduce un código canónico al código usado por RIPS.
     */
    public static function aRips(?string $codigo): ?string
    {
        return match (self::normalizar($codigo)) {
            null => null,
            'F' => 'M',
            'M' => 'H',
            'I' => 'I',
        };
    }

    /**
     * Traduce un código RIPS al código canónico de personas.
     */
    public static function desdeRips(?string $codigo): ?string
    {
        $codigo = self::limpiar($codigo);

        if ($codigo === null) {
            return null;
        }

        return match ($codigo) {
            'H' => 'M',
            'M' => 'F',
            'I' => 'I',
            default => throw new InvalidArgumentException("Código RIPS de sexo no válido: {$codigo}"),
        };
    }

    private static function limpiar(?string $codigo): ?string
    {
        if ($codigo === null) {
            return null;
        }

        $codigo = strtoupper(trim($codigo));

        return $codigo === '' ? null : $codigo;
    }
}
