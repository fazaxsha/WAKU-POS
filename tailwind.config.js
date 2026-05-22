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
                sans: ['Plus Jakarta Sans', 'sans-serif'],
                mono: ['DM Mono', 'monospace'],
            },
            colors: {
                brand: {
                    primary:    '#0F172A', // Slate 900 — sidebar, headings
                    accent:     '#0D9488', // Teal 600  — highlights, links, CTAs
                    'accent-dark': '#0F766E', // Teal 700 — hover states
                    'accent-light': '#F0FDFA', // Teal 50  — backgrounds
                    background: '#F4F6F8', // Cool gray — page background
                    surface:    '#FFFFFF', // White     — cards
                    success:    '#22C55E', // Green 500
                }
            }
        },
    },

    plugins: [forms],
};
