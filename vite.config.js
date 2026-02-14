import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

import viteCompression from 'vite-plugin-compression';

export default defineConfig({
    define: {
        global: 'globalThis',
    },
    plugins: [
        tailwindcss(),
        viteCompression(),
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
                'resources/core/css-rtl/plugins/forms/wizard.css',
                'resources/core/js/scripts/forms/wizard-steps.js',
                'resources/js/pages/presslist.js',
                'resources/js/pages/rollpress_archive.js',
                'resources/core/vendors/css/forms/select/select2.min.css',
                'resources/core/css-rtl/plugins/forms/validation/form-validation.css',
                'resources/core/vendors/css/pickers/pickadate/pickadate.css',
                'resources/js/pages/invoice_history.js',
                'resources/js/pages/users.js',
                'resources/js/pages/stras.js',
                'resources/js/pages/laser.js',
                'resources/js/pages/tarter.js',
                'resources/js/notifications.js',
                'resources/js/pages/roles.js',
                'resources/js/pages/rollpress_trash.js',
                'resources/js/pages/presslist.js',
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
    build: {
        chunkSizeWarningLimit: 1000,
        modulePreload: false,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('jquery')) {
                            return 'jquery';
                        }
                        if (id.includes('apexcharts')) {
                            return 'apexcharts';
                        }
                        if (id.includes('lodash')) {
                            return 'lodash';
                        }
                        return 'vendor';
                    }
                }
            }
        }
    }
});
