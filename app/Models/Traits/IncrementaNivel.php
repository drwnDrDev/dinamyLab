<?php
namespace App\Models\Traits;

trait IncrementaNivel
{
    public static function incrementarNivel($codigo, $ventaja = 50): void
    {
        $item = self::where('codigo', $codigo)->first();
        if (!$item) {
            return;
        }
        $avgNivel = self::avg('nivel');
        if ($item->nivel < 255 && $item->nivel < $avgNivel + $ventaja) {
            $item->nivel += 1;
            $item->save();
        }
        if($avgNivel > 200) {
            self::resetearNiveles();
        }
    }

public static function resetearNiveles($top = 5): bool
{
    // Selecciona los primeros antes de modificar
    $primeros = self::orderBy('nivel', 'desc')->limit($top)->get();

    // Actualiza todos los niveles a 1
    $actualizar = self::query()->update(['nivel' => 1]);

    // Asigna niveles altos a los primeros seleccionados
    $nivel = $top + 2;
    foreach ($primeros as $item) {
        $item->nivel = $nivel--;
        $item->save();
    }
    return $actualizar ?? false;
}
}
