<section>
    <x-encabezado-examen/>

    <x-parametro-resultado 
        :resultado="$resultados['antiestreptolisina']"
        :isNormal="isset($resultados['antiestreptolisina']) && $resultados['antiestreptolisina'] <= 200"
        unidad="UI/mL"
        vReferencia="0 - 200"
    >
    <x-slot name="p_nombre">
        Antiestreptolisina O
    </x-slot>
    
    </x-parametro-resultado>


</section>
