<section>
    <x-encabezado-examen/>
    <x-parametro-resultado 
        :resultado="$resultados['psa']"
        :isNormal=" (isset($resultados['psa']) && $resultados['psa'] <= 4) || !is_numeric($resultados['psa'])"
        unidad="ng/mL"
        vReferencia="0 - 4"
    >
    <x-slot name="p_nombre">
        PSA
    </x-slot>
    </x-parametro-resultado>
    <x-observaciones-resultado>
       {{ $resultados['observacion'] ?? 'No hay observaciones para este resultado.' }}
    </x-observaciones-resultado>
</section>
