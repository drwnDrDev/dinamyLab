<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Examen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use UnexpectedValueException;

class ExamenCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $datos = json_decode(file_get_contents(resource_path('utils/listas/gemini-code-1794684327220.json')), true, 512, JSON_THROW_ON_ERROR);
        $normalizar = static fn (string $nombre) => preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii($nombre)));
        $alias = [
            'colesteroltotal' => 'colesterol',
            'coproanalisiscoprologico' => 'coprologico',
        ];
        $examenes = Examen::all();
        DB::transaction(function () use ($datos, $normalizar, $alias, $examenes) {
            foreach ($datos['categorias'] as $grupo) {
                if (! isset($grupo['nombre']) || ! is_string($grupo['nombre'])) {
                    throw new UnexpectedValueException('Cada categoría debe tener un nombre.');
                }

                if (isset($grupo['parametros']) && is_array($grupo['parametros'])) {
                    $items = [['nombre' => $grupo['nombre']]];
                } elseif (isset($grupo['examenes']) && is_array($grupo['examenes'])) {
                    $items = $grupo['examenes'];
                } else {
                    throw new UnexpectedValueException(
                        "La categoría {$grupo['nombre']} debe incluir una lista de examenes o parametros."
                    );
                }

                $categoria = Categoria::firstOrCreate(['nombre' => $grupo['nombre']]);
                $ids = [];
                foreach ($items as $item) {
                    if (! isset($item['nombre']) || ! is_string($item['nombre'])) {
                        throw new UnexpectedValueException(
                            "Cada entrada de {$grupo['nombre']} debe tener un nombre."
                        );
                    }

                    $clave = $normalizar($item['nombre']);
                    $clave = $alias[$clave] ?? $clave;
                    $examen = $examenes->first(function ($examen) use ($clave, $normalizar, $alias) {
                        foreach ([$examen->nombre, $examen->nombre_alternativo] as $nombre) {
                            $normal = $normalizar($nombre ?? '');
                            if (($alias[$normal] ?? $normal) === $clave) {
                                return true;
                            }
                        }
                        return false;
                    });
                    if ($examen) {
                        $ids[] = $examen->id;
                    } else {
                        $this->command?->warn("No existe el examen {$item['nombre']} para {$grupo['nombre']}");
                    }
                }
                $categoria->examenes()->syncWithoutDetaching($ids);
            }
        });
    }
}
