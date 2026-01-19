import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/images.js',

                // 'resources/core/js/core/app-menu.js',
                // 'resources/core/js/core/app.js',
                // 'resources/core/js/scripts/components.js',
                // 'resources/core/js/scripts/ui/data-list-view.js',
                // 'resources/core/css/core/app-menu.css',
                // 'resources/core/css/core/app.css',
                // 'resources/core/css/scripts/components.css',
                // 'resources/core/css/scripts/ui/data-list-view.css',
            ],
            refresh: true,
        }),
    ],
});
