<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="font-sans antialiased bg-background dark:bg-slate-950 dark:text-white">
        @include('layouts.header')

            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="m-auto mt-16 pl-14 w-full h-full min-h-[calc(100vh-65px)] overflow-y-auto bg-gradient-to-br from-background via-blue-50 to-cyan-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-950 print:!p-0 print:!m-0 print:!bg-white">
                @include('layouts.notifications')
                 <!-- Page Heading -->
                    @isset($header)
                            <div class="max-w-5xl mx-2 lg:mx-auto sm:p-2 md:p-4 lg:p-8 print:hidden">
                                {{ $header }}
                            </div>

                    @endisset

                {{ $slot ?? null }}
            </main>
    </body>
</html>
