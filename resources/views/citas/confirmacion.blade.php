<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirmar Cita - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-gray-50">
    <div id="confirmacion-cita-root"
         data-pre-registro="{{ json_encode($preRegistro) }}"
         data-csrf="{{ csrf_token() }}"
         data-action="{{ route('citas.confirmar', $preRegistro->codigo_confirmacion) }}">
    </div>
</body>
</html>
