<x-app-layout>
    <x-slot name="title">
        Aliados y Colaboradores
    </x-slot>

    <x-canva>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Convenios y Colaboradores</h1>
      <form method="GET" action="{{ route('convenios.index') }}" class="flex flex-1 items-center gap-2 py-3">

      <label class="flex flex-col min-w-40 h-12 w-full">
        <div class="flex w-full flex-1 items-stretch rounded-xl h-full">
          <div
            class="text-titles flex border-none bg-secondary items-center justify-center pl-4 rounded-l-xl border-r-0"
            data-icon="MagnifyingGlass"
            data-size="24px"
            data-weight="regular">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
              <path
                d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
            </svg>
          </div>
          <input
            type="search"
            name="busqueda"
            value="{{ request('busqueda') }}"
            placeholder="Razón social, NIT, contrato o teléfono"
            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
             />
        </div>
      </label>
      <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-white">Buscar</button>
      <a href="{{ route('convenios.index') }}" class="rounded-lg border border-borders px-4 py-2 text-text">Limpiar</a>
      </form>
    <x-primary-button  href="{{ route('convenios.create') }}" class="w-40" id="tipoGuardado" name="tipoGuardado">Nuevo Convenio</x-primary-button>

    </div>


    <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
      <table class="flex-1">
        <thead>

          <tr class="bg-background">
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Razon Social</th>
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">NIT</th>
            <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Telefono</th>
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Acciones</th>
          </tr>
        </thead>
        <tbody>

        @if($convenios->isEmpty())
            <tr><td colspan="4" class="px-4 py-6 text-center text-text">No se encontraron convenios.</td></tr>
        @else

        @foreach ($convenios as $convenio)
          <tr class="border-t border-borders">
            <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
              {{ $convenio->razon_social }}
            </td>

            <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
              {{ $convenio->numero_documento }}
            </td>
            <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
              {{ $convenio->telefonos->first()?->numero ?? 'No disponible' }}
            </td>
            <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
              <a href="{{ route('convenios.show', $convenio->id) }}" class="text-blue-500 hover:underline">Ver Detalles</a>
            </td>
          </tr>
          @endforeach
        @endif
        </tbody>
      </table>
    </div>
  </x-canva>
  {{ $convenios->links() }}

</x-app-layout>
