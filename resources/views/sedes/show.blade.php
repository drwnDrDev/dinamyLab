<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sede') }}
        </h2>
    </x-slot>
<x-canva>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">{{ $sede->nombre }}</h1>
                    <p><strong>Dirección:</strong> {{ $sede->direccion->direccion }} - {{ $sede->direccion->municipio->municipio }}</p>
                    <p><strong>Ciudad:</strong> {{ $sede->direccion->municipio->departamento }}</p>
                    <p><strong>Teléfono:</strong> {{ $sede->telefonos->pluck('numero')->implode(', ' ) }}</p>
                    <p><strong>Email:</strong> {{ $sede->emails->pluck('email')->implode(', ' ) }}</p>
                </div>
            </div>

        </div>
    </div>
    <section>
        <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Profesionales disponibles en esta sede') }}
                </h2>

                @foreach ($sede->empleados as $empleado)
                    <p>{{ $empleado->user->name }} - {{ $empleado->cargo }}</p>
                @endforeach
        </header>
    </section>
    <section>
        <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Exámenes disponibles en esta sede') }}
                </h2>
                <form class="grid grid-cols-2 justify-center">
                @foreach ($examenes as $examen)
                <div class="grid grid-cols-2 gap-2 px-4">
                    <label>{{ $examen->nombre }}</label>
                    <input type="number" name="precio_{{ $examen->id }}" placeholder="Precio" value="{{$examen->valor}}">
                </div>
                @endforeach
                </form>

        </header>
    </section>
</x-canva>
</x-app-layout>
