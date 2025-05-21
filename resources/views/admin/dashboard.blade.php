<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
<div class="w-full min-w-96 flex flex-col justify-around">
            <div class="p-4">
                <h2>Empleados</h2>
                <div class="grid grid-cols-3 justify-around gap-2">
                    <div class="flex gap-2 bg-slate-300 p-2">
                            <p class="text-gray-900">Nombre</p>
                            <p class="text-gray-900">Cargo</p>
                            <p class="text-gray-900">Sede</p>
                    </div>
                   @foreach ($empleados as $empleado)
                        <div class="flex gap-2">
                            <p class="text-gray-900">{{ $empleado->persona->nombreCompleto() }}</p>
                            <p class="text-gray-900">{{ $empleado->cargo }}</p>
                            <p class="text-gray-900">{{ $empleado->sede->nombre }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class=" p-4">
                <h2>Usuarios</h2>
                <div class="flex flex-col">
                    <div class="flex gap-2 bg-slate-300 p-2">
                            <p class="text-gray-900">Nombre</p>
                            <p class="text-gray-900">Email</p>
                            <p class="text-gray-900">Acciones</p>
                    </div>
                   @foreach ($usuarios as $usuario)
                        <div class="flex gap-2">
                            <p class="text-gray-900">{{ $usuario->name }}</p>
                            <p class="text-gray-900">{{ $usuario->email }}</p>
                            <p class="text-gray-900"><button >Promover a empleado</button></p>
                        </div>
                    @endforeach
                </div>

    </div>


    </div>

<form id="crearPeronsa" class="w-full max-w-[600px] p-4 mx-auto" method="POST">
    @csrf
    <div class="grid grid-cols-2 justify-around items-center gap-2">
        <label for="tipo_documento">Tipo de Documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-select">
            @foreach ($tipos_documento as $valor => $nombre)
                <option value="{{ $valor }}">{{ $nombre }}</option>
            @endforeach
        </select>
        <div class="flex justify-around items-center col-span-2 gap-2">
            <label for="numero_documento" class="flex items-end">Numero de Documento</label>
            <input type="text" name="numero_documento" id="numero_documento" class="border border-gray-300 p-2 rounded">
            <button id="buscarPersona" class="flex gap-2 bg-blue-500 text-white p-2 rounded">Buscar <span class="hidden sm:flex">Persona</span> </button>
            <select name="datos_adicionales[pais]" id="pais" class="hidden">
                <option value="COL">Colombia</option>
            </select>
        </div>
        <label for="nombre">Nombres</label>
        <input type="text" name="nombres" id="nombres" class="border border-gray-300 p-2 rounded">
        <label for="apellidos">Apellidos</label>
        <input type="text" name="apellidos" id="apellidos" class="border border-gray-300 p-2 rounded">
        <label for="sexo">Sexo</label>
        <select name="sexo" id="sexo" class="form-select">
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
        </select>
        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="border border-gray-300 p-2 rounded">
        <label for="telefono">Telefono</label>
        <input type="text" name="telefono" id="telefono" class="border border-gray-300 p-2 rounded">
        <label for="email">Email</label>
        <input type="email" name="datos_adicionales[email]" id="email" class="border border-gray-300 p-2 rounded">
        <label for="direccion">Direccion</label>
        <input type="text" name="datos_adicionales[direccion]" id="direccion" class="border border-gray-300 p-2 rounded">
        <label for="eps">EPS</label>
        <input list="eps" name="datos_adicionales[eps]" id="prestador" class="form-select">
        <datalist id="eps">
            @foreach ($eps as $prestador)
            <option value="{{ $prestador->nombre }}"></option>
            @endforeach
        </datalist>
        <label for="afiliacion">Afiliacion</label>
        <select name="datos_adicionales[afiliacion]" id="afiliacion" class="form-select">
            <option value="1">Contributivo</option>
            <option value="2">Subsidiado</option>
        </select>

        
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Nueva Persona</button>
    </div>

</form>



    <div id="examenes" class="grid grid-cols-2 p-2 gap-2">
        <h2 class="col-span-2">Examenes</h2>
    </div>

    @vite('resources/js/formularioPersona.js')



</x-app-layout>
