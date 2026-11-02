@php
    $notifications = [
        [
            'message' => session('success'),
            'title' => 'Operación completada',
            'classes' => 'border-green-300 bg-green-50 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-100',
            'role' => 'status',
            'live' => 'polite',
        ],
        [
            'message' => session('error'),
            'title' => 'No se pudo completar la solicitud',
            'classes' => 'border-red-300 bg-red-50 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100',
            'role' => 'alert',
            'live' => 'assertive',
        ],
        [
            'message' => session('warning'),
            'title' => 'Atención',
            'classes' => 'border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100',
            'role' => 'alert',
            'live' => 'assertive',
        ],
        [
            'message' => session('info'),
            'title' => 'Información',
            'classes' => 'border-blue-300 bg-blue-50 text-blue-950 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-100',
            'role' => 'status',
            'live' => 'polite',
        ],
    ];
@endphp

<div class="mx-auto max-w-7xl space-y-3 px-4 pt-4 sm:px-6 lg:px-8 print:hidden">
    @if (request()->routeIs('login') && session()->has('url.intended'))
        <div class="rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100" role="status" aria-live="polite">
            <p class="font-semibold">Inicia sesión para continuar.</p>
        </div>
    @endif

    @foreach ($notifications as $notification)
        @if (filled($notification['message']))
            <div class="rounded-md border px-4 py-3 text-sm {{ $notification['classes'] }}" role="{{ $notification['role'] }}" aria-live="{{ $notification['live'] }}">
                <p class="font-semibold">{{ $notification['title'] }}</p>
                <p class="mt-1">{{ $notification['message'] }}</p>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100" role="alert" aria-live="assertive">
            <p class="font-semibold">No se pudo completar la solicitud.</p>
            <p class="mt-1">
                @if ($errors->count() === 1)
                    Hay un error. Revisa los mensajes junto a los campos e inténtalo de nuevo.
                @else
                    Hay {{ $errors->count() }} errores. Revisa los mensajes junto a los campos e inténtalo de nuevo.
                @endif
            </p>
        </div>
    @endif
</div>
