<?php

namespace App;

enum Impuesto:string
{
    case IVA = 'IVA';
    case ICA = 'ICA';
    case RET= 'retención en la fuente';

    public function nombre(): string
    {
        return match($this) {
            self::IVA => 'Impuesto al Valor Agregado',
            self::ICA => 'Impuesto de Industria y Comercio',
            self::RET => 'Retención en la Fuente',
        };
    }
    public static function forLocalStorage(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[] = ['value' => $case->value, 'label' => $case->nombre()];
        }
        return $items;
    }
    public function tasa(): float
    {
        return match($this) {
            self::IVA => 0.19,
            self::ICA => 0.004, // Ejemplo, puede variar según la actividad económica y el municipio
            self::RET => 0.015, // Ejemplo, puede variar según el tipo de ingreso
        };
    }
    public function codigo(): string
    {
        return match($this) {
            self::IVA => '01',
            self::ICA => '02',
            self::RET => '03',
        };
    }

}
