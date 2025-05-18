<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Sistema de gestión de resultados médicos.">

        <title>{{ config('app.name', 'Mis resultados') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
          <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        

            <main>
                <div class="flex flex-col items-center justify-center min-h-screen py-6 sm:py-12 lg:py-24">
                
                 <form action="buscar" method="POST" class="w-full max-w-md p-8 space-y-3 rounded-xl bg-white shadow-md">
                    @csrf
                    <h1 class="text-2xl font-semibold text-center">Resultados de análisis clínicos</h1>
                    <p class="text-sm text-center text-gray-400">Ingrese su número de documento para ver los resultados.</p>
                    <div class="space-y-1 text-sm">
                        <label for="documento" class="block text-gray-600">Número de documento</label>
                        <input type="text" name="documento" id="documento" placeholder="Número de documento" class="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring focus:ring-blue-400" required>

                 </form>   
               
            </main>
            <footer>
                Laboratorio de análisis clínicos - Todos los derechos reservados &copy; {{ date('Y') }}<br>
            </footer>

    </body>
</html>
