
<x-app-layout>

    <x-slot name="header" titulo="Persona"> 
        <div class="grid grid-cols-2 gap-1">
        <section>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $persona->nombreCompleto() }}
        </h2>
        <h3>
            {{ $persona->tipo_documento }} {{ $persona->numero_documento }}
        </h3>
        </section>
        <section class="flex flex-col justify-end">
         <h2 class="w-full text-2xl font-semibold p-2">Contacto:</h2>
            <p> Email: <a href="mailto:{{ $persona->email }}" class="text-blue-500 hover:underline">{{ $persona->contacto->email }}</a></p> 
            <p> Telefono: <a href="tel:{{ $persona->telefono }}" class="text-blue-500 hover:underline">{{ $persona->contacto->telefono }}</a></p> 
        </section>
        </div>
    </x-slot>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="flex flex-col gap-4">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Información Personal</h3>
            <p><strong>Nombre:</strong> {{ $persona->nombreCompleto() }}</p>
            <p><strong>Tipo de Documento:</strong> {{ $persona->tipo_documento }}</p>
            <p><strong>Número de Documento:</strong> {{ $persona->numero_documento }}</p>
            <p><strong>Fecha de Nacimiento:</strong> {{ $persona->fecha_nacimiento->format('d/m/Y') }}</p>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Historial Clínico</h3>
            @if($persona->historialClinico)
                <ul class="list-disc pl-5">
                    @foreach($persona->historialClinico as $historia)
                        <li>{{ $historia->descripcion }} - {{ $historia->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
            @else
                <p>No hay historial clínico disponible.</p>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Resultados de Exámenes</h3>
            @if($persona->resultadosExamenes)
                <ul class="list-disc pl-5">
                    @foreach($persona->resultadosExamenes as $resultado)
                        <li>{{ $resultado->examen->nombre }} - Resultado: {{ $resultado->resultado }} - Fecha: {{ $resultado->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
            @else
                <p>No hay resultados de exámenes disponibles.</p>
            @endif
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Procedimientos Pendientes</h3>
            @if($persona->procedimientosPendientes)
                <ul class="list-disc pl-5">
                    @foreach($persona->procedimientosPendientes as $procedimiento)
                        <li>{{ $procedimiento->examen->nombre }} - Fecha: {{ $procedimiento->fecha->format('d/m/Y') }}</li>
                    @endforeach
                </ul>
            @else
                <p>No hay procedimientos pendientes.</p>
            @endif
        </div>

</x-app-layout>
