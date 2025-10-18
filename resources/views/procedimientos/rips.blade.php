<x-app-layout >
    <x-slot name="title">
        {{ __('Medical Reports') }}
    </x-slot>
<x-slot name="header">
<div class="container flex items-center justify-between py-4">

<div class="p-5 w-2/5 mx-auto bg-white rounded-lg border border-gray-200 shadow-md print:hidden">

    <h2 class="font-semibold text-xl flex flex-col md:flex-row justify-center items-end gap-3 text-text leading-tight print:hidden">
     Este mes <span class="text-gray-500 text-sm">{{now()->startOfMonth()->translatedFormat('F Y')}} </span>
    </h2>
@foreach($procedimientos as $procedimiento)
    <div class="w-full flex justify-between items-center border-b border-borders py-2">
        <div>
            <p class="text-lg font-bold leading-tight tracking-[-0.015em]">{{ $procedimiento->examen->nombre }}</p>
            <p class="text-titles">Total Procedimientos: {{ $procedimiento->total_procedimientos }}</p>
        </div>
    </div>
@endforeach

</div>
<section class=" w-2/5 mx-auto grid items-start bg-white rounded-lg border border-gray-200 shadow-md print:hidden">
    <img src="./rips-cargador.png" alt="rips" class="w-32 mx-auto mt-8 mb-4">

 <form action="{{route('descargar.rips')}}" class="p-8 flex flex-wrap gap-6 justify-center" method="POST">
    @csrf
    <div class="form-group">
        <x-input-label for="fecha_inicio" :value="__('Start date')" />
        <x-date-input name="fecha_inicio" id="fecha_inicio"/>
    </div>
    <div class="">
        <x-input-label for="fecha_fin" :value="__('End date')" />
        <x-date-input name="fecha_fin" id="fecha_fin"/>
    </div>

    <x-primary-button class="bg-violet-600 h-10">{{__('Generate report')}}</x-primary-button>

</form>
</section>
</div>
</x-app-layout>
