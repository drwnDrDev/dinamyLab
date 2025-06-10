<section class="flex flex-col g-1">
    <div class="grid grid-cols-4 bg-primary gap-1 font-semibold text-white">
        <div class="text-center">Parametro</div>
        <div class="text-end ">Resultado</div>
        <div class="text-start font-light">Unidades</div>
        <div class="text-center">Valor de Ref.</div>
    </div>
    <div class="py-2 font-bold uppercase w-full"> examen fisico-quimico</div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> color</div>
        <div class="text-end">
            <select name="color" class="text-sm p-1 pr-6 rounded-md uppercase border border-borders" id="color">
                <option value="hidrico">hidrico</option>
                <option value="amarillo">amarillo</option>
                <option value="ambar">ambar</option>
                <option value="amarillo intenso">amarillo intenso</option>
                <option value="rojo">rojo</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> aspecto</div>
        <div class="text-end">
            <select name="aspecto" class="text-sm p-1 pr-6 rounded-md uppercase border border-borders" id="aspecto">
                <option value="lig turbio">lig turbio</option>
                <option value="turbio">turbio</option>
                <option value="limpido">limpido</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> densidad</div>
        <div class="text-end">
            <input step="0.001" type="number" class="text-sm p-1 pr-6 rounded-md uppercase border-borders" name="densidad"
                id="densidad">
        </div>
        <div class="text-sm pl-2">
            <span>g/dL</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> <span>PH</span> </div>
        <div class="text-end">
            <input step="0.001" type="number" class="text-sm p-1 pr-6 rounded-md uppercase border-borders" name="ph"
                id="ph">
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> Glucosa</div>
        <div class="text-end">
            <input type="text" name="glucosa" value="negativo" id="glucosa" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>mg/dL</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> cetonas</div>
        <div class="text-end">
            <input type="text" name="cetonas" value="negativo" id="cetonas" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>mg/dL</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> leucocito esterasa</div>
        <div class="text-end">
            <select name="leucocito" class="text-sm p-1 pr-6 rounded-md uppercase border border-borders" id="leucocito">
                <option value="positivo">positivo</option>
                <option value="negativo">negativo</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> proteinas</div>
        <div class="text-end">
            <input type="text" name="proteinas" value="negativo" id="proteinas" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>mg/dL</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> pigmentos biliares</div>
        <div class="text-end">
            <input type="text" name="pigmentos" value="negativo" id="pigmentos" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>mg/dL</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> hemoglobina</div>
        <div class="text-end">
            <input type="text" name="hemoglobina" value="negativo" id="hemoglobina" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> nitritos</div>
        <div class="text-end">
            <select name="nitritos" class="text-sm p-1 pr-6 rounded-md uppercase border border-borders" id="nitritos">
                <option value="positivo">positivo</option>
                <option value="negativo">negativo</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> urobilinogeno</div>
        <div class="text-end">
            <input type="text" name="urobilinogeno" value="normal" id="urobilinogeno" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>mg/dL</span>
        </div>
    </div>
    <div class="py-2 font-bold uppercase w-full"> examen microscopico</div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> cel epiteliales</div>
        <div class="text-end">
            <input type="text" name="epiteliales" id="epiteliales" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>x campo</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> leucocitos</div>
        <div class="text-end">
            <input type="text" name="leucocitos" id="leucocitos" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
           <span>x campo</span> 
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> hematies</div>
        <div class="text-end">
            <input type="text" name="hematies" id="hematies" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
        <div class="pl-2">
            <span>x campo</span>
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> bacterias</div>
        <div class="text-end">
            <input type="text" name="bacterias" id="bacterias" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="pl-2 uppercase"> moco</div>
        <div class="text-end">
            <input type="text" name="moco" id="moco" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>
    </div>
    <div class="grid grid-cols-4 text-sm items-center gap-1 py-1 border-b border-borders">
        <div class="py-2 font-semibold uppercase"> observaciones</div>
        <div class="text-end">
            <input type="text" name="observacion" id="observacion" class="text-sm p-1 pr-6 rounded-md uppercase border-borders">
        </div>

    </div>

    <div class="w-full flex justify-end mt-4">
        
        <x-primary-button>Guardar</x-primary-button>
    </div>

</section>
