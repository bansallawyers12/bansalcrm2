import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',  // Bootstrap CSS in <head> (JS stays in app.js)
                'resources/js/jquery-init.js',  // Load jQuery first
                'resources/js/fullcalendar-init.js',  // Load FullCalendar v6
                'resources/js/vendor-libs.js',  // Third-party libraries (flatpickr, izitoast)
                'resources/js/ui-libs.js',  // UI libraries (feather-icons, jquery.nicescroll)
                'resources/js/legacy-init.js',  // Legacy initialization (waits for vendor libs)
                'resources/js/app.js',
                'resources/js/pages/admin/account.js',
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

