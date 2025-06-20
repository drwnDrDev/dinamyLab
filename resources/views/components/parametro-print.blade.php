@props(['item'])

    @if ($item->parametro->nombre !== 'observaciones')
        
    <div class="Componente_param grid [grid-template-columns:minmax(max-content,2fr)_1fr_1fr_minmax(max-content,1fr)] gap-2 pl-2 py-1 border-b-[0.2px] border-dashed border-secondary">
         
            <div class="uppercase"><label>{{$item->parametro->nombre}}</label>
            <span class="block font-light text-[0.8rem] text-titles">{{$item->parametro->metodo}}</span>                       
            </div>
            <div class="flex flex-row-reverse">
                <p id="{{$item->id}}">{{$item->resultado}}</p>
            @if (!$item->es_normal)  
                    <span class="font-bold pr-4">
                    *
                    </span>
            @endif    
            
            </div>
            <div><p class="font-light">{{$item->parametro->unidades}}</p></div>
            <div><p class="font-light">{{$item->valor_referencia}}</p></div>
        
    </div>
    @else

    <div class="flex flex-wrapp gap-4 pl-2 my-2">
            <div class="uppercase font-semibold"><label>{{$item->parametro->nombre}}</label></div>
            <div class="font-italic">
                <p name="{{$item->id}}" id="{{$item->id}}" class="w-full">{{$item->resultado}}</p>
            </div>
    </div>
    @endif