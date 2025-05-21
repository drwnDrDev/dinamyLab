
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

        <button type="submit">Enviar</button>
</form>
</x-app-layout>
