<x-app-layout>
    <x-slot name="title">
        Nueva Factura
    </x-slot>
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



</x-app-layout>
