<?php

namespace App\Support;

class StringHelper
{
    protected static array $particulas = [
        'de', 'del', 'la', 'las', 'los', 'y', 'san', 'santa'
    ];

    public static function titlecaseConParticulas(string $texto): string
    {
        $palabras = explode(' ', mb_strtolower(trim($texto)));

        return implode(' ', array_map(function ($palabra, $index) {
            if (in_array($palabra, self::$particulas) && $index !== 0) {
                return $palabra;
            }
            return mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");
        }, $palabras, array_keys($palabras)));
    }
}
