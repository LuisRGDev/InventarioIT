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
            colors: {
                middleby: {
                    50: '#EBF4FC',
                    100: '#D3E7F8',
                    200: '#AED2F2',
                    300: '#7BB7E7',
                    400: '#4A98DA',
                    500: '#2A7CC8',
                    600: '#1D62A8',
                    700: '#004A87', // Brand Primary Blue
                    800: '#003366', // Classic Middleby Navy
                    900: '#05274D', // Deep Slate Corporate
                    950: '#03172E',
                },
                amber: { 
                    50: '#FFF9ED',
                    100: '#FEF0D5',
                    200: '#FDE0AA',
                    300: '#FBCA74',
                    400: '#F8AD37',
                    500: '#F58D14',
                    600: '#E56D07', // Middleby Warm Accent
                    700: '#B9500A',
                    800: '#944111',
                    900: '#7A3713',
                }
            },
            fontFamily: {
                sans: ['Outfit', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'glass': '0 8px 32px 0 rgba(0, 51, 102, 0.07)',
                'premium': '0 10px 25px -5px rgba(0, 51, 102, 0.1), 0 8px 10px -6px rgba(0, 51, 102, 0.05)',
            }
        },
    },

    plugins: [forms],
};
