<section>
    <x-encabezado-examen/>
    <div class="p-2 font-bold uppercase w-full"> Examen fisico quimico</div>
    <x-parametro-resultado 
        :resultado="$resultados['color']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Color
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['consistencia']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Consistencia
        </x-slot>
    </x-parametro-resultado>
    
    <x-parametro-resultado 
        :resultado="$resultados['glucotest']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Glucotest
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['ph']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            pH
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['moco']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Moco
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['hemoglobina']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Hemoglobina
        </x-slot>
    </x-parametro-resultado>
    <div class="p-2 font-bold uppercase w-full"> Examen microscópico</div>
    <x-parametro-resultado 
        :resultado="$resultados['celulosa']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Celulosa
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['fibras_vegetales']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Fibras Vegetales
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['almidones']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Almidones
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['grasas_neutras']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Grasas Neutras
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['acidos_grasos']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Ácidos Grasos
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['leucocitos']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Leucocitos
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['hematies']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Hematies
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['lev_gemacion']"
        :isNormal="true"
        unidad="xC"
        >
        <x-slot name="p_nombre">
            Levaduras en Gemación
        </x-slot>
    </x-parametro-resultado>
    <x-parametro-resultado 
        :resultado="$resultados['f_bacteriana']"
        :isNormal="true"
        >
        <x-slot name="p_nombre">
            Flora Bacteriana
        </x-slot>
    </x-parametro-resultado>
    <div class="p-2 font-bold uppercase w-full"> Examen parasitológico</div>

    <x-observaciones-resultado >
    <x-slot name="name">
        Informe
    </x-slot>
        {{ $resultados['informe'] ?? 'No hay informe para este resultado.' }}
    </x-observaciones-resultado>
    <x-observaciones-resultado >
    <x-slot name="name">
        Coloración de Gram
    </x-slot>
        {{ $resultados['coloracion_gram'] ?? 'No hay coloración de Gram para este resultado.' }}
    </x-observaciones-resultado>

    <x-observaciones-resultado >
    <x-slot name="name">
        Observaciones
    </x-slot>
        {{ $resultados['observacion'] ?? 'No hay observaciones para este resultado.' }}
    </x-observaciones-resultado>
    
    </div>

</section>
