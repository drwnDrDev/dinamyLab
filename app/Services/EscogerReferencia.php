<?php
namespace App\Services;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\ValorReferencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EscogerReferencia
{
    /**
     * Obtiene los datos demográficos necesarios para escoger una referencia.
     */
    public static function datosDemograficos(Persona $paciente, Carbon $fecha): array
    {
        $edad = optional($paciente->fecha_nacimiento)->diffInYears($fecha);

        if (is_null($edad) || $edad < 0) {
            Log::warning('Edad inválida para paciente.', ['paciente_id' => $paciente->id]);
            return [];
        }

        return [
            'sexo'   => $paciente->sexo === 'M' ? 'hombres' : 'mujeres',
            'etario' => $edad < 5 ? 'menores' : 'adultos',
        ];
    }

    /**
     * Selecciona el valor de referencia más apropiado según datos demográficos.
     */
    public static function estableceReferencia(array $datosDemograficos, Parametro $parametro): ?ValorReferencia
    {
        $referencias = $parametro->valoresReferencia ?? collect();

        if ($referencias->isEmpty()) return null;
        if ($referencias->count() === 1) return $referencias->first();

        foreach (['etario', 'sexo'] as $clave) {
            $valor = $datosDemograficos[$clave] ?? null;
            if ($valor) {
                $match = $referencias->firstWhere('demografia', $valor);
                if ($match) return $match;
            }
        }

        return $referencias->first();
    }

    /**
     * Recorre los parámetros de un procedimiento y obtiene sus referencias.
     */
    public static function recorrerParametrosExamen(Procedimiento $procedimiento): array
    {
        $datosDemograficos = self::datosDemograficos(
            $procedimiento->orden->paciente,
            Carbon::parse($procedimiento->fecha)
        );

        if (empty($datosDemograficos)) return [];

        $parametros = [];

        $parametrosE = $procedimiento->examen
            ->parametros()
            ->with(['valoresReferencia', 'opciones']) // eager load
            ->withPivot('orden')
            ->orderBy('pivot_orden')
            ->get();

        foreach ($parametrosE as $parametro) {
            $referencia = self::estableceReferencia($datosDemograficos, $parametro);

            $parametros[] = [
                'id'        => $parametro->id,
                'nombre'    => $parametro->nombre,
                'grupo'     => $parametro->grupo,
                'tipo_dato' => $parametro->tipo_dato,
                'default'   => $parametro->default,
                'metodo'    => $parametro->metodo,
                'unidades'  => $parametro->unidades,
                'referencia'=> optional($referencia)->salida,
                'opciones'  => $parametro->opciones->pluck('valor')->all(),
            ];
        }

        return $parametros;
    }

    /**
     * Guarda resultados para un procedimiento.
     */
    public static function guardaResultado(array $formData, Procedimiento $pro): void
    {
        $datosDemograficos = self::datosDemograficos(
            $pro->orden->paciente,
            Carbon::parse($pro->fecha)
        );

        foreach ($formData as $parametroId => $valorResultado) {
            $parametro = Parametro::with('valoresReferencia')->find($parametroId);
            if (!$parametro) continue;

            $referencia = self::estableceReferencia($datosDemograficos, $parametro);

            $isNormal = true;
            if ($referencia) {
                try {
                    $valorResultado = floatval($valorResultado);
                } catch (\Exception $e) {
                    Log::error('Error al convertir el valor del resultado a float.', [
                        'parametro_id' => $parametro->id,
                        'valor' => $valorResultado,
                        'error' => $e->getMessage(),
                    ]);
                    continue; // Skip this parameter if conversion fails
                }
                $isNormal = $valorResultado < $referencia->max && $valorResultado > $referencia->min;
            }
            Resultado::create([
                'parametro_id'     => $parametro->id,
                'resultado'        => $valorResultado,
                'procedimiento_id' => $pro->id,
                'es_normal'        => $isNormal,
            ]);
        }
    }
}
