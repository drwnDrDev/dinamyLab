<x-app-layout>
    @dump($persona)
    <article id="react-crear-orden" data-persona='@json($persona)'></article>

    @vite(['resources/js/react-app.jsx'])
</x-app-layout>
