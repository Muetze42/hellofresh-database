import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
              'resources/css/portal/app.css',
              'resources/js/portal/app.js'
            ],
            buildDirectory: 'build-portal',
            hotFile: 'public/hot-portal',
            refresh: true,
        }),
        tailwindcss(),
    ],
});
