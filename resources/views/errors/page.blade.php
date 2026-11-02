@php
    [$title, $message] = match ($status) {
        401, 403 => ['Acceso no autorizado', 'No tienes permiso para consultar esta página. Si crees que es un error, contacta al administrador del laboratorio.'],
        404 => ['No encontramos esta página', 'La dirección puede haber cambiado o la página ya no está disponible.'],
        419 => ['La sesión expiró', 'El formulario venció o tu sesión terminó. Vuelve al inicio, ingresa de nuevo e inténtalo otra vez.'],
        422 => ['No pudimos procesar la solicitud', 'Revisa la información enviada e inténtalo de nuevo.'],
        429 => ['Demasiadas solicitudes', 'Espera un momento antes de volver a intentarlo.'],
        500, 501, 502, 503, 504 => ['Tuvimos un problema', 'Ocurrió un error inesperado y no pudimos completar la solicitud. Inténtalo más tarde o contacta al administrador del laboratorio.'],
        default => ['No se pudo completar la solicitud', 'Revisa la dirección o la información enviada e inténtalo de nuevo.'],
    };
@endphp
<!DOCTYPE html>
<html lang="es-CO">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} | {{ config('app.name') }}</title>
        <style>
            :root {
                color-scheme: light;
                font-family: ui-sans-serif, system-ui, sans-serif;
                color: #0f172a;
                background: #f8fafc;
            }

            * { box-sizing: border-box; }
            body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 1.5rem; }
            main { width: min(100%, 38rem); padding: 2.5rem; border: 1px solid #cbd5e1; border-radius: 1rem; background: #fff; box-shadow: 0 20px 40px rgb(15 23 42 / 10%); }
            .code { margin: 0; color: #0369a1; font-size: .875rem; font-weight: 700; letter-spacing: .12em; }
            h1 { margin: .75rem 0; font-size: clamp(1.5rem, 5vw, 2rem); line-height: 1.2; }
            .message { color: #334155; line-height: 1.6; }
            a { display: inline-block; margin-top: 1.5rem; border-radius: .5rem; background: #075985; padding: .7rem 1rem; color: #fff; font-weight: 600; text-decoration: none; }
            a:hover, a:focus-visible { background: #0c4a6e; }

            @media (prefers-color-scheme: dark) {
                :root { color-scheme: dark; color: #f8fafc; background: #020617; }
                main { border-color: #334155; background: #0f172a; box-shadow: 0 20px 40px rgb(0 0 0 / 30%); }
                .code { color: #7dd3fc; }
                .message { color: #cbd5e1; }
            }

            @media (max-width: 480px) {
                main { padding: 1.5rem; }
            }
        </style>
    </head>
    <body>
        <main aria-labelledby="error-title">
            <p class="code">Error {{ $status }}</p>
            <h1 id="error-title">{{ $title }}</h1>
            <p class="message">{{ $message }}</p>
            <a href="{{ url('/') }}">Volver al inicio</a>
        </main>
    </body>
</html>
