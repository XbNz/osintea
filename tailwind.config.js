import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
        "./vendor/livewire/flux/stubs/**/*.blade.php",
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('tailwind-scrollbar-hide')
    ],
};
