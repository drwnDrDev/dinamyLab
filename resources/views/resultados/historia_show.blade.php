<x-app-layout>
    
    <div class="py-4 flex justify-between items-center border-b border-borders">
        <div>
            <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $persona->nombreCompleto() }}</p>
            <p class="text-titles">Paciente ID: {{ $persona->numero_documento }}</p>
        </div>
    </div>
    <h3>Aqui va la VISTA de los procedimiento a imprimir</h1>

</x-app-layout>