import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Prompt', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                badminton: {
                    lime: '#22c55e',
                    neon: '#10b981',
                    court: '#064e3b',
                    slate: '#0f172a',
                    card: '#1e293b',
                    cardlight: '#334155',
                    accent: '#38bdf8',
                }
            }
        },
    },
    plugins: [forms],
};
