import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    // Necesario para HMR cuando Vite corre dentro de Docker
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: { host: 'localhost' },
    },
    plugins: [

        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.jsx',
                'resources/js/appOrden.jsx',
                'resources/js/appPersona.jsx',
                'resources/js/ExamenDetail.jsx',
                'resources/js/components/guest/Typewriter.jsx',
                'resources/js/convenioCreate.jsx'
            ],
            refresh: true,

        }),

    ],
});
