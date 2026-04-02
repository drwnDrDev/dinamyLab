<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'DinamyLAB') }}</title>

        <script>
            (function() {
                var theme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark', theme === 'dark');
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="font-sans antialiased min-h-screen flex items-center justify-center bg-background dark:bg-slate-950 transition-colors duration-300">

        <!-- Fondo decorativo clínico -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/5 dark:bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-cyan-200/20 dark:bg-cyan-900/20 rounded-full blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-sm px-4">

            <!-- Header branding -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 dark:bg-primary/20 border border-primary/20 dark:border-primary/30 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-text dark:text-white tracking-tight">DinamyLAB</h1>
                <p class="text-sm text-muted dark:text-slate-400 mt-1">Sistema de Gestión de Laboratorio Clínico</p>
            </div>

            <!-- Card login -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-borders/60 dark:border-slate-800 shadow-[0_8px_40px_-8px_rgba(14,180,209,0.15)] dark:shadow-[0_8px_40px_-8px_rgba(0,0,0,0.4)] px-8 py-8">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-muted dark:text-slate-500 mt-6">
                &copy; {{ date('Y') }} DinamyLAB · Todos los derechos reservados
            </p>
        </div>

    </body>
</html>
