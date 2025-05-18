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
   
</x-app-layout>
