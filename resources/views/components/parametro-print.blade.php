@props(['item'])


    <div class="Componente_param grid [grid-template-columns:2fr_1fr_1fr_1fr] gap-2 pl-2">
         
            <div class="uppercase"><label>{{$item->parametro->nombre}}</label>
            <span class="block font-light text-[0.8rem] text-titles">{{$item->parametro->metodo}}</span>                       
            </div>
            <div class="flex flex-row-reverse justify-between">
                <p id="{{$item->id}}">{{$item->resultado}}</p>
            @if (!$item->es_normal)  
                    <span class="font-bold pl-20">
                    *
                    </span>
                @endif    
            
            </div>
            <div><p class="font-light">{{$item->parametro->unidades}}</p></div>
            <div><p>{{$item->valor_referencia}}</p></div>
        
    </div>