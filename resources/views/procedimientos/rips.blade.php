<x-app-layout >
    <x-slot name="title">
        {{ __('Medical Reports') }}
    </x-slot>
<x-slot name="header">

    <h2 class="font-semibold text-xl text-center text-text leading-tight print:hidden">
     Este mes {{now()->startOfMonth()->translatedFormat('F Y')}} - Reporte de Procedimientos Realizados
    </h2>
<div class="p-5 w-11/12 mx-auto bg-white rounded-lg border border-gray-200 shadow-md print:hidden">

@foreach($procedimientos as $procedimiento)
    <div class="w-full flex justify-between items-center border-b border-borders py-2">
        <div>
            <p class="text-lg font-bold leading-tight tracking-[-0.015em]">{{ $procedimiento->examen->nombre }}</p>
            <p class="text-titles">Total Procedimientos: {{ $procedimiento->total_procedimientos }}</p>
        </div>
    </div>
@endforeach

</div>

</x-app-layout>
