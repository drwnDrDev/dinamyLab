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

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                serif: ['DM Serif Display', 'serif'],
                headline: ['Plus Jakarta Sans', 'sans-serif'],
                ubuntu: ['Ubuntu', 'sans-serif', 'system-ui'],
                ubuntuBold: ['Ubuntu', 'sans-serif', 'system-ui', 'bold'],
            },
            colors: {
                text: 'rgb(13, 25, 28)',
                background: 'rgb(238, 245, 248)',
                primary: '#0eb4d1',
                'primary-dark': '#0a8fa8',
                secondary: 'rgb(218, 240, 246)',
                surface: 'rgb(255, 255, 255)',
                titles: 'rgb(75, 143, 155)',
                borders: 'rgb(196, 224, 232)',
                muted: 'rgb(100, 144, 154)',
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
