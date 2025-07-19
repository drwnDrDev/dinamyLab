@props([
'perfil' => 'Paciente', // Valores posibles: paciente, acompañante, pagador
'accion' => null,
])


<form id="crear{{$perfil}}"
class="max-w-screen-md mx-auto"

            action="{{ $accion }}"

method="POST">

@if ($accion != null)
    @csrf
@endif

    <div class="w-full min-w-80 p-4 my-4">
        <h2 class="font-bold mb-4 text-xl text-titles">Datos {{$perfil}}</h2>
        <input type="hidden" id="perfil" name="perfil" value="{{ $perfil }}">
        <div class="row-inputs w-full md:grid  md:grid-cols-3 justify-around gap-2">
            <div>
                <x-input-label for="numero_documento">Número de documento</x-input-label>
                <x-text-input type="number" id="numero_documento" name="numero_documento" required />
            </div>

            <div class="w-full">
            <x-input-label for="tipo_documento">Tipo de documento</x-input-label>
                <x-select-input id="tipo_documento" name="tipo_documento"  :options="['CC'=>'Cédula de Ciudadanía']" />
            </div>
            <div class="w-full ">
                <x-input-label for="pais">País de orígen</x-input-label>
                <x-select-input id="pais" name="pais"  :options="['COL'=>'Colombia']" />
            </div>
        </div>

        <div class="row-inputs pt-2 w-full md:flex justify-between gap-4">
            <div class="w-full flex items-baseline justify-around gap-1">
                <x-input-label for="nombres">Nombres</x-input-label>
                <x-text-input type="text" id="nombres" name="nombres" class="col-span-2" required />
            </div>
            <div class="w-full flex items-baseline justify-around gap-1">
                <x-input-label for="apellidos">Apellidos</x-input-label>
                <x-text-input  type="text" id="apellidos" name="apellidos" class="col-span-2" required />
            </div>
        </div>

        <!-- Información opcional (Solo para 'paciente') -->
        @if($perfil === 'Paciente')

        <div class="row-inputs pt-2 w-full md:grid md:grid-cols-2 gap-2">
            <div class="w-full pb-2 flex gap-2 items-center">
                <x-input-label for="fecha_nacimiento">Fecha de Nacimiento</x-input-label>
                <x-date-input type="date" id="fecha_nacimiento" name="fecha_nacimiento" />
            </div>
            <div class="w-full pb-2 flex gap-2 items-center">
                <span>Sexo</span>
                <x-input-label for="sexo_femenino" class="font-bold text-2xl">F</x-input-label>
                <input type="radio" id="sexo_femenino" name="sexo" value="F">
                <x-input-label for="sexo_masculino" class="font-bold text-2xl">M</x-input-label>
                <input type="radio" id="sexo_masculino" name="sexo" value="M">
            </div>
        </div>
     @endif
        <hr class="m-0 border-1 border-borders">
        <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
            <div class="w-full pb-2">
                <x-input-label for="telefono">Teléfono</x-input-label>
                <x-text-input type="number" id="telefono" name="telefono" />
            </div>
            <div class="w-full pb-2">
                <x-input-label for="correo">Correo</x-input-label>
                <x-text-input type="email" id="correo" name="correo" placeholder="example@mail.com" />
            </div>

     @if($perfil === 'Paciente')
            <div class="w-full pb-2">
                <x-input-label for="eps">EPS</x-input-label>
                <x-text-input list="lista_eps" id="eps" name="eps" />
                <datalist id="lista_eps">
                    <option value="Salud Total"></option>
                </datalist>
            </div>
        @else
            <div class="w-full pb-2">
                <x-input-label for="parentesco">Parentesco</x-input-label>
                <x-text-input type="text" id="parentesco" name="parentesco" />
            </div>
    @endif
        </div>
        <div class="row-inputs pt-2 w-full md:grid md:grid-cols-3 gap-2">

            <div class="w-full pb-2 relative">
                <x-input-label for="municipioBusqueda">Municipio</x-input-label>
                <x-text-input type="text" id="municipioBusqueda" name="municipioBusqueda" placeholder="Buscar municipio..." class="form-input w-full mb-2"/>
                <div class="municipio-busqueda absolute z-10 bg-white border border-borders w-full max-h-60 overflow-y-auto hidden">
                    <!-- Resultados de búsqueda de municipios -->
                </div>
            </div>
            <div class="w-full pb-2">
                <x-input-label for="municipio">Ciudad</x-input-label>
                <x-select-input id="municipio" name="municipio"  :options="['11007'=>'Bosa']" />
            </div>

            <div class="w-full pb-2 md:col-span-2">
                <x-input-label for="direccion">Dirección</x-input-label>
                <x-text-input type="text" id="direccion" name="direccion" />
            </div>

        </div>

        <div class="row-inputs py-8 w-full flex justify-center gap-2">
            <x-primary-button id="tipoGuardado" name="tipoGuardado" class="w-40" >Guardar</x-primary-button>

        </div>
    </div>
</form>
