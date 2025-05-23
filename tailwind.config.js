import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#088eaf',
                primaryContrast: '#e1f3f8',
                secondary: '#d09ab6',
                secondaryContrast: '#f8dfEc',
                accent: '#d96383',
                accentContrast: '#ffdee7',
                primaryDark: '#50d5f7',
                primaryDarkContrast:'#0a171a',
                secondaryDark: '#652f4b',
                secondaryDarkContrast: '#1c0d15',
                accentDark: '#9c2645',
                accentDarkContrast: '#280911',
                
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
