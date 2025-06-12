<x-app-layout>
  <x-slot name="title">
    {{ __('Facturación') }}
  </x-slot>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Facturación') }}
    </h2>
  </x-slot>
  <x-canva>
    <div class="py-3 w-1/2 mx-auto">
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
            placeholder="Search by number invoice or orden ID"
            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-text focus:outline-0 focus:ring-0 border-none bg-secondary focus:border-none h-full placeholder:text-titles px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
            value="" />
        </div>
      </label>
    </div>

    <h2 class="text-text text-2xl font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Facturas recientes</h2>
    <div class="my-3 flex overflow-hidden rounded-xl border border-borders bg-background">
      <table class="flex-1">
        <thead>

          <tr class="bg-background">
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Fecha</th>
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal"># de Factura</th>
            <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Pagador</th>
            <th class="px-4 py-3 text-left text-text w-60 text-sm font-medium leading-normal">Valor</th>
            <th class="px-4 py-3 text-left text-text w-40 text-sm font-medium leading-normal">Estado</th>
          </tr>
        </thead>
        <tbody>

          @foreach ($facturas as $factura)
          <tr class="border-t border-borders">
            <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
              {{ $factura->created_at->format('Y-m-d') }}
            </td>
            <td class="content-start px-4 py-2 w-40 text-titles text-sm font-normal leading-normal">
              <a href="{{route('ordenes.show',$orden)}}" class="text-titles">{{$factura->numero}}</a>
            </td>
            <td class="content-start px-4 py-2 w-60 text-sm font-normal leading-normal">
              {{ $factura->pagador->nombre}}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </x-canva>

</x-app-layout>