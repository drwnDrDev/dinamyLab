<section>
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end ">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> Ácido úrico</div>
        <div class="p-2 text-right">
        @if ($resultados['acido_urico']>6 || $resultados['acido_urico']<1.5)
            *
        @endif
         {{ $resultados['acido_urico'] ?? '' }}
        </div>

        <div class="p-2">
            mg/dL
        </div>
        <div class="p-2 text-center">
            1.5 - 6.0
        </div>
    </div>


</section>
