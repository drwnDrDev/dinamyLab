<section>
    <x-encabezado-examen/>
    <x-parametro-resultado 
        :resultado="$resultados['col_ldl']"
        :isNormal="isset($resultados['col_ldl']) && $resultados['col_ldl'] <= 130"
        unidad="mg/dL"
        vReferencia="Óptimo menor a 130"
    >
    <x-slot name="p_nombre">
        Colesterol LDL
    </x-slot>
    </x-parametro-resultado>


</section>
