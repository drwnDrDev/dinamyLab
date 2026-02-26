<x-app-layout>

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-text leading-tight">
            {{ __('Company') }}
        </h2>
    </x-slot>
    <x-canva>
        <div class="py-4 flex justify-between items-center">
            <div>
                <p class="text-2xl font-bold leading-tight tracking-[-0.015em]">{{ $empresa->razon_social }} - {{ $empresa->nombre_comercial }}</p>
                <p class="text-teal-700">
                    <span id="tipo-documento">NIT: </span>
                    <span id="numero-documento">{{ $empresa->nit }}</span>
                </p>
            </div>
            <div class="flex gap-2">
                <picture>
                    <img src="{{ asset('storage/logos/ryc.png') }}" alt="Logo de la empresa" class="w-20 h-20 object-cover rounded-full">
                </picture>
            </div>
        </div>

        <div class="mt-4 flex justify-between items-center border-b border-borders">
            <div>
                <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-teal-500">Información de la Empresa</h2>
            </div>
            <div>
                <x-primary-button class="bg-teal-500">Administrar</x-primary-button>
            </div>
        </div>
        <div class="py-4 grid grid-cols-2" id="info">
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-teal-700  font-normal leading-normal">Dirección</p>
                <p class=" font-normal leading-normal">
                    {{ $empresa->direccion->direccion}} | {{ $empresa->direccion->municipio->municipio }}, {{ $empresa->direccion->municipio->departamento }}
                </p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-teal-700  font-normal leading-normal">Emails</p>
                <p class=" font-normal leading-normal">
                    {{ $empresa->emails->pluck('email')->join(', ') }}
                </p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pr-2">
                <p class="text-teal-700  font-normal leading-normal">Telefonos</p>
                <p class="font-normal leading-normal" id="telefonos">
                    @if($empresa->telefonos->isEmpty())
                    No tiene telefonos registrados
                    @else
                    @foreach($empresa->telefonos as $telefono)
                    <span>{{ $telefono->numero}}</span>
                    @endforeach
                    @endif
                </p>
            </div>
            <div class="flex flex-col gap-1 border-b border-borders py-4 pl-2">
                <p class="text-teal-700  font-normal leading-normal">Social</p>
                <p class=" font-normal leading-normal">
                    {{ $empresa->redesSociales->pluck('url')->join(', ') }}
                </p>
            </div>
        </div>

        <div class="mt-4 flex justify-between items-center border-b border-borders">
            <div>
                <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-teal-500">Sedes</h2>
            </div>
            <div>
                <x-primary-button id="editar-button" class="bg-teal-500">Nueva Sede</x-primary-button>
            </div>
        </div>
        <div class="py-4 w-full md:grid md:grid-cols-2 gap-4" id="sedes">
            @foreach ($empresa->sedes as $sede)
            <div class="card w-full gap-2 items-center p-4 border border-borders rounded-lg bg-gradient-to-tr from-secondary to-transparent">
                <div class="flex items-center mb-4">
                    <div><img src="{{ asset('storage/logos/' . $sede->logo) }}" alt="{{ $sede->nombre }}" class="w-16"></div>
                    <div class="pl-4">
                        <div class="font-semibold text-xl">
                            <p>{{ $sede->nombre }}</p>
                        </div>
                        <div class="font-normal text-sm text-gray-600">
                            <p>{{ $sede->direccion->direccion }}</p>
                        </div>
                    </div>
                </div>
                <div class="">
                    @can('update', $sede)
                    <a href="{{ route('sedes.show', $sede) }}">
                        <x-secondary-button class=" border border-teal-500">Administrar</x-secondary-button>
                    </a>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-between items-center border-b border-borders">
            <div>
                <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-teal-500">Empleados</h2>
            </div>
            <div>
                <x-primary-button id="editar-button" class="bg-teal-500">Nuevo Empleado</x-primary-button>
            </div>
        </div>
        <div class="w-full py-4" id="empleados">
            <table class="w-full table-auto border-collapse ">

                <tbody>
                    @foreach ($empresa->empleados as $empleado)
                    <tr>
                        <td class="border-b border-borders px-4 py-2">{{ $empleado->created_at->format('d/m/Y') }}</td>
                        <td class="border-b border-borders px-4 py-2">{{ $empleado->user->name }}</td>
                        <td class="border-b border-borders px-4 py-2">{{ $empleado->cargo }}</td>
                        <td class="border-b border-borders px-4 py-2 text-right">
                            
                            <a href="{{ route('empleados.show', $empleado) }}">
                                <x-secondary-button class="border border-teal-500">Administrar</x-secondary-button>
                            </a>
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
         <div class="mt-4 flex justify-between items-center border-b border-borders">
            <div>
                <h2 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 border-b-4 border-teal-500">Convenios</h2>
            </div>
            <div>
                <x-primary-button id="editar-button" class="bg-teal-500">Nuevo Convenio</x-primary-button>
            </div>
        </div>

    </x-canva>

</x-app-layout>