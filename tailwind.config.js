import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',

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
                text: '#0d191c',
                background: '#f8fbfc',
                primary: '#0eb4d1',
                secondary: '#e7f2f3',
                titles: '#4b8f9b',
                borders: '#cfe4e8'

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
