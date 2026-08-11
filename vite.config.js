import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    server: {
        watch: {
            ignored: ['**/app-modules/**/database/*.mmdb'],
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/chart.js', 'resources/js/map.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
