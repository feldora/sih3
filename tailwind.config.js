import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

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
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: [
            {
                hidroinfo: {
                    "primary": "#0077b6",    // biru laut
                    "secondary": "#90be6d",  // hijau daun
                    "accent": "#fefae0",     // krem pasir
                    "neutral": "#2b2d42",    // abu gelap
                    "base-100": "#f8f9fa",   // dasar putih abu
                    "info": "#48cae4",       // cyan
                    "success": "#06d6a0",    // tosca
                    "warning": "#ffb703",    // oranye terang
                    "error": "#ef476f",      // merah muda terang
                },
            },
        ],
    },
};
