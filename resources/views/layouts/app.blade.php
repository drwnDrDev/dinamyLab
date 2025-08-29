<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Laboratorio  Clinico' }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/react/index.jsx'])
    </head>
    <body class="font-sans antialiased bg-background">
        @include('layouts.header')
        <div class="min-h-screen bg-background grid grid-rows-[auto_1fr] lg:grid-cols-[56px_1fr] h-screen">
            
            @include('layouts.navigation')
 
            <!-- Page Content -->
            <main class="m-auto mt-16 ml-14 p-6 w-[calc(100vw-56px)] md:h-[calc(100vh-64px)] overflow-y-auto h-full print:!p-0 print:!m-0 print:!bg-white">

            <div id="react-test"></div>
            @vite(['resources/js/react/test.jsx'])


                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if (session('success') || session('error'))
                        @if (session('success'))
                            <div class="text-xs bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 print:hidden " role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 border border-red-400 text-xs text-red-700 px-4 py-3 rounded relative mb-4 print:hidden" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                    @endif
                 <!-- Page Heading -->
                    @isset($header)
                        <header >
                            <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                </div>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
