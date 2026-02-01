import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [

        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/main.js',
                'resources/js/buscarPersona.js',
                'resources/js/crearOrdenes.js',
                'resources/js/obtenerStaticos.js',
                'resources/js/crearPersona.js',
                'resources/js/app.jsx',
                'resources/js/react-app.jsx',
                'resources/js/components/guest/Typewriter.jsx'

            ],
            refresh: true,

        }),

    ],
});
