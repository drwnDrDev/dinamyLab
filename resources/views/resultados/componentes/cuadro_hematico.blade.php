<section class="p-4 max-w-3xl mx-auto bg-white dark:bg-gray-800 dark:text-white shadow sm:rounded-lg">
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>

    <!-- Hematocrito -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">hematocrito</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['hto'] ?? '' }}
            @else

            <input type="number" class="w-24 h-8 border border-spacing-0 rounded" step="0.001" name="hto"
                id="hto">
            @endif
        </div>
        <div class="p-1">%</div>

            <div class="p-1 text-center">42-52</div>


    </div>

    <!-- Hemoglobina -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">hemoglobina</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['hb'] ?? '' }}
            @else
            <input type="number" class="w-24 h-8 border border-spacing-0 rounded" step="0.001" name="hb"
                id="hb">
            @endif
        </div>
        <div class="p-1">g%</div>
        <div class="p-1 text-center">13.5-16.5</div>
    </div>

    <!-- Recuento de leucocitos -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">recuento de leucocitos</div>
        <div class="p-1 text-end">
      
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['leu'] ?? '' }}
            @else
            <input type="number" class="w-24 h-8 border border-spacing-0 rounded" step="0.001" name="leu"
                id="leu">
            @endif
        </div>
        <div class="p-1 ">leu/mm³</div>
        <div class="p-1 text-center">5000-10000</div>
    </div>

    <!-- Recuento diferencial -->
    <div class="p-1 font-bold uppercase w-full">recuento diferencial</div>

    <!-- Neutrofilos -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 ml-1 uppercase">neutrofilos</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['neutrofilos'] ?? '' }}
            @else
            <input step="0.001" type="number" class="w-24 h-8 border border-spacing-0 rounded" name="neutrofilos"
                id="neutrofilos">
            @endif
        </div>
        <div class="p-1">%</div>
        <div class="p-1 text-center">52-67</div>
    </div>

    <!-- Linfocitos -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 ml-1 uppercase">linfocitos</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['linfocitos'] ?? '' }}
            @else
            <input step="0.001" type="number" class="w-24 h-8 border border-spacing-0 rounded" name="linfocitos"
                id="linfocitos">
            @endif

        </div>
        <div class="p-1">%</div>
        <div class="p-1 text-center">27-42</div>
    </div>

    <!-- Eosinofilos -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 ml-1 uppercase">eosinofilos</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['eosinofilos'] ?? '' }}
            @else
            <input step="0.001" type="number" class="w-24 h-8 border border-spacing-0 rounded" name="eosinofilos"
                id="eosinofilos">
            @endif
        </div>
        <div class="p-1">%</div>
        <div class="p-1 text-center">0-3</div>
    </div>

    <!-- Monocitos -->
    <div class="grid grid-cols-4 text-lg gap-1">


        <div class="p-1 ml-1 uppercase">monocitos</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['monocitos'] ?? '' }}
            @else
            <input step="0.001" type="number" class="w-24 h-8 border border-spacing-0 rounded" name="monocitos"
                id="monocitos">
            @endif
        </div>
        <div class="p-1 text-start">%</div>
        <div class="p-1 text-center">3-7</div>
    </div>

    <!-- Celulas inmaduras -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 ml-1 uppercase">celulas inmaduras</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['inmaduras'] ?? '' }}
            @else
            <input step="0.001" type="number" class="w-24 h-8 border border-spacing-0 rounded" name="inmaduras"
                id="inmaduras">
            @endif
        </div>
        <div class="p-1">%</div>
        <div class="p-1"></div>
    </div>

    <!-- Recuento de plaquetas -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">recuento de plaquetas</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['rto_plaquetas'] ?? '' }}
              @else
            <input type="number" class="w-24 h-8 border border-spacing-0 rounded" step="0.001" name="plaquetas"
                id="plaquetas">
            @endif
        </div>
        <div class="p-1">plaq/mm³</div>
        <div class="p-1 text-center">150000-450000</div>
    </div>

    <!-- VSG -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">vsg</div>
        <div class="p-1 text-end">
            @if (isset($isResultado) && $isResultado)
                {{ $resultados['vsg'] ?? '' }}
            @else
            <input type="number" class="w-24 h-8 border border-spacing-0 rounded" step="0.001" name="vsg"
                id="vsg">
            @endif
        </div>
        <div class="p-1">mm/h</div>
        <div class="p-1 text-center">0-22</div>
    </div>

    <!-- Observaciones -->
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-1 font-semibold uppercase">observaciones</div>

            @if (isset($isResultado) && $isResultado)
                {{ $resultados['observacion'] ?? '' }}
            @else
        <div class="p-1">
            <input type="text" name="observacion" id="observacion">
        </div>
            @endif


    </div>
    @if (!isset($isResultado) || !$isResultado)

    <button type="submit"

        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-4 p-8">
        enviar
    </button>
    @endif
</section>
