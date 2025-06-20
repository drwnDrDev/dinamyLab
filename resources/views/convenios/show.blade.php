<x-app-layout>
    <x-slot name="title">
        {{$convenio->razon_social}} - Convenios y Colaboradores
    </x-slot>

    <x-canva>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">{{$convenio->razon_social}}</h1>

    @can('eliminar', $convenio)
      <form action="{{ route('convenios.destroy', $convenio->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <x-danger-button type="submit" class="w-40" id="tipoGuardado" name="tipoGuardado">Eliminar Convenio</x-danger-button>
      </form>

    @endcan


    <x-primary-button  href="{{ route('convenios.update',$convenio) }}" class="w-40" id="tipoGuardado" name="tipoGuardado">Editar Convenio</x-primary-button>

    </div>
    <div class="mb-4">
      <h2 class="text-xl font-semibold">Detalles del Convenio</h2>
      <p><strong>NIT:</strong> {{ $convenio->nit }}</p>
      <p><strong>Teléfono:</strong> {{ $convenio->contacto->telefono }}</p>
      <p><strong>Correo:</strong> {{$convenio->contacto->infoAdicional('correo')}}</p>
      <p><strong>Ciudad:</strong>{{ $convenio->contacto->municipio->municipio }} {{ $convenio->contacto->municipio->departamento }} {{$convenio->contacto->infoAdicional('pais')}} </p>
      <p><strong>Dirección:</strong> {{$convenio->contacto->infoAdicional('direccion')}}</p>
      @if($convenio->contacto->infoAdicional('linkedin'))
        <p> {{$convenio->contacto->infoAdicional('linkedin')}}</p>
      @endif
          @if($convenio->contacto->infoAdicional('whatsapp'))
        <a href="https://wa.me/+57{{$convenio->contacto->infoAdicional('whatsapp')}}"><strong>Whatsapp: </strong> {{$convenio->contacto->infoAdicional('whatsapp')}}</a>
      @endif
    </div>


     </x-canva>

</x-app-layout>
