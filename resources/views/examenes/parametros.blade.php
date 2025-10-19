<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('examens') }}
    </h2>
</x-slot>
<x-canva>

    <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Exámenes</h2>
        <div class="flex flex-wrap w-full gap-2">
            @foreach($examenes as $examen)
                <div class="w-1/5 border border-borders rounded-lg p-4  shadow
                @if ($examen->sexo_aplicable == 'M')
                 bg-blue-100
                @elseif($examen->sexo_aplicable == 'F')
                    bg-pink-100

                @else
                    bg-green-100

                @endif">
                    <h3 class="text-lg font-semibold mb-2">{{ $examen->nombre }}</h3>
                    <p class="text-light text-sm text-gray-800">{{ $examen->nombre_alternativo }}</p>
                    <p class="text-gray-600 mb-2">CUP: {{ $examen->cup }}</p>
                    <p class="text-green-700 font-semibold mb-4">{{ $examen->sexo_aplicable }}</p>
                </div>
                <div class="w-3/4" >
            @foreach ($examen->parametros as $parametro)
                <div class="grid grid-cols-4 border border-borders rounded-lg p-1 shadow">
                    <h4 class="text-md font-semibold mb-2"><span class="text-gray-600 pr-2">{{ $parametro->posicion  }}</span>{{ $parametro->nombre }}</h4>
                    <p class="text-gray-600 mb-2">{{ $parametro->grupo }}</p>
                    <p class="text-gray-600 mb-2">{{$parametro->tipo_dato}}</p>
                    @if ($parametro->metodo && $parametro->metodo !== '')
                        <p class="text-gray-600 mb-2">Método: {{ $parametro->metodo }}</p>
                    @endif
                </div>
            @endforeach
                </div>
            @endforeach
        </div>
</x-canva>
</x-app-layout>
