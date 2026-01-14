@extends('layouts.app')

@section('content')
<div id="detalle-cita-root"
     data-pre-registro="{{ json_encode($preRegistro) }}"
     data-csrf="{{ csrf_token() }}"
     data-url-update="{{ route('citas.updateEstado', $preRegistro->id) }}"
     data-url-cancelar="{{ route('citas.cancelar', $preRegistro->id) }}"
     data-url-listado="{{ route('citas.index') }}">
</div>
@endsection
