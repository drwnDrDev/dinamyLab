      @props(['isNormal' => true, 'resultado' => ''])
      
    <div class="p-2 text-right">
        @if(!$isNormal)
            *
        @endif
         {{ $resultado}}
    </div>
