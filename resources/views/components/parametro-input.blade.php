@props(['parametro'])


    <div class="Componente_param grid grid-cols-4 gap-2 pl-4 mb-2 text-sm items-center">

        @switch ($parametro['tipo_dato'])
            @case ('text')

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><input type="text" name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md" require></div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>

            @break
            @case ('number')

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><input type="number" step="0.005" name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md" require></div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>
            @break
            @case ('date')

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><input type="date" name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md"></div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>
            @break
            @case ('select')

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><select name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 pr-10 border-borders focus:border-primary focus:ring-primary
            rounded-md">
                    @foreach ($parametro['opciones'] as $opcion)
                    <option value="{{$opcion}}" class="active:bg-titles">{{$opcion}}</option>
                    @endforeach
                </select></div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>
            @break
            @case ('list')

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><input list="opciones-{{$parametro['id']}}" name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md" require>
                    <datalist id="opciones-{{$parametro['id']}}">
                    @foreach ($parametro['opciones'] as $opcion)
                    <option value="{{$opcion}}">{{$opcion}}</option>
                    @endforeach
                    </datalist>
            </div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>
            @break

            @case ('textarea')

            <div class="pt-4 uppercase font-semibold"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="pt-4 col-span-3"><textarea name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" rows="2" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md"></textarea>
            </div>
            
            @break

            @default

            <div class="uppercase"><label for="{{$parametro['id']}}">{{$parametro['nombre']}}</label></div>
            <div class="text-end"><input type="text" name="{{$parametro['id']}}" id="{{$parametro['id']}}" value="{{ $parametro['default'] ?? '' }}" class="w-full p-2 border-borders focus:border-primary focus:ring-primary
            rounded-md" require></div>
            <div><p>{{$parametro['unidades']}}</p></div>
            <div><p>{{$parametro['referencia']}}</p></div>
            @break

        @endswitch
    </div>
