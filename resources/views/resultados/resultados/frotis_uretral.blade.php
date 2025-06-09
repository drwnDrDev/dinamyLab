<section>
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end ">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> Coloración de gram</div>
        <div class="p-1 w-full col-span-4">
            <input list="l_gram_a" class="w-full" name="gram_a">
            <datalist id="l_gram_a">
                <option>cocos Gram positivos aislados escasos</option>
                <option>negativo para diplococos gram Negativos intra y extra celulares</option>
            </datalist>
        </div>

    </div>
    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase"> reacción leucocitaria</div>
        <div class="p-2">
            <select name="r_leuc" class="rounded-lg uppercase" id="r_leuc">
                <option value="escasa">escasa</option>
                <option value="moderada">moderada</option>
                <option value="aumentada">aumentada</option>
            </select>
        </div>

    </div>

    <button type="submit"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-4 p-8">
        envair
    </button>

</section>
