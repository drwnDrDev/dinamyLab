<section>
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end ">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>
    <div class="p-2 font-bold uppercase w-full"> Polimorfonucleares</div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal derecha</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="poli_de"
                id="poli_de">
        </div>
        <div class="p-2">
            %
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal izquierda</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="poli_iz"
                id="poli_iz">
        </div>
        <div class="p-2">
            %
        </div>
    </div>


    <div class="p-2 font-bold uppercase w-full"> Mononucleares</div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal derecha</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="mono_de"
                id="mono_de">
        </div>
        <div class="p-2">
            %
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal izquierda</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="mono_iz"
                id="mono_iz">
        </div>
        <div class="p-2">
            %
        </div>
    </div>


    <div class="p-2 font-bold uppercase w-full"> Eosinofilos</div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal derecha</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="eos_de"
                id="eos_de">
        </div>
        <div class="p-2">
            %
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Fosa nasal izquierda</div>
        <div class="p-2">
            <input step="0.001" type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" name="eos_iz"
                id="eos_iz">
        </div>
        <div class="p-2">
            %
        </div>
    </div>


    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> observaciones</div>
        <div class="p-2">
            <input type="text" name="observaciones" id="observaciones">
        </div>

    </div>

    <button type="submit"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-4 p-8">
        envair
    </button>

</section>
