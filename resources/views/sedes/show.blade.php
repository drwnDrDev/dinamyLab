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
                    {{ __('Exámenes disponibles en esta sede') }}
                </h2>
                
        </header>
    </section>
</x-canva>
</x-app-layout>
