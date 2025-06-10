<section>
    <x-encabezado-examen/>
    <x-parametro-resultado 
        :resultado="$resultados['col_hdl']"
        :isNormal="isset($resultados['col_hdl']) && $resultados['col_hdl'] >= 65"
        unidad="mg/dL"
        vReferencia="Óptimo mayor a 65"
        >
    <x-slot name="p_nombre">
        Colesterol HDL
    </x-slot>

</x-parametro-resultado>

</section>
