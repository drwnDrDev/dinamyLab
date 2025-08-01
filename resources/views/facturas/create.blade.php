<x-app-layout>
    <x-slot name="title">
        Nueva Factura
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $persona->nombreCompleto() }} - Nueva Factura
        </h2>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="list-disc pl-5 text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif


    <div x-data="{ open: false }">
        <label for="mostrarConvenio">La persona que efectuara el pago es diferente del paciente</label>
        <input type="checkbox" id="mostrarPagador" @change="open = $event.target.checked">
        <div class="section_paciente" x-show="open" x-transition>
            <x-formPersona perfil="Pagador" />
        </div>
    </div>

    <div x-data="{ open: false }">
        <label for="mostrarConvenio">El pago está a cargo de uno de nuestros aliados</label>
        <input type="checkbox" id="mostrarConvenio" @change="open = $event.target.checked">
        <div x-show="open" x-transition>

            <select name="convenio" id="razon_social">
                @foreach ($convenios as $convenio)
                    <option value="{{ $convenio->id }}">{{ $convenio->razon_social }}</option>
                @endforeach
            </select>


        </div>
    </div>

<form action="{{ route('facturas.store') }}" method="POST">
@csrf

<input type="hidden" name="paciente_id" value="{{ $persona->id }}">
<input type="hidden" name="pagador_type" value="persona">
<input type="hidden" name="pagador_id" value="{{ $persona->id }}">

<h2>Detalles de la Factura </h2>
<label for=""></label><section class="flex flex-wrap gap-1">
<label for="numero_factura">Número de Factura</label><input type="text" name="numero_factura" id="numero_factura" placeholder="Número de Factura" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
<label for="fecha_emision">Fecha de Emisión</label><input type="text" name="fecha_emision" id="fecha_emision" placeholder="Fecha de Emisión" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
<label for="fecha_vencimiento">Fecha de Vencimiento</label><input type="text" name="fecha_vencimiento" id="fecha_vencimiento" placeholder="Fecha de Vencimiento" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
<label for="tipo_pago">Tipo de Pago</label>
<select name="tipo_pago" id="tipo_pago" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
    <option value="Efectivo">Efectivo</option>
    <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
    <option value="Cheque">Cheque</option>
</select>
<label for="subtotal">Subtotal</label><input type="number" name="subtotal" id="subtotal" placeholder="Subtotal" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
<label for="total">Total</label><input type="number" name="total" id="total" placeholder="Total" class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4">
</section>

    @foreach($ordenes as $orden)
        <div class="section_orden bg-slate-100 p-2 rounded-lg">
            <h3>Orden: {{ $orden->id }}</h3>
            @foreach ($orden->procedimientos as $procedimiento)
                <div class="flex section_procedimiento">
                 <p class="text-xs">
                 <input type="checkbox" name="procedimientos[{{$procedimiento->id}}]" id="{{$procedimiento->examen->nombre}}" checked>   <label for="{{$procedimiento->examen->nombre}}">{{ $procedimiento->examen->nombre }}</label>
                </p>
                <p class="text-xs">{{ $procedimiento->examen->valor }}</p>
                <p class="text-xs">{{ $procedimiento->fecha }}</p>
                </div>

        @endforeach
    </div>
@endforeach
</section>

        <x-primary-button type="submit" class="mt-4">
            {{ __('Crear Factura') }}
        </x-primary-button>
    </form>

</x-app-layout>
