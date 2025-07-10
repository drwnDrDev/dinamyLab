<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Sede</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    

</head>
<body>
<x-canva>
    <h1 class="text-2xl font-bold leading-tight tracking-[-0.015em] p-4 pl-0">
        seleccione una sede
    </h1>
            <div class="p-6 text-gray-900">
                <p class="p-2 bg-slate-300">{{$empleado->empresa->nombre_comercial}}</p>
                {{$empleado->cargo}}
            </div>
           
        <section>
            @foreach ($empleado->sedes as $sede)
            <a class="p-4 border-b border-borders" href="{{route('elegir.sede', $sede->id)}}">
                <h2 class="text-xl font-semibold">{{$sede->nombre}}</h2>
            </a>  
            @endforeach              
        
        </section>
</x-canva>    
</body>
</html>

