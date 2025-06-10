    
    <div class="grid grid-cols-4 text-lg gap-1">
        <x-nombre-parametro >{{$p_nombre}}</x-nombre-parametro>
        <x-resultado-campo-parametro :resultado="$resultado" :isNormal="$isNormal"/>
        @isset($unidad)
            <div class="p-2">
                {{ $unidad }}
            </div>          
        @endisset
        @isset($vReferencia)
            <div class="p-2 text-center">
                {{ $vReferencia }}
            </div>
         @endisset
    </div>