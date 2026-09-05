import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                vintage: {
                    50: '#FDFBF7',
                    100: '#F5EFEB',
                    500: '#8B5A2B',
                    700: '#6F4E37',
                    800: '#5a3f2c',
                    900: '#3D2817',
                },
                neon: {
                    amber: '#F59E0B',
                    glowamber: '#FBBF24',
                    cyan: '#06B6D4',
                    glowcyan: '#22D3EE',
                    emerald: '#10B981',
                }
            },
            boxShadow: {
                'neon-cyan': '0 0 15px -2px rgba(6, 182, 212, 0.45)',
                'neon-amber': '0 0 15px -2px rgba(245, 158, 11, 0.45)',
                'neon-sm': '0 0 8px 0 rgba(6, 182, 212, 0.35)',
            }
        },
    },

    plugins: [forms],
};
