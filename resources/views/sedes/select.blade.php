<x-guest-layout>
    <h1 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 pl-0">
            Hola {{ $empleado->user->name}}
            <span class="text-titles font-normal text-lg">({{ $empleado->cargo }})</span>
        </h1>

            <p class="text-lg mb-4">Para continuar, por favor selecciona una sede:</p>

            <section>
                @foreach ($empleado->sedes as $sede)
                <a href="{{route('elegir.sede', $sede->id)}}">
                    <div class="mb-4 flex items-center gap-4 p-4 border border-borders cursor-pointer rounded-lg hover:shadow-lg hover:shadow-borders transition-all duration-300 ease-in-out hover:border-primary">
                        <img class="w-12 h-12 rounded-full" src="{{ $sede->logo ? asset('storage/logos/' . $sede->logo) : asset('images/sede_default.png') }}" alt="Logo de la sede">
                        <h2 class="text-xl font-semibold">{{$sede->nombre}}</h2>
                    </div>
                </a>
                @endforeach
            </section>
            @vite('resources/js/obtenerStaticos.js')
    </x-guest-layout>



