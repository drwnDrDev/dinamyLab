<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\ResultadoCambio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ActualizarResultados
{
    public function actualizar(
        Procedimiento $procedimiento,
        int $sedeId,
        array $valores,
        string $observacion,
        int $usuarioId,
        string $usuarioNombre
    ): int {
        return DB::transaction(function () use ($procedimiento, $sedeId, $valores, $observacion, $usuarioId, $usuarioNombre) {
            $procedimiento = Procedimiento::query()
                ->whereKey($procedimiento->id)
                ->where('sede_id', $sedeId)
                ->lockForUpdate()
                ->firstOrFail();

            $parametros = Examen::findOrFail($procedimiento->examen_id)
                ->parametros()
                ->with('opciones')
                ->get()
                ->keyBy('id');
            $resultados = $procedimiento->resultado()->lockForUpdate()->get()->keyBy('parametro_id');

            foreach ($valores as $parametroId => $valor) {
                $parametro = $parametros->get((int) $parametroId);
                if (! $parametro) {
                    throw ValidationException::withMessages([
                        "resultados.{$parametroId}" => 'El parámetro no pertenece a este examen.',
                    ]);
                }

                if ($valor === null || $valor === '') {
                    continue;
                }

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
                        "resultados.{$parametroId}" => 'El resultado no tiene un formato válido para este parámetro.',
                    ]);
                }
            }

            $cambios = 0;
            foreach ($valores as $parametroId => $valor) {
                $parametro = $parametros->get((int) $parametroId);
                $resultado = $resultados->get((int) $parametroId);
                $valorAnterior = $resultado?->resultado;
                $valorNuevo = $valor === '' ? null : $valor;

                if ((string) ($valorAnterior ?? '') === (string) ($valorNuevo ?? '')) {
                    continue;
                }

                if (! $resultado && $valorNuevo === null) {
                    continue;
                }

                if (! $resultado) {
                    $resultado = new Resultado([
                        'parametro_id' => $parametro->id,
                        'posicion' => $parametro->posicion,
                    ]);
                    $procedimiento->resultado()->save($resultado);
                }

                $resultado->resultado = $valorNuevo;
                $resultado->save();

                ResultadoCambio::create([
                    'procedimiento_id' => $procedimiento->id,
                    'resultado_id' => $resultado->id,
                    'parametro_id' => $parametro->id,
                    'usuario_id' => $usuarioId,
                    'parametro_nombre' => $parametro->nombre,
                    'valor_anterior' => $valorAnterior,
                    'valor_nuevo' => $valorNuevo,
                    'observacion' => $observacion,
                    'usuario_nombre' => $usuarioNombre,
                ]);
                $cambios++;
            }

            return $cambios;
        });
    }
}
