import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv';
dotenv.config();

const host = process.env.VITE_HOST || 'localhost';
const port = process.env.VITE_PORT || 5173;

export default defineConfig({
    server: {
        host: host,
        port: port,
        strictPort: true,
        hmr: {
            host: host,
    }
    },
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
