import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import path from 'path'; // Jika belum mengimpor path

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/mod.css',
                'resources/js/app.js',
                'resources/css/pages/geospasial-map.css',
                'resources/js/pages/geospasial-map.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        chunkSizeWarningLimit: 1600, // Menyesuaikan ukuran chunk warning
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/tinymce')) {
                        return 'tinymce'; // Pisahkan TinyMCE menjadi chunk terpisah
                    }
                    if (id.includes('node_modules/daisyui')) {
                        return 'daisyui'; // Pisahkan daisyUI menjadi chunk terpisah
                    }
                }
            }
        }
    },
});
