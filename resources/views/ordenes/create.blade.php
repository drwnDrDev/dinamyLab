<x-app-layout>

<script>
    window.appData = {
        sede: @json(session('sede')),
        user: @json(auth()->user()),
        permisos: @json(auth()->user()->getAllPermissions()->pluck('name'))
    };
</script>

    <article id="react-crear-orden"></article>

    @vite(['resources/js/react-app.jsx'])
</x-app-layout>
