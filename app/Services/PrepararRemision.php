<?php

namespace App\Services;

use App\Models\IpsExterna;
use App\Models\ValorRemision;
use Illuminate\Validation\ValidationException;

class PrepararRemision
{
    public static function reglas(string $campo): array
    {
        $tarifa = "{$campo}.*.valor_remision_id";

        return [
            $tarifa => ['nullable', 'integer', 'exists:valores_remision,id'],
            "{$campo}.*.ips_externa_id" => ['nullable', 'integer', 'exists:ips_externas,id'],
            "{$campo}.*.cup" => ["required_without:{$tarifa}", 'nullable', 'string', 'max:20'],
            "{$campo}.*.nombre" => ["required_without:{$tarifa}", 'nullable', 'string', 'max:255'],
            "{$campo}.*.valor" => ["required_without:{$tarifa}", 'nullable', 'numeric', 'min:0'],
            "{$campo}.*.destino" => ["required_without_all:{$tarifa},{$campo}.*.ips_externa_id", 'nullable', 'string', 'max:255'],
        ];
    }

    public static function paraOrden(array $datos): array
    {
        if (! empty($datos['valor_remision_id'])) {
            $tarifa = ValorRemision::with(['remision', 'ipsExterna'])->findOrFail($datos['valor_remision_id']);

            // Los identificadores y el costo se resuelven en servidor; el precio al paciente es elegible.
            return [
                'valor_remision_id' => $tarifa->id,
                'ips_externa_id' => $tarifa->ips_externa_id,
                'cup' => $tarifa->remision->cup,
                'nombre' => $tarifa->remision->nombre,
                'valor' => $datos['valor'] ?? $tarifa->precio,
                'valor_remitido' => $tarifa->valor_remitido,
                'destino' => $tarifa->ipsExterna->nombre,
            ];
        }

        $ips = ! empty($datos['ips_externa_id']) ? IpsExterna::findOrFail($datos['ips_externa_id']) : null;
        $destino = $ips?->nombre ?? trim($datos['destino'] ?? '');
        if ($destino === '') {
            throw ValidationException::withMessages(['remisiones' => 'Indique el profesional o la IPS de destino.']);
        }

        return [
            'valor_remision_id' => null,
            'ips_externa_id' => $ips?->id,
            'cup' => trim($datos['cup']),
            'nombre' => trim($datos['nombre']),
            'valor' => $datos['valor'],
            'valor_remitido' => null,
            'destino' => $destino,
        ];
    }
}
