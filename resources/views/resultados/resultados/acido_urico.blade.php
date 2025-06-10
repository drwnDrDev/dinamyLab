<section>
    <x-encabezado-examen/>

    <x-parametro-resultado 

        :resultado="$resultados['acido_urico']"
        :isNormal="isset($resultados['acido_urico']) && $resultados['acido_urico'] >= 1.5 && $resultados['acido_urico'] <= 6.0"
        unidad="mg/dL"
        vReferencia="1.5 - 6.0"
    >
    <x-slot name="p_nombre">
        Ácido úrico
    </x-slot>
     
    </x-parametro-resultado>



</section>
