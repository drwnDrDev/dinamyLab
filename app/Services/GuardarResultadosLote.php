<?php

namespace App\Services;

use App\Estado;
use App\Models\Examen;
use App\Models\Procedimiento;
use App\Models\Resultado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GuardarResultadosLote
{
    public function guardar(Examen $examen, int $sedeId, int $empleadoId, array $muestras): int
    {
        $ids = array_map(fn (array $muestra) => (int) $muestra['id'], $muestras);
        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages([
                'procedimientos' => 'No se puede incluir una muestra más de una vez.',
            ]);
        }

        return DB::transaction(function () use ($examen, $sedeId, $empleadoId, $muestras, $ids) {
            $procedimientos = Procedimiento::query()
                ->whereKey($ids)
                ->where('examen_id', $examen->id)
                ->where('sede_id', $sedeId)
                ->where('estado', Estado::PROCESO->value)
                ->whereDoesntHave('resultado')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($procedimientos->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'procedimientos' => 'Una o más muestras ya no están disponibles para registrar resultados.',
                ]);
            }

            $parametros = $examen->parametros()->with('opciones')->orderBy('posicion')->get()->keyBy('id');
            if ($parametros->isEmpty()) {
                throw ValidationException::withMessages([
                    'procedimientos' => 'El examen no tiene parámetros configurados.',
                ]);
            }

            foreach ($muestras as $indice => $muestra) {
                $resultados = $muestra['resultados'] ?? [];
                $parametrosDesconocidos = array_diff(
                    array_map('intval', array_keys($resultados)),
                    $parametros->keys()->map(fn ($id) => (int) $id)->all()
                );

                if ($parametrosDesconocidos !== []) {
                    throw ValidationException::withMessages([
                        "procedimientos.{$indice}.resultados" => 'El lote contiene parámetros que no pertenecen a este examen.',
                    ]);
                }

                foreach ($resultados as $parametroId => $valor) {
                    if ($valor === null || $valor === '') {
                        continue;
                    }

                    $parametro = $parametros->get((int) $parametroId);
                    $rules = ['value' => ['required', 'string', 'max:255']];

                    if ($parametro->tipo_dato === 'number') {
                        $rules['value'][] = 'numeric';
                    } elseif ($parametro->tipo_dato === 'date') {
                        $rules['value'][] = 'date_format:Y-m-d';
                    } elseif ($parametro->tipo_dato === 'select') {
                        $rules['value'][] = Rule::in($parametro->opciones->pluck('valor')->map(fn ($opcion) => (string) $opcion)->all());
                    }

                    $validator = Validator::make(['value' => $valor], $rules);
                    if ($validator->fails() || ($parametro->tipo_dato === 'number' && ! is_finite((float) $valor))) {
                        throw ValidationException::withMessages([
                            "procedimientos.{$indice}.resultados.{$parametroId}" => 'El resultado no tiene un formato válido para este parámetro.',
                        ]);
                    }
                }
            }

            foreach ($muestras as $muestra) {
                $procedimiento = $procedimientos->get((int) $muestra['id']);

                foreach ($muestra['resultados'] ?? [] as $parametroId => $valor) {
                    if ($valor === null || $valor === '') {
                        continue;
                    }

                    $parametro = $parametros->get((int) $parametroId);
                    Resultado::create([
                        'parametro_id' => $parametro->id,
                        'procedimiento_id' => $procedimiento->id,
                        'posicion' => $parametro->posicion,
                        'resultado' => $valor,
                    ]);
                }

                $procedimiento->estado = Estado::TERMINADO->value;
                $procedimiento->fecha = now();
                $procedimiento->empleado_id = $empleadoId;
                $procedimiento->save();
            }

            return count($muestras);
        });
    }
}
