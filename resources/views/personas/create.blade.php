<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>
    <x-canva class="max-w-5xl">
        <h2>Crea un nuevo registro de paciente o persona</h2>
        <form action="{{route('personas.store')}}" method="post">

            <div class="w-full min-w-80 bg-white p-4 border border-gray-300 rounded-sm shadow-md my-4">
                <div class="row-inputs pt-2 w-full md:grid md:grid-cols-4 sm:grid-cols-2 gap-2">
                    @csrf

                    <div class="w-full max-w-60">
                        <x-input-label for="tipo_documento">Tipo de documento</x-input-label>
                        <x-select-input :options="$tipos_documento" name="tipo_documento" id="tipo_documento" class="form-select" required>
                        </x-select-input>
                    </div>
                    <div class="w-full max-w-60">
                        <x-input-label for="numero_documento">Número de documento</x-input-label>
                        <x-text-input type="number" placeholder="numero_documento" id="numero_documento" name="numero_documento" required />
                    </div>

                    <div class="w-full col-span-2">
                        <x-input-label for="pais">Nacionalidad</x-input-label>
                        <x-text-input list="paises" placeholder="Nacionalidad" id="pais" name="pais" class="overflow-hidden" required />
                    </div>
                    <datalist id="paises">
                        <option value="100101">Bogotá</option>
                        @foreach($paises as $pais)
                        <option value="{{ $pais->codigo_iso }}">{{$pais->nombre}}</option>
                        @endforeach
                    </datalist>
                </div>
                <hr class="my-2 border-1 border-gray-200">

                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-text-input type="text" placeholder="nombres" id="nombres" name="nombres" required />
                    <x-text-input type="text" placeholder="apellidos" id="apellidos" name="apellidos" required />

                </div>
                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-date-input type="date" placeholder="fecha_nacimiento" id="fecha_nacimiento" name="fecha_nacimiento" required />

                    <x-input-label>Sexo</x-input-label>
                    <x-input-label for="sexo">F</x-input-label><input type="radio" id="sexo" name="sexo" value="F">
                    <x-input-label for="sexo">M</x-input-label><input type="radio" id="sexo" name="sexo" value="M">
                </div>


                <!-- informacion de Contacto -->


                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-text-input type="number" placeholder="telefono" id="telefono" name="telefono" class="max-w-60" required />
                    <x-text-input type="text" placeholder="direccion" id="direccion" name="direccion" required />

                </div>
                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-text-input type="email" placeholder="example@mail.com" id="correo" name="correo" required />
                    <x-text-input list="ciudades" placeholder="ciudad" id="ciudad" name="ciudad" class="w-96" required />
                    <datalist id="ciudades">
                        <option value="100101">Bogotá</option>
                        @foreach($ciudades as $ciudad)
                        <option value="{{ $ciudad->codigo }}">{{$ciudad->departamento}}, {{$ciudad->municipio}}</option>
                        @endforeach
                    </datalist>

                    <!-- La EPS va a un campo de la tabla contacto -->
                    <x-text-input type="text" placeholder="EPS" id="EPS" name="EPS" required />
                </div>
                <div class="row-inputs py-8 w-full flex justify-center gap-2">
                    <x-primary-button>Guardar</x-primary-button>
                    <x-secondary-button>Cancelar</x-secondary-button>
                </div>
            </div>

        </form>
    </x-canva>

</x-app-layout>