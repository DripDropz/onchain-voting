const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    safelist: [
        'bg-gray-500/20',
        'bg-emerald-500/20',
        'bg-emerald-500',
        'bg-indigo-500/20',
        'bg-indigo-500',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            width: {
                '128': '32rem',
            },
            screens: {
                '3xl': '1840px',
                '4xl': '2160px'
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
