@props(['item'])

    @if ($item['tipo_dato'] !== 'textarea')

    <div class="Componente_param grid [grid-template-columns:minmax(max-content,40%)_20%_20%_20%] gap-2 pl-2 border-b-[0.2px] border-dashed border-secondary">

            <div class="uppercase max-w-80"><label>{{$item['nombre']}}</label>
            <span class="block font-light print:!text-[0.6rem] text-titles">{{$item['metodo']}}</span>
            </div>
            <div class="flex flex-row-reverse capitalize font-semibold">
                <p id="{{$item['id']}}">{{$item['resultado']}}</p>
            @if (!$item['es_normal'])
                    <span class="font-bold pr-4">
                    *
                    </span>
            @endif

            </div>
            <div><p class="font-light">{{$item['unidades']}}</p></div>
            <div><p class="font-light">{{$item['referencia']?? null}}</p></div>

    </div>
    @else

    <div class="flex flex-wrapp gap-4 pl-2 my-2">
            <div class="uppercase font-semibold"><label>{{$item['nombre']}}</label></div>
            <div class="font-italic">
                <p name="{{$item['id']}}" id="{{$item['id']}}" class="w-full">{{$item['resultado']}}</p>
            </div>
    </div>
    @endif
