<?php

namespace App;

enum Estado: string
{
    case PENDIENTE = 'pendiente de muestra';
    case MUESTRA = 'toma muestra';
    case PROCESO = 'en proceso';
    case TERMINADO = 'terminado';
    case ENTREGADO = 'entregado';
    case ANULADO = 'anulado';


    public function nombre(): string
    {
        return match($this) {

    self::PENDIENTE => 'pendiente de muestra',
    self::MUESTRA => 'toma muestra',
    self::PROCESO => 'en proceso',
    self::TERMINADO => 'terminado',
    self::ENTREGADO => 'entregado',
    self::ANULADO => 'anulado'

        };
    }
    public function color(): string
    {
        return match($this) {
            self::PENDIENTE => 'bg-yellow-500',
            self::MUESTRA => 'bg-blue-500',
            self::PROCESO => 'bg-orange-500',
            self::TERMINADO => 'bg-green-500',
            self::ENTREGADO => 'bg-purple-500',
            self::ANULADO => 'bg-red-500'
        };
    }

}
