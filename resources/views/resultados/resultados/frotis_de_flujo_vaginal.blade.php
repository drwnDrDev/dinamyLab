<section>
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end ">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> pH</div>
        <div class="p-2">
            <input type="number" class="w-24 h-8 bordrder border-spacing-0 rounded" step="0.001" name="ph"
                id="ph">
        </div>

    </div>
    <div class="p-2 font-bold uppercase w-full"> Examen Fresco</div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> Celulas Epiteliales</div>
        <div class="p-2">
            <input type="text" name="cel_epiteliales" id="cel_epiteliales">
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> leucocitos</div>
        <div class="p-2">
            <input type="text" name="leucocitos" id="leucocitos">
        </div>
        <div class="p-2">
            xC
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> hematies</div>
        <div class="p-2">
            <input type="text" name="hematies" id="hematies">
        </div>
        <div class="p-2">
            xC
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> bacterias</div>
        <div class="p-2">
            <input type="text" name="bacterias" value="negativo" id="bacterias">
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> hongos</div>
        <div class="p-2">
            <input type="text" name="hongos" value="negativo" id="hongos">
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> trichomona vaginalis</div>
        <div class="p-2">
            <input type="text" name="trichomona" value="negativo" id="trichomona">
        </div>
    </div>


    <div class="p-2 font-bold uppercase w-full"> examen microscópico</div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> coloración de Gram</div>
        <div class="p-2">
            <input type="text" name="coloracion" id="coloracion">
        </div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 ml-1 uppercase"> reaccion leucocitaria</div>
        <div class="p-2">
            <input type="text" name="r_leu" id="r_leu">
        </div>
    </div>


    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> informe</div>
        <div class="p-2">
            <input type="text" name="informe" id="informe">
        </div>

    </div>

    <button type="submit"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-4 p-8">
        envair
    </button>

</section>
