<x-app-layout>
<x-slot:title>
    citas asignadas
</x-slot:title>

<div id="listado-citas-root"
     data-pre-registros="{{ json_encode($preRegistros) }}"
     data-filtros="{{ json_encode($filtros ?? []) }}"
     data-csrf="{{ csrf_token() }}"
     data-url-filtrar="{{ route('citas.filtrar') }}">
</div>
</x-app-layout>
