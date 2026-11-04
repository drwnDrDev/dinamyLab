
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $persona->nombreCompleto() }}
        </h2>
        <h3>
            {{ $persona->tipo_documento->nombre }} {{ $persona->numero_documento }}
        </h3>
    </x-slot>

    <x-canva>
        <form method="POST" action="{{ route('personas.update', $persona) }}" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach (['primer_nombre' => 'Primer nombre', 'segundo_nombre' => 'Segundo nombre', 'primer_apellido' => 'Primer apellido', 'segundo_apellido' => 'Segundo apellido'] as $campo => $etiqueta)
                <div>
                    <label for="{{ $campo }}">{{ $etiqueta }}</label>
                    <input id="{{ $campo }}" name="{{ $campo }}" value="{{ old($campo, $persona->$campo) }}" class="block w-full rounded border-gray-300">
                    @error($campo)<p class="text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div>
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input id="fecha_nacimiento" type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento?->format('Y-m-d')) }}" class="block w-full rounded border-gray-300">
                @error('fecha_nacimiento')<p class="text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" class="block w-full rounded border-gray-300">
                    <option value="">Sin especificar</option>
                    @foreach (['H' => 'Hombre', 'M' => 'Mujer', 'I' => 'Intersexual', 'F' => 'Femenino'] as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(old('sexo', $persona->sexo) === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
                @error('sexo')<p class="text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="telefono">Teléfono</label>
                <input id="telefono" name="telefono" value="{{ old('telefono', $persona->telefonos->first()?->numero) }}" class="block w-full rounded border-gray-300">
                @error('telefono')<p class="text-red-600">{{ $message }}</p>@enderror
            </div>

            <x-primary-button>Guardar cambios</x-primary-button>
        </form>
    </x-canva>
</x-app-layout>