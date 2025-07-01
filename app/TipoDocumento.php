<?php

namespace App;

enum TipoDocumento: string
{
    // Paciente
    case CC = 'CC';
    case TI = 'TI';
    case RC = 'RC';
    case CE = 'CE';
    case PA = 'PA';
    case MS = 'MS';
    case AS = 'AS';
    case NUIP = 'NUIP';
    case PE = 'PE';
    case CD = 'CD';
    case NV = 'NV';

    // Pagador (DIAN)
    case COD_13 = '13';
    case COD_22 = '22';
    case COD_31 = '31';
    case COD_41 = '41';
    case COD_42 = '42';
    case COD_50 = '50';
    case COD_91 = '91';

    public function nombre(): string
    {
        return match($this) {
            self::CC => 'Cédula de Ciudadanía',
            self::TI => 'Tarjeta de Identidad',
            self::RC => 'Registro Civil',
            self::CE => 'Cédula de Extranjería',
            self::PA => 'Pasaporte',
            self::MS => 'Menor sin identificación',
            self::AS => 'Adulto sin identificación',
            self::NUIP => 'Número único de identificación personal',
            self::PE => 'Permiso especial de permanencia',
            self::CD => 'Carné diplomático',
            self::NV => 'Nacido vivo sin identificación',
            self::COD_13 => 'Cédula de Ciudadanía (DIAN)',
            self::COD_22 => 'Cédula de Extranjería (DIAN)',
            self::COD_31 => 'NIT',
            self::COD_41 => 'Pasaporte (DIAN)',
            self::COD_42 => 'Documento extranjero',
            self::COD_50 => 'NIT de otro país',
            self::COD_91 => 'NUIP (DIAN)'
        };
    }

    public static function forLocalStorage(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[] = [
                'value' => $case->value,
                'label' => $case->nombre()
            ];
        }
        return $items;
    }

    public static function soloPacientes(): array
    {
        return [
            self::CC, self::TI, self::RC, self::CE, self::PA,
            self::MS, self::AS, self::NUIP, self::PE, self::CD, self::NV
        ];
    }

    public static function soloPagadores(): array
    {
        return [
            self::COD_13, self::COD_22, self::COD_31, self::COD_41,
            self::COD_42, self::COD_50, self::COD_91
        ];
    }
}
