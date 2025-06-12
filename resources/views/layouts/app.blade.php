<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Laboratorio  Clinico' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-background md:grid md:grid-rows-[auto_1fr] md:grid-cols-[250px_1fr] h-screen">
            @include('layouts.header')
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="w-full m-auto p-6 md:ml-60 md:mt-16 md:w-[calc(100vw-240px)] md:h-[calc(100vh-64px)] overflow-y-auto h-full">
                 <!-- Page Heading -->
                    @isset($header)
                        <header >
                            <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
