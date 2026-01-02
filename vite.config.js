import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/jquery-init.js',  // Load jQuery first
                'resources/js/fullcalendar-init.js',  // Load FullCalendar v6
                'resources/js/vendor-libs.js',  // Third-party libraries (flatpickr, select2, datatables, izitoast, intl-tel-input)
                'resources/js/ui-libs.js',  // UI libraries (feather-icons, jquery.nicescroll)
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '127.0.0.1',  // Force IPv4 to prevent IPv6 binding issues with CSP
        port: 5173,
    },
});

