<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-background via-blue-50 to-cyan-100 p-4 dark:from-slate-900 dark:via-slate-900 dark:to-black">
            <div class="mb-8 flex flex-col items-center text-center">
                    <div class="mb-4 flex items-center justify-center gap-3">
                        <x-application-logo class="w-10 h-10" />
                        <h1 class="font-headline text-5xl font-bold tracking-tighter text-primary">LissApp</h1>
                    </div>
                    <p class="max-w-xs text-lg text-slate-500 font-semibold">{{__('Your trusted partner in clinical laboratory management')}}</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-2xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
