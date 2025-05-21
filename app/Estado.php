<?php

namespace App;

enum Estado: string
{
    case ESPERA = 'sala de espera';
    case MUESTRA = 'toma muestra';
    case PENDIENTE = 'en propoceso';
    case TERMINADO = 'terminado';
    case ENTREGADO = 'entregado';
    case CANCELADO = 'cancelado';
    case RECHAZADO = 'rechazado';
    case ANULADO = 'anulado';
    case PENDIENTE_PAGO = 'pendiente de pago';

    public function nombre(): string
    {
        return match($this) {
            self::ESPERA => 'Sala de espera',
            self::MUESTRA => 'Toma de muestra',
            self::PENDIENTE => 'En proceso',
            self::TERMINADO => 'Terminado',
            self::ENTREGADO => 'Entregado',
            self::CANCELADO => 'Cancelado',
            self::RECHAZADO => 'Rechazado',
            self::ANULADO => 'Anulado',
            self::PENDIENTE_PAGO => 'Pendiente de pago'
        };
    }
    public function color(): string
    {
        return match($this) {
            self::ESPERA => 'bg-blue-500',
            self::MUESTRA => 'bg-yellow-500',
            self::PENDIENTE => 'bg-orange-500',
            self::TERMINADO => 'bg-green-500',
            self::ENTREGADO => 'bg-purple-500',
            self::CANCELADO => 'bg-red-500',
            self::RECHAZADO => 'bg-red-700',
            self::ANULADO => 'bg-gray-500',
            self::PENDIENTE_PAGO => 'bg-pink-500'
        };
    }

}
