import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/buscarPersona.js',
                'resources/js/crearOrdenes.js',
                'resources/js/obtenerStaticos.js',

                ],
            refresh: true,
        }),
    ],
});
