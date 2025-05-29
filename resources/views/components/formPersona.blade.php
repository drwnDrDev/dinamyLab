@props([
'perfil' => 'Paciente', // Valores posibles: paciente, acompañante, pagador
'eps' => [], // Lista de EPS
'ciudades' => [], // Lista de ciudades
'tipos_documento' => [] // Lista de tipos de documento
])


<form  id="crear{{$perfil}}">
    <div class="w-full min-w-80 bg-white p-4 border border-gray-300 rounded-sm shadow-md my-4">
        <h2 class="font-bold mb-4 text-xl text-secondary">Datos {{$perfil}}</h2>
        <input type="hidden" id="perfil" name="perfil" value="{{ $perfil }}">
        <div class="row-inputs w-full md:grid lg:grid-cols-4 md:grid-cols-2 gap-2">
            <div class="w-full pb-2 lg:max-w-60">
                <x-input-label for="numero_documento">Número de documento</x-input-label>
                <x-text-input type="number" id="numero_documento" name="numero_documento" required />
            </div>
            <div class="w-full pb-2 lg:max-w-60">
                <x-input-label for="tipo_documento">Tipo de documento</x-input-label>
                <x-select-input :options="$tipos_documento" name="tipo_documento" id="tipo_documento" class="form-select" required />
            </div>
            <div class="w-full pb-2 col-span-2">
                <x-input-label for="pais">Nacionalidad</x-input-label>
                <x-select-input id="pais" name="pais" class="hidden" :options="['COL'=>'Colombia']" />
            </div>
        </div>

        <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
            <div class="w-full pb-2">
                <x-input-label for="nombres">Nombres</x-input-label>
                <x-text-input type="text" id="nombres" name="nombres" required />
            </div>
            <div class="w-full pb-2">
                <x-input-label for="apellidos">Apellidos</x-input-label>
                <x-text-input type="text" id="apellidos" name="apellidos" required />
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

        <hr class="my-8 border-1 border-gray-200">
        <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
            <div class="w-full pb-2">
                <x-input-label for="telefono">Teléfono</x-input-label>
                <x-text-input type="number" id="telefono" name="telefono" />
            </div>
            <div class="w-full pb-2">
                <x-input-label for="correo">Correo</x-input-label>
                <x-text-input type="email" id="correo" name="correo" placeholder="example@mail.com" />
            </div>
            <div class="w-full pb-2">
                <x-input-label for="EPS">EPS</x-input-label>
                <x-text-input list="lista_eps" id="eps" name="eps" />
                <datalist id="lista_eps">
                    @foreach ($eps as $prestador)
                    <option value="{{ $prestador->nombre }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>
        <div class="row-inputs pt-2 w-full md:grid md:grid-cols-3 gap-2">

            <div class="w-full pb-2 relative">
                <x-input-label for="ciudad">Ciudad</x-input-label>
                <x-text-input id="municipioBusqueda" />
                <div class="w-full absolute flex-col-reverse bottom-10 bg-white border border-primary max-h-60 overflow-y-auto
                        z-10 hidden rounded-md shadow-lg" id="opciones"></div>
                <select name="municipio" id="municipio" class="hidden">
                    @foreach($ciudades as $ciudad)
                    <option value="{{ $ciudad->codigo }}">{{$ciudad->municipio}} - {{$ciudad->departamento}}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full pb-2 md:col-span-2">
                <x-input-label for="direccion">Dirección</x-input-label>
                <x-text-input type="text" id="direccion" name="direccion" :required="false" />
            </div>

        </div>

     @endif

        <div class="row-inputs py-8 w-full flex justify-center gap-2">
            <x-primary-button id="tipoGuardado" >Guardar</x-primary-button>
            <x-secondary-button>Cancelar</x-secondary-button>
        </div>
    </div>
</form>
