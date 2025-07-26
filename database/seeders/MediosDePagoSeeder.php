<?php

namespace Database\Seeders;

use App\Models\MetodoPago;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediosDePagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Código DIAN	Descripción del Medio de Pago
        // 10	Efectivo
        // 20	Cheque
        // 30	Tarjeta de crédito
        // 31	Tarjeta de débito
        // 41	Transferencia bancaria
        // 42	Depósito en cuenta de pago electrónico (billeteras digitales como Nequi, Daviplata, etc.)
        // 43	Recaudo en corresponsal bancario
        // 44	Recaudo en red de pagos
        // 45	Pago por botón PSE
        // 46	Pago por pasarela de pagos
        // 47	Pago con bono electrónico
        // 48	Pago con tarjeta prepago
        // 49	Pago con otros instrumentos electrónicos
        MetodoPago::updateOrCreate(
            ['codigo' => 10],
            [
                'nombre' => 'Efectivo',
                'nivel' => 3,
                'descripcion' => 'Pago en efectivo en el punto de venta'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 20],
            ['nombre' => 'Cheque']
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 30],
            [
                'nombre' => 'Tarjeta de crédito',
                'nivel' => 1,
                'descripcion' => 'Pago con tarjeta de crédito en el punto de venta',
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 31],
            [
                'nombre' => 'Tarjeta de débito',
                'nivel' => 1,
                'descripcion' => 'Pago con tarjeta de débito en el punto de venta'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 41],
            [
                'nombre' => 'Transferencia bancaria',
                'nivel' => 1,
                'descripcion' => 'Transferencia bancaria a la cuenta del vendedor'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 42],
            [
                'descripcion' => 'Depósito en cuenta de pago electrónico (billeteras digitales como Nequi, Daviplata, etc.)',
                'nombre' => 'Depósito en billetera electrónica',
                'nivel' => 2
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 43],
            ['nombre' => 'Recaudo en corresponsal bancario']
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 44],
            ['nombre' => 'Recaudo en red de pagos']
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 45],
            [
                'nombre' => 'Pago por botón PSE',
                'nivel' => 1,
                'descripcion' => 'Pago a través del botón PSE en línea'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 46],
            [
                'nombre' => 'Pago por pasarela de pagos',
                'nivel' => 1,
                'descripcion' => 'Pago a través de una pasarela de pagos en línea'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 47],
            [
                'nombre' => 'Pago con bono electrónico',
                'nivel' => 1,
                'descripcion' => 'Pago utilizando un bono electrónico o cupón digital'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 48],
            [
                'nombre' => 'Pago con tarjeta prepago',
                'nivel' => 1,
                'descripcion' => 'Pago utilizando una tarjeta prepago o de regalo'
            ]
        );
        MetodoPago::updateOrCreate(
            ['codigo' => 49],
            ['nombre' => 'Pago con otros instrumentos electrónicos']
        );
    }
}
