@php
    // Flash de sesión que se muestran como toast. Los textos por campo de la
    // validación siguen junto a cada campo; aquí solo va el resumen.
    $toasts = [];

    if (request()->routeIs('login') && session()->has('url.intended')) {
        $toasts[] = ['type' => 'info', 'title' => 'Inicia sesión para continuar.', 'message' => 'La página que pediste requiere una sesión activa.'];
    }

    foreach (['success', 'error', 'warning', 'info'] as $tipo) {
        if (filled(session($tipo)) && is_string(session($tipo))) {
            $toasts[] = ['type' => $tipo, 'title' => null, 'message' => session($tipo)];
        }
    }

    if ($errors->any()) {
        $toasts[] = [
            'type' => 'error',
            'title' => 'No se pudo completar la solicitud.',
            'message' => $errors->count() === 1
                ? 'Hay un error. Revisa los mensajes junto a los campos e inténtalo de nuevo.'
                : 'Hay '.$errors->count().' errores. Revisa los mensajes junto a los campos e inténtalo de nuevo.',
        ];
    }
@endphp

<div
    id="toast-root"
    class="pointer-events-none fixed right-4 top-20 z-50 flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-2 print:hidden"
>
    @foreach ($toasts as $toast)
        <x-toast :type="$toast['type']" :title="$toast['title']" :message="$toast['message']" />
    @endforeach
</div>

{{-- Plantillas para los toasts creados desde JS (window.toast / lib/toast.js). --}}
@foreach (['success', 'info', 'warning', 'error'] as $tipo)
    <template data-toast-template="{{ $tipo }}">
        <x-toast :type="$tipo" />
    </template>
@endforeach
