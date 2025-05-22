<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Persona
        </h2>
    </x-slot>
    <form action="{{route('personas.store')}}" method="post">
        @csrf
        <label for="tipo_documento">Tipo de Documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-select">
            @foreach ($tipos_documento as $valor => $nombre)
            <option value="{{ $valor }}">{{ $nombre }}</option>
            @endforeach
        </select>

         <input list="paises" placeholder="pais" id="pais" name="pais" class="w-96">

            <datalist id="paises">
                       
            <option value="100101">Bogotá</option>
               @foreach($paises as $pais)
               <option value="{{ $pais->codigo_iso }}">{{$pais->nombre}}</option>
                @endforeach
            </datalist>

        <input type="number" placeholder="numero_documento" id="numero_documento" name="numero_documento"> <br><br>
        <input type="text" placeholder="nombres" id="nombres" name="nombres"> <br><br>
        <input type="text" placeholder="apellidos" id="apellidos" name="apellidos"> <br><br>
        <input type="date" placeholder="fecha_nacimiento" id="fecha_nacimiento" name="fecha_nacimiento"> <br><br>

        <label>Sexo</label>
        <label for="sexo">F</label><input type="radio" id="sexo" name="sexo" value="F">
        <label for="sexo">M</label><input type="radio" id="sexo" name="sexo" value="M">
        <br><br>
        <label>Nacionalidad</label> <br><br>
        <label for="nacional">Colombiana</label>
        <input type="checkbox" id="nacional" name="nacional" value="nacional" checked>
        <label for="extranjero">Extranjero</label>
        <input type="checkbox" id="extranjero" name="nacional" value="Extranjero">

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




    </form>




</x-app-layout>