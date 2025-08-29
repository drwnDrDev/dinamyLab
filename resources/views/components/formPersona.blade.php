@props([
'perfil' => 'Paciente', // Valores posibles: paciente, acompañante, pagador

])


<form id="crear{{$perfil}}" class="max-w-screen-md mx-auto" method="POST">


    @csrf


    <div class="w-full min-w-80 p-4 my-4">
      <div class="flex gap-4">
    <h2 class="font-bold mb-4 text-xl text-titles">Datos {{$perfil}} </h2>
    </div>
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
            <div class="w-full h-12 procedencia">
                <x-input-label for="pais">País de orígen</x-input-label>
                <x-select-input id="pais" name="pais"  :options="['170'=>'Colombia']" />
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

                </datalist>
            </div>
            <div>
                <x-input-label for="tipo_afiliacion">Tipo de afiliación</x-input-label>


                <x-select-input id="tipo_afiliacion" name="tipo_afiliacion" :options="[
                    '12' => 'Particular',
                    '01' => 'Contributivo cotizante',
                    '02' => 'Contributivo beneficiario',
                    '03' => 'Contributivo adicional',
                    '04' => 'Subsidiado',
                    '05' => 'No afiliado',
                    '06' => 'Especial o Excepción cotizante',
                    '07' => 'Especial o Excepción beneficiario',
                    '08' => 'Personas privadas de la libertad Fondo Nacional de Salud',
                    '09' => 'Tomador - Amparado ARL',
                    '10' => 'Tomador - Amparado SOAT',
                    '11' => 'Tomador - Amparado Planes voluntarios de salud',
                    '13' => 'Especial o Excepción no cotizante Ley 352 de 1997'
                ]" />


            </div>

        @else
            <div class="w-full pb-2">
                <x-input-label for="parentesco">Parentesco</x-input-label>
                <x-text-input type="text" id="parentesco" name="parentesco" />
            </div>
        @endif
        </div>
        <div class="row-inputs pt-2 w-full md:grid md:grid-cols-4 gap-2" x-data="{ openCiudad: true }">
            <div class="w-full md:col-span-4 pb-2 flex items-center">
                <div class="w-full pb-2 flex flex-wrap gap-2 items-center">
                    <p>¿Reside en Colombia?</p>
                    <x-input-label for="reside_colombia_actualmente" class="font-bold text-2xl">SÍ</x-input-label>
                    <input type="radio" id="reside_colombia_actualmente" name="reside_colombia" @change="openCiudad = $event.target.checked" checked>
                    <x-input-label for="visitante_extranjero" class="font-bold text-2xl">NO</x-input-label>
                    <input type="radio" id="visitante_extranjero" name="reside_colombia" @change="openCiudad = !$event.target.checked">
                </div>
            </div>

            <div class="w-full h-12 pb-2 relative " x-show="openCiudad">
                <x-input-label for="municipioBusqueda">Municipio</x-input-label>
                <x-text-input type="text" id="municipioBusqueda" name="municipioBusqueda" placeholder="Buscar municipio..." class="form-input w-full mb-2"/>
                <div class="municipio-busqueda absolute z-10 bg-white border border-borders w-full max-h-60 overflow-y-auto hidden">

                </div>
                <select id="municipio" name="municipio" class="text-sm h-9 w-full p-1 border-borders focus:border-primary focus:ring-primary rounded-md uppercase hidden">
                    <option value="11001">Bogotá</option>
                </select>

            </div>


            <div class="w-full h-12 pb-2 md:col-span-2" x-show="openCiudad">
                <x-input-label for="direccion">Dirección</x-input-label>
                <x-text-input type="text" id="direccion" name="direccion" class="form-input max-w-80 mb-2" />
            </div>
            <div class="w-full h-12 pb-2 pl-1" x-show="openCiudad">
                <x-input-label for="zona" class="w-full text-center ml-1 font-bold text-2xl">Zona</x-input-label>
                <div class="flex items-center justify-around gap-2 pt-2">
                    <div class="flex items-center gap-1">
                        <input type="radio" id="zona_urbana" name="zona" value="01" checked>
                        <label for="zona_urbana">Urbana</label>
                    </div>
                    <div class="flex items-center gap-1">
                        <input type="radio" id="zona_rural" name="zona" value="02">
                        <label for="zona_rural">Rural</label>
                    </div>
                </div>
            </div>
            <div class="w-full h-12" x-show="!openCiudad">
                <x-input-label for="pais_residencia">País de residencia </x-input-label>
                <x-select-input id="pais_residencia" name="pais_residencia"  :options="['null'=>'Elija un país']" />
            </div>



        </div>

        <div class="row-inputs py-8 w-full flex justify-center gap-2">
            <x-primary-button id="tipoGuardado" name="tipoGuardado" class="w-40" >Guardar</x-primary-button>
        </div>
    </div>
</form>
