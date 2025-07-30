@props(['item'])

@if ($item['nombre'] !== 'observaciones')
    {{-- Parámetros normales --}}
    <td class="text-left py-2 pl-2 uppercase max-w-80">
        <label>{{$item['nombre']}}</label>
        @if($item['metodo'])
            <span class="block font-light print:!text-[0.6rem] text-titles">{{$item['metodo']}}</span>
        @endif
    </td>
    <td class="text-right py-2">
        <div class="flex flex-row-reverse">
            <p id="{{$item['id']}}">{{$item['resultado']}}</p>
            @if (!$item['es_normal'])
                <span class="font-bold pr-4">*</span>
            @endif
        </div>
    </td>
    <td class="text-left py-2">
        <p class="font-light">{{$item['unidades']}}</p>
    </td>
    <td class="text-left py-2">
        <p class="font-light">{{$item['referencia']}}</p>
    </td>
@else
    {{-- Observaciones --}}
    <td colspan="4" class="py-2 pl-2">
        <div class="flex flex-wrap gap-4 my-2">
            <div class="uppercase font-semibold">
                <label>{{$item['nombre']}}</label>
            </div>
            <div class="font-italic">
                <p name="{{$item['id']}}" id="{{$item['id']}}" class="w-full">{{$item['resultado']}}</p>
            </div>
        </div>
    </td>
@endif
