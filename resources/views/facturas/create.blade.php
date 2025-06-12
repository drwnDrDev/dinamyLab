<x-app-layout >
    <x-slot name="title">
       Nueva Factura
    </x-slot>

        <div class="section_paciente">
            <x-formPersona perfil="Pagador" :tipos_documento="$tipos_documento" />
        </div>

        <div x-data="{ open: false }">
            <label for="mostrarConvenio">Convenio</label>
            <input type="checkbox" id="mostrarAcompaniante" @change="open = $event.target.checked">
            <div x-show="open" x-transition>

                    <select name="convenio" id="razon_social">
                        @foreach ($convenios as $convenio)
                        <option value="{{$convenios->id}}">{{$convenios->razon_social}}</option>
                        @endforeach
                    </select>


            </div>
        </div>



</x-app-layout>
