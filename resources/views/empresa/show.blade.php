<x-app-layout>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Company') }}
    </h2>
</x-slot>
<x-canva>
    <div class="py-3">
        <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Información de la empresa</h2>
        <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
            <div class="flex w-full flex-col">
                <div class="flex items-center justify-between border-b border-borders px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="text-text text-base font-semibold">Empresa</span>
                    </div>
                </div>
                <div class="flex flex-col gap-2 px-4 py-3">
                    <p><strong>Nombre:</strong> {{ $empresa->razon_social }} - {{ $empresa->nombre_comercial }}</p>
                    <p><strong>NIT:</strong> {{ $empresa->nit }}</p>
                    <p><strong>Dirección:</strong> {{ $empresa->direccion->direccion}} /{{ $empresa->direccion->municipio->municipio }}, {{ $empresa->direccion->municipio->departamento }}</p>
                    <p><strong>Teléfonos:</strong> {{ $empresa->telefonos->pluck('numero')->join(', ') }}</p>
                    <p><strong>Emails:</strong> {{ $empresa->emails->pluck('email')->join(', ') }}</p>
                    <p><strong>Redes Sociales:</strong> {{ $empresa->redesSociales->pluck('url')->join(', ') }}</p>

                    <div class="mt-4 flex flex-col gap-2">
                        <header class="bg-slate-600 dark:bg-slate-800 dark:text-blue-50">Sedes</header>
                        @foreach ($empresa->sedes as $sede)
                            <a href=""> {{ $sede->nombre }} - {{ $sede->direccion->direccion }}</a>
                        @endforeach

                    </div>

                    <div  class="mt-4 flex flex-col gap-2">
                    <header class="bg-slate-600 dark:bg-slate-800 dark:text-blue-50">Empleados</header>
                    @foreach ($empresa->empleados as $empleado)
                        <a href="{{route('empleados.show',$empleado)}}"> {{ $empleado->user->name }} - {{ $empleado->cargo }}</a>
                    @endforeach
                    </div>



                </div>
            </div>
        </div>
    </div>
</x-canva>

</x-app-layout>

