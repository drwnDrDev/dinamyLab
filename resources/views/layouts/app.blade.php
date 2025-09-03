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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background">
        @include('layouts.header')
                    
            @include('layouts.navigation')
 
            <!-- Page Content -->
            <main class="m-auto mt-16 pl-14 w-full h-full min-h-[calc(100vh-65px)] overflow-y-auto bg-gradient-to-br from-background via-blue-50 to-cyan-50 dark:from-slate-900 dark:via-slate-900 dark:to-black print:!p-0 print:!m-0 print:!bg-white">

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
                            <div class="max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8">
                                {{ $header }}
                            </div>
                        
                    @endisset

                {{ $slot }}
            </main>
    </body>
</html>
