<section>
    <x-encabezado-examen/>
    <x-parametro-resultado 
        :resultado="$resultados['col_total']"
        :isNormal="isset($resultados['col_total']) && $resultados['col_total'] <= 200"
        unidad="mg/dL"
        vReferencia="Hasta 200"
    >
    <x-slot name="p_nombre">
        Colesterol Total
    </x-slot>
    </x-parametro-resultado>
  

</section>
