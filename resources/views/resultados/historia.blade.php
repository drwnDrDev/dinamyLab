<x-app-layout>
    <h1>Aqui va el select de los procedimiento a imprimir</h1>


    <form action="{{route('resultados.historia_show', $persona)}}" method="POST">
        @csrf
        <button type="submit">imprimir</button>
    </form>
    @dump($persona)
    @dump($ordenes)
</x-app-layout>