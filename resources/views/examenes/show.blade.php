<x-app-layout>

<x-slot name="header">
<div class="max-screen mx-auto">
  <div class="flex items-center justify-center gap-1 mb-0">
    <h2 class="text-xl font-semibold">{{ $examen->nombre }}</h2> 
    <h3 class="text-lg font-light text-gray-700 mt-0">({{ $examen->nombre_alternativo }})</h3>
</div> 
<div class="flex items-center justify-center gap-2 ">
    <h3 class="text-lg font-light text-gray-700">CUP-{{ $examen->cup }}</h3>
    <h3  class="text-lg font-light text-green-700">${{ $examen->valor }} COP</h3>
</div> 
    <p class="max-w-11/12 p-4">{{ ucfirst($examen->descripcion) }}</p>

</div>
</x-slot>
<x-canva>
    

<section>
    @foreach($examen->parametros as $parametro)
    <div class="max-screen mx-auto my-4 p-4 bg-white rounded-lg shadow">
        <h3 class="text-lg font-semibold">{{ $parametro->nombre }}</h3>
       @if($parametro->grupo && $parametro->grupo !== '')
                <p class="text-gray-600">Grupo: {{ $parametro->grupo }}</p>

       @endif
    @if($examen->parametros->count() > 2)
        <p class="text-gray-600">Posición: {{ $parametro->posicion }}</p>
       
    @endif
    @if ($parametro->metodo && $parametro->metodo !== '')
         <p class="text-gray-600">Método: {{ $parametro->metodo }}</p>
    @endif
        
        @if($parametro->valoresReferencia && $parametro->valoresReferencia->count() > 0)
            <h4 class="mt-2 font-semibold">Valores de referencia</h4>
            <table class="min-w-full border border-gray-400">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-text font-semibold border border-gray-400">Demografia</th>
                        <th class="px-4 py-2 text-text font-semibold border border-gray-400">Salida</th>
                        <th class="px-4 py-2 text-text font-semibold border border-gray-400">Min</th>
                        <th class="px-4 py-2 text-text font-semibold border border-gray-400">Max</th>
                        <th class="px-4 py-2 text-text font-semibold border border-gray-400">Optimo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parametro->valoresReferencia as $referencia)
                        <tr>
                            <td class="p-2 bg-slate-200 uppercase border border-gray-400"> {{ $referencia->demografia }} </td>
                            <td class="p-2 bg-slate-200 border border-gray-400"> {{ $referencia->salida }} </td>
                            <td class="p-2 bg-slate-200 border border-gray-400"> {{ $referencia->min }} </td>
                            <td class="p-2 bg-slate-200 border border-gray-400"> {{ $referencia->max }} </td>
                            <td class="p-2 bg-slate-200 border border-gray-400"> {{ $referencia->optimo }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        @endif
         @if($parametro->opciones && $parametro->opciones->count() > 0)
            <h4 class="mt-2 font-semibold">Opciones:</h4>
            <ul class="list-disc pl-5">
                @foreach($parametro->opciones as $opcion)
                    <li>{{ $opcion->valor }} </li>
                @endforeach
            </ul>
        @endif

    </div>
    @endforeach
        
</section>

</x-canva>
</x-app-layout>