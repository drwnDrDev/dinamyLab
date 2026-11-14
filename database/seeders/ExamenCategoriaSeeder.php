<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Examen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                $categoria = Categoria::firstOrCreate(['nombre' => $grupo['nombre']]);
                $ids = [];
                foreach ($grupo['examenes'] as $item) {
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
