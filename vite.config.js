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
                'resources/core/vendors/css/tables/datatable/datatables.min.css',
                'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
                'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
                'resources/core/css-rtl/core/colors/palette-gradient.css',
                'resources/core/css-rtl/plugins/file-uploaders/dropzone.css',
                'resources/core/css-rtl/pages/data-list-view.css',
                'resources/core/css-rtl/custom-rtl.css',
                'resources/core/vendors/css/file-uploaders/dropzone.min.css',
                'resources/js/pages/AddNewOrder.js',
                'resources/js/pages/PrinterLog.js',
                'resources/js/pages/trash.js',
                'resources/js/pages/dashboard.js',
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
