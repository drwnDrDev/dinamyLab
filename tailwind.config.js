import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
        './resources/js/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                headline: ['Ubuntu', 'sans-serif', 'system-ui'],
                ubuntu: ['Ubuntu', 'sans-serif', 'system-ui'],
                ubuntuBold: ['Ubuntu', 'sans-serif', 'system-ui', 'bold'],
            },
            colors: {
                text: 'rgb(13, 25, 28)',
                background: 'rgb(248, 251, 252)',
                primary: '#0eb4d1',
                secondary: 'rgb(231, 242, 243)',
                titles: 'rgb(75, 143, 155)',
                borders: 'rgb(207, 228, 232)',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic':
                    'conic-gradient(from 180deg at center, var(--tw-gradient-stops))',
                'rips-cargador': "url('./rips-cargador.png')",
            },
        },
    },
    plugins: [forms],
     variants: {
        extend: {
            placeholderShown: ['responsive', 'focus', 'hover'],
        },
    },
};
