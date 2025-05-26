<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>
    <x-canva class="max-w-5xl">

        <form action="{{route('personas.store')}}" method="post">

            <div class="w-full min-w-80 bg-white p-4 border border-gray-300 rounded-sm shadow-md my-4">
                <h2 class="font-bold mb-4 text-xl text-secondary">Datos personales</h2>
                <div class="row-inputs w-full md:grid lg:grid-cols-4 md:grid-cols-2 gap-2">

                    @csrf

                    <div class="w-full pb-2 lg:max-w-60">
                        <x-input-label for="numero_documento">Número de documento</x-input-label>
                        <x-text-input type="number" id="numero_documento" name="numero_documento" required />
                    </div>
                    <div class="w-full pb-2 lg:max-w-60">
                        <x-input-label for="tipo_documento">Tipo de documento</x-input-label>
                        <x-select-input :options="$tipos_documento" name="tipo_documento" id="tipo_documento" class="form-select" required>
                        </x-select-input>
                    </div>

                    <div class="w-full pb-2 col-span-2">
                        <x-input-label for="pais">Nacionalidad</x-input-label>
                        <x-text-input list="paises" id="pais" name="pais" class="overflow-hidden" required />
                    </div>
                    <datalist id="paises">
                        <option value="100101">Bogotá</option>
                        @foreach($paises as $pais)
                        <option value="{{ $pais->codigo_iso }}">{{$pais->nombre}}</option>
                        @endforeach
                    </datalist>
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
                <div class="row-inputs pt-2 w-full md:grid md:grid-cols-2 justify-center gap-2">
                    <div class="w-full pb-2 flex gap-2 items-center">
                        <x-input-label for="fecha_nacimiento">Fecha de Nacimiento</x-input-label>
                        <x-date-input type="date" placeholder="fecha_nacimiento" id="fecha_nacimiento" name="fecha_nacimiento" required />
                    </div>
                    <div class="w-full pb-2 flex gap-2 items-center">
                        <span>Sexo</span>
                        <x-input-label for="sexo" class="font-bold text-2xl">F</x-input-label><input type="radio" id="sexo" name="sexo" value="F">
                        <x-input-label for="sexo" class="font-bold text-2xl">M</x-input-label><input type="radio" id="sexo" name="sexo" value="M">
                    </div>
                </div>

                <hr class="my-8 border-1 border-gray-200">

                <!-- informacion de Contacto -->

                <h2 class="font-bold mb-4 text-xl text-secondary">Datos de contacto</h2>

                <div class="row-inputs pt-2 w-full md:flex justify-between gap-2">
                    <div class="w-full pb-2">
                        <x-input-label for="telefono">Teléfono</x-input-label>
                        <x-text-input type="number" id="telefono" name="telefono" required />
                    </div>
                    <div class="w-full pb-2">
                        <x-input-label for="correo">Correo</x-input-label>
                        <x-text-input type="email" placeholder="example@mail.com" id="correo" name="correo" required />
                    </div>
                    <div class="w-full pb-2">
                        <x-input-label for="EPS">EPS</x-input-label>
                        <x-text-input type="text" id="EPS" name="EPS" required />
                    </div>
                </div>
                <div class="row-inputs pt-2 w-full md:grid md:grid-cols-3 gap-2">

                    <div class="w-full pb-2">
                        <x-input-label for="ciudad">Ciudad</x-input-label>
                        <x-text-input list="ciudades" id="ciudad" name="ciudad" class="w-96" required />
                        <datalist id="ciudades">
                            <option value="100101">Bogotá</option>
                            @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->codigo }}">{{$ciudad->departamento}}, {{$ciudad->municipio}}</option>
                            @endforeach
                        </datalist>
                    </div>
                    <!-- La EPS va a un campo de la tabla contacto -->

                    <div class="w-full pb-2 md:col-span-2">
                        <x-input-label for="direccion">Dirección</x-input-label>
                        <x-text-input type="text" id="direccion" name="direccion" required />
                    </div>

                </div>
                <div class="row-inputs py-8 w-full flex justify-center gap-2">
                    <x-primary-button>Guardar</x-primary-button>
                    <x-secondary-button>Cancelar</x-secondary-button>
                </div>
            </div>

        </form>
    </x-canva>

</x-app-layout>