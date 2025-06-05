<section>
    <div class="grid grid-cols-4 bg-cyan-400 mb-2 gap-1 font-semibold text-yellow-50">
        <div class="text-center">Parametro</div>
        <div class="text-end">Resultado</div>
        <div class="text-start font-light">unidades</div>
        <div class="text-center">v referencia</div>
    </div>

    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase">Grupo sanguíneo</div>
        <div class="p-2">
            <select name="gs" class="rounded-lg uppercase" id="gs">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-4 text-lg gap-1">
        <div class="p-2 font-semibold uppercase">RH</div>
        <div class="p-2">
            <select name="rh" class="rounded-lg uppercase" id="rh">
                <option value="positivo">positivo</option>
                <option value="negativo">negativo</option>
            </select>
        </div>

    </div>

    <button type="submit"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mt-4 p-8">
        enviar
    </button>
</section>
