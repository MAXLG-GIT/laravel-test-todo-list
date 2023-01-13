import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jQuery'
        },
    },
    server: {
        host: true,
        hmr: {
            host: 'localhost'
        },
        watch: {
            usePolling: true
        }
    },
    build: {
        minify: true
    },
});
