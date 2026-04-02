<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Laboratorio  Clinico' }}</title>
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <!-- Dark mode: aplicar clase antes de renderizar para evitar flash -->
        <script>
            (function() {
                var theme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark', theme === 'dark');
            })();
        </script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="font-sans antialiased bg-background dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300">
        @include('layouts.header')
        @include('layouts.navigation')

        <!-- Page Content -->
        <main class="mt-16 pl-14 w-full min-h-[calc(100vh-64px)] bg-background dark:bg-slate-950 print:!p-0 print:!m-0 print:!bg-white print:!pl-0">

            @if (session('success') || session('error'))
                <div class="max-w-5xl mx-auto px-4 pt-4 print:hidden">
                    @if (session('success'))
                        <div class="flex items-center gap-3 text-sm bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl mb-2" role="alert">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="flex items-center gap-3 text-sm bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl mb-2" role="alert">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            @isset($header)
                <div class="max-w-5xl mx-2 lg:mx-auto px-2 sm:px-4 pt-6 pb-2 print:hidden">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot ?? null }}
        </main>
    </body>
</html>
