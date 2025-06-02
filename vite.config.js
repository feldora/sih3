import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/mod.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    // resolve: {
    //     alias: {
    //         '~': path.join(__dirname, '/node_modules/'),
    //     }
    // },
    // build: {
    //     chunkSizeWarningLimit: 1600,
    // },

});
