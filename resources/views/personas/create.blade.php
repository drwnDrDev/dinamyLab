<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pacientes
        </h2>
    </x-slot>
    <x-canva>
        <h2>Crea un nuevo registro de paciente o persona</h2>
        <form action="{{route('personas.store')}}" method="post">

            <div class="w-full min-w-80 bg-secondaryContrast p-4 rounded-sm">
                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    @csrf
                    
                    <select name="tipo_documento" id="tipo_documento" class="form-select">
                        @foreach ($tipos_documento as $valor => $nombre)
                        <option value="{{ $valor }}">{{ $nombre }}</option>
                        @endforeach
                    </select>

                    <x-text-input type="number" placeholder="numero_documento" id="numero_documento" name="numero_documento" class="max-w-40" />

                    
                    <x-text-input list="paises" placeholder="Nacionalidad" id="pais" name="pais" class="overflow-hidden" />

                    <datalist id="paises">
                        <option value="100101">Bogotá</option>
                        @foreach($paises as $pais)
                        <option value="{{ $pais->codigo_iso }}">{{$pais->nombre}}</option>
                        @endforeach
                    </datalist>          

                </div>
                <hr class="my-2 border-1 border-secondary"> 

                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-text-input type="text" placeholder="nombres" id="nombres" name="nombres" required/>
                    <x-text-input type="text" placeholder="apellidos" id="apellidos" name="apellidos" required/>
                                        
                </div>
                <div class="row-inputs pt-2 w-full flex justify-between gap-2">
                    <x-date-input type="date" placeholder="fecha_nacimiento" id="fecha_nacimiento" name="fecha_nacimiento" />

                    <x-input-label>Sexo</x-input-label>
                    <x-input-label for="sexo">F</x-input-label><input type="radio" id="sexo" name="sexo" value="F">
                    <x-input-label for="sexo">M</x-input-label><input type="radio" id="sexo" name="sexo" value="M">
                </div>
                
                <br>
                <hr class="border-1 border-secondary">

                <br>
            </div>



            <br><br>
            <hr>
            <br><br>
            <input type="number" placeholder="telefono" id="telefono" name="telefono"> <br>
            <input type="text" placeholder="direccion" id="direccion" name="direccion"> <br>
            <input type="mail" placeholder="correo" id="correo" name="correo"> <br>

            <input list="ciudades" placeholder="ciudad" id="ciudad" name="ciudad" class="w-96">

            <datalist id="ciudades">

                <option value="100101">Bogotá</option>
                @foreach($ciudades as $ciudad)
                <option value="{{ $ciudad->codigo }}">{{$ciudad->departamento}}, {{$ciudad->municipio}}</option>
                @endforeach
            </datalist>



            <!-- La EPS va a un campo de la tabla contacto -->
            <input type="text" placeholder="EPS" id="EPS" name="EPS"> <br><br>

            <x-primary-button>Enviar</x-primary-button>
            <button class="bg-primary">primary</button>
            <button class="bg-secondary">secondary</button>
            <button class="bg-accent">accent</button>
            <button class="bg-primaryDarkContrast">primaryDark</button>

            <x-input-label>Nombres</x-input-label>
            <x-text-input></x-text-input>




        </form>
    </x-canva>




</x-app-layout>