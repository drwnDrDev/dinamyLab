<x-app-layout >
    <x-slot name="title">
        {{ __('Medical Reports') }}
    </x-slot>
<x-slot name="header">
<div class="max-w-7xl mx-auto flex items-center justify-between py-4">

<div class="w-full md:w-1/2 grid sm:grid-cols-2 justify-center p-3 mx-auto bg-white rounded-lg border border-gray-200 shadow-md print:hidden">
    <form method="GET" action="{{ route('reportes') }}" class="col-span-2 flex flex-wrap gap-6 justify-center bg-slate-200 bg-opacity-80 p-8">
    <div>
        <x-input-label for="fecha_inicio" :value="__('Start Date')" />
        <x-date-input name="fecha_inicio" id="fecha_inicio" />
    </div>
    <div>
        <x-input-label for="fecha_fin" :value="__('End Date')" />
        <x-date-input name="fecha_fin" id="fecha_fin" />
    </div>
    <x-primary-button class="bg-cyan-600 h-10 self-end">{{__('Filter')}}</x-primary-button>
</form>
    <h2 class="font-semibold text-xl col-span-2 flex flex-col md:flex-row justify-center items-end gap-3 text-text leading-tight print:hidden">
 {{ $startDate }} - {{ $endDate }}
    </h2>
@foreach($procedimientos as $procedimiento)
    <div class="w-11/12 flex justify-between items-center border-b border-borders py-2">
        <div>
            <p class="text-lg font-bold leading-tight tracking-[-0.015em]">{{ $procedimiento->examen->nombre }}</p>
            <p class="text-titles">Total Procedimientos: {{ $procedimiento->total_procedimientos }}</p>
        </div>
    </div>
@endforeach

</div>
<section class="w-full md:w-2/5 mx-auto grid items-start bg-white rounded-lg border border-gray-200 shadow-md print:hidden">
<div class="bg-rips-cargador bg-cover bg-no-repeat bg-center bg-opacity-25 p-4 rounded-t-lg">
    <h2 class="font-semibold text-xl flex flex-col md:flex-row justify-center items-end gap-3 bg-slate-500 bg-opacity-80 rounded-t-xl text-white leading-tight print:hidden">
        RIPS
    </h2>


 <form action="{{route('descargar.rips')}}" class="p-8 flex flex-wrap gap-6 justify-center bg-slate-200 bg-opacity-80" method="POST">
    @csrf
    <div class="form-group w-full">
        <x-input-label for="sede_id" :value="__('Headquarter')" />
        <select name="sede_id" id="sede_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
            @foreach($sedes as $sede)
                <option value="{{$sede->id}}" @if($sede->id == session('sede')->id) selected @endif>{{$sede->nombre}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group w-5/12">
        <x-input-label for="fecha_inicio" :value="__('Start date')" />
        <x-date-input name="fecha_inicio" id="fecha_inicio"/>
    </div>
    <div class="form-group w-5/12">
        <x-input-label for="fecha_fin" :value="__('End date')" />
        <x-date-input name="fecha_fin" id="fecha_fin"/>
    </div>

    <x-primary-button class="bg-violet-600 h-10">{{__('Generate report')}}</x-primary-button>

</form>
</div>
</section>
</div>
</x-app-layout>
