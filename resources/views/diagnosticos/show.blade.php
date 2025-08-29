<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight print:hidden">
          {{$codigoDiagnostico->descripcion ?? 'CIE-10 Codes'}}
        </h2>
    </x-slot>
<x-canva>
    <div class="p-4">
        <h3 class="font-semibold text-lg text-text">Detalles del CIE-10</h3>
        <p><strong>Código:</strong> {{$codigoDiagnostico->codigo}}</p>
        <p><strong>Descripción:</strong> {{$codigoDiagnostico->descripcion}}</p>
        <p><strong>Grupo:</strong> {{$codigoDiagnostico->grupo}}</p>
        <p><strong>Subgrupo:</strong> {{$codigoDiagnostico->sub_grupo}}</p>
        <p><strong>Capitulo:</strong> {{$codigoDiagnostico->capitulo}}</p>
        <p><strong>Aplicabilidad</strong>

        @if ($codigoDiagnostico->sexo_aplicable == 'M')
           Hombres
        @elseif ($codigoDiagnostico->sexo_aplicable == 'F')
           Mujeres
        @else
           Ambos
        @endif
        </p>
        <p><strong>Edad mínima:</strong> {{$codigoDiagnostico->edad_minima}}</p>
        <p><strong>Edad máxima:</strong> {{$codigoDiagnostico->edad_maxima}}</p>
        <p><strong>Grupo de mortalidad:</strong> {{$codigoDiagnostico->grupo_mortalidad}}</p>
        <form action="{{ route('diagnosticos.activar', $codigoDiagnostico->id) }}" method="POST">
            @csrf
            @method('patch')
            <button type="submit" class="mt-4 px-4 py-2 bg-primary text-white rounded-md hover:bg-secondary">
                @if ($codigoDiagnostico->activo)
                    Desactivar
                    <input type="hidden" name="estado" value="Inactivo">
                @else
                    Activar
                    <input type="hidden" name="estado" value="Activo">
                @endif
            </button>
        </form>

    </div>

</x-canva>



</x-app-layout>
