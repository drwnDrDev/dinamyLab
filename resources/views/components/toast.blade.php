{{--
    Un toast. Lo usa el layout para los flash del servidor y, dentro de un
    <template>, toastDom.js para los toasts creados desde JS (mismos estilos).
--}}
@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
])

@php
    $tipos = [
        'success' => [
            'title' => 'Operación completada',
            'role' => 'status',
            'live' => 'polite',
            'classes' => 'border-green-300 bg-green-50 text-green-900 dark:border-green-800 dark:bg-green-950 dark:text-green-100',
            'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        ],
        'info' => [
            'title' => 'Información',
            'role' => 'status',
            'live' => 'polite',
            'classes' => 'border-blue-300 bg-blue-50 text-blue-950 dark:border-blue-800 dark:bg-blue-950 dark:text-blue-100',
            'icon' => 'm11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z',
        ],
        'warning' => [
            'title' => 'Atención',
            'role' => 'alert',
            'live' => 'assertive',
            'classes' => 'border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100',
            'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z',
        ],
        'error' => [
            'title' => 'No se pudo completar la solicitud',
            'role' => 'alert',
            'live' => 'assertive',
            'classes' => 'border-red-300 bg-red-50 text-red-900 dark:border-red-800 dark:bg-red-950 dark:text-red-100',
            'icon' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
        ],
    ];
    $config = $tipos[$type] ?? $tipos['info'];
@endphp

<div
    data-toast
    data-toast-type="{{ array_key_exists($type, $tipos) ? $type : 'info' }}"
    role="{{ $config['role'] }}"
    aria-live="{{ $config['live'] }}"
    aria-atomic="true"
    class="pointer-events-auto flex w-full items-start gap-3 rounded-md border px-4 py-3 text-sm shadow-lg transition duration-200 ease-out motion-reduce:transition-none {{ $config['classes'] }}"
>
    <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $config['icon'] }}" />
    </svg>
    <div class="min-w-0 flex-1">
        <p class="font-semibold" data-toast-title>{{ $title ?? $config['title'] }}</p>
        <p class="mt-1 break-words" data-toast-message>{{ $message }}</p>
    </div>
    {{-- El botón cerrar lo muestra toastDom.js; sin JS no hace nada. --}}
    <button
        type="button"
        data-toast-close
        hidden
        class="-m-1 shrink-0 rounded p-1 opacity-70 hover:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-current"
        aria-label="Cerrar notificación"
    >
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>
