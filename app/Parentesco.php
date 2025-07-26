<?php

namespace App;

enum Parentesco:string
{
    case Padre = 'Padre';
    case Madre = 'Madre';
    case Hermano = 'Hermano';
    case Hermana = 'Hermana';
    case Esposo = 'Esposo';
    case Esposa = 'Esposa';
    case Hijo = 'Hijo';
    case Hija = 'Hija';
    case Pareja = 'Pareja';
    case Abuelo = 'Abuelo';
    case Abuela = 'Abuela';
    case Tio = 'Tio';
    case Tia = 'Tia';
    case Primo = 'Primo';
    case Prima = 'Prima';
    case Vecino = 'Vecino';
    case Amigo = 'Amigo';
    case Otro = 'Otro';

    public function label(): string
    {
        return match ($this) {
            self::Padre => 'Padre',
            self::Madre => 'Madre',
            self::Hermano => 'Hermano',
            self::Hermana => 'Hermana',
            self::Esposo => 'Esposo',
            self::Esposa => 'Esposa',
            self::Hijo => 'Hijo',
            self::Hija => 'Hija',
            self::Pareja => 'Pareja',
            self::Abuelo => 'Abuelo',
            self::Abuela => 'Abuela',
            self::Tio => 'Tio',
            self::Tia => 'Tia',
            self::Primo => 'Primo',
            self::Prima => 'Prima',
            self::Vecino => 'Vecino',
            self::Amigo => 'Amigo',
            self::Otro => 'Otro',

            default => $this->value,
        };
    }
    public static function soloFemeninos(): array
    {
        return [
            self::Madre,
            self::Hermana,
            self::Esposa,
            self::Hija,
            self::Pareja,
            self::Abuela,
            self::Tia,
            self::Prima,
            self::Vecino, // Si consideras "Vecina" como femenino, pero aquí es "Vecino"
            self::Amigo,  // Si consideras "Amiga" como femenino, pero aquí es "Amigo"
            self::Otro
        ];
    }
    public static function soloMasculinos(): array
    {
        return [
            self::Padre,
            self::Hermano,
            self::Esposo,
            self::Hijo,
            self::Pareja,
            self::Abuelo,
            self::Tio,
            self::Primo,
            self::Vecino,
            self::Amigo,
            self::Otro
        ];
    }
}

