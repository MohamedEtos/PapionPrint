import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    define: {
        global: 'globalThis',
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/images.js',
                'resources/js/scripts.js',

                // Core JS files as separate entry points
                'resources/js/app-menu-wrapper.js',
                'resources/core/js/core/app.js',
                'resources/core/js/scripts/components.js',
                'resources/core/js/scripts/pages/dashboard-ecommerce.js',

                // Core CSS files
                'resources/core/css/components.css',
                'resources/core/css/pages/dashboard-ecommerce.css',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jquery',
            'jQuery': 'jquery',
        },
    },
});
