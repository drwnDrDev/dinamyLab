<?php

namespace App;

enum TipoDocumento: string
{
    case CC = 'CC';
    case TI = 'TI';
    case CE = 'CE';
    case RC = 'RC';
    case PA = 'PA';
    case AS = 'AS';
    case MS = 'MS';
    case PE = 'PE';
    case PT = 'PT';

    public function nombre(): string
    {
        return match($this) {
            self::CC => 'Cédula de Ciudadanía',
            self::TI => 'Tarjeta de Identidad',
            self::CE => 'Cédula de Extranjería',
            self::RC => 'Registro Civil',
            self::PT => 'Permiso de Permanencia',
            self::PA => 'Pasaporte',
            self::AS => 'Adulto Sin Identificación',
            self::MS => 'Menores Sin Identificación',
            self::PE => 'Permiso Especial',

        };
    }

}
