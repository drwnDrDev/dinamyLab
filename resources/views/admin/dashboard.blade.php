<x-app-layout>

    <x-slot name="header">
        <nav>
            <x-primary-button class="bg-red-300" href="{{route('convenios.index')}}">Convenios</x-primary-button>
            <x-primary-button class="bg-green-300" href="{{route('examenes')}}">Examenes</x-primary-button>
            <x-primary-button class="bg-violet-300" href="{{route('facturas')}}">Facturar</x-primary-button>

        </nav>
    </x-slot>

        Procedimientos Pendientes:
        <div class="flex flex-col gap-1 overflow-hidden shadow-sm sm:rounded-lg">
            @foreach ($pendientes as $procedimiento)
            <div class="grid grid-cols-7">
                <p class="p-1 col-span-3 bg-slate-100 hover:bg-violet-300 rounded-md" >{{ $procedimiento->examen->nombre }} - {{ $procedimiento->fecha}}</p>
                <a class="bg-green-100 hover:bg-green-300 p-2" href="{{route('resultados.create',$procedimiento)}}">Ir a reultado</a>

                <details class="col-span-3">
                    <summary class="bg-blue-100 hover:bg-blue-300 p-2">Cambiar estado manualmente</summary>
                    <form action="{{route('procedimientos.observaciones',$procedimiento)}}" method="POST">
                    @method('patch')
                    @csrf
                        <div class="flex flex-col gap-2">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="border-2 border-gray-300 rounded-md">
                                <option value="pendiente de muestra">Pendiente por muestra</option>
                                <option value="anulado">Anulado</option>
                            </select>
                            <label for="observacion">Observación</label>
                            <textarea name="observacion" id="observacion" cols="30" rows="3" class="border-2 border-gray-300 rounded-md"></textarea>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Enviar</button>
                        </div>
                    </form>
                </details>

            </div>
            @endforeach
        </div>

</x-app-layout>
