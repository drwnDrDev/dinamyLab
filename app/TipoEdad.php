<?php
namespace App;

enum TipoEdad: string
{
    case ANIOS = 'años';
    case MESES = 'meses';
    case DIAS  = 'días';

    public function nombre(): string
    {
        return match($this) {
            self::ANIOS => 'Años',
            self::MESES => 'Meses',
            self::DIAS  => 'Días',
        };
    }

    public function codigo(): int
    {
        return match($this) {
            self::ANIOS => 1,
            self::MESES => 2,
            self::DIAS  => 3,
        };
    }

    public static function fromCodigo(int $codigo): ?self
    {
        return match($codigo) {
            1 => self::ANIOS,
            2 => self::MESES,
            3 => self::DIAS,
            default => self::ANIOS, // Default to ANIOS if invalid code
        };
    }

    public function esValido(int $valor): bool
    {
        return match($this) {
            self::ANIOS => $valor > 0 && $valor <= 130,
            self::MESES => $valor > 0 && $valor < 12,
            self::DIAS  => $valor >= 0 && $valor < 31,
        };
    }
}
