import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',  // Bootstrap CSS in <head> (JS stays in app.js)
                'resources/js/fullcalendar-init.js',  // Load FullCalendar v6
                'resources/js/vendor-libs.js',  // flatpickr, iziToast, Tom Select, DataTables (CSS in layout head)
                'resources/js/apexcharts-init.js',
                'resources/js/signature-pad-init.js',
                'resources/js/tinymce-init.js',
                'resources/js/legacy-init.js',  // Legacy initialization (waits for vendor libs)
                'resources/js/app.js',
                'resources/js/pages/admin/account.js',
                'resources/js/pages/admin/client-detail-entry.js',
                'resources/js/admin-layout-scripts.js',
                'resources/js/agent-layout-scripts.js',
                'resources/js/adminconsole-layout-scripts.js',
                'resources/js/agent-adminconsole-layout-scripts.js',
                'resources/js/minimal-layout-scripts.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5 SCSS still uses legacy @import and global builtins.
                // Silence until Bootstrap migrates; keeps custom _variables.scss overrides working.
                silenceDeprecations: ['import', 'if-function', 'global-builtin', 'color-functions'],
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            '@legacy': path.resolve(__dirname, 'public/js'),
            jquery: path.resolve(__dirname, 'resources/js/jquery-global-shim.js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined, // Let Vite handle chunking automatically
            },
        },
        commonjsOptions: {
            include: [/node_modules/],
        },
    },
    server: {
        host: '127.0.0.1',  // Force IPv4 to prevent IPv6 binding issues with CSP
        port: 5173,
    },
});

