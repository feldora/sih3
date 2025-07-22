import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv';

dotenv.config();

const serverHost = '0.0.0.0';                   // bind di semua interface supaya bisa diakses dari luar container
const hmrHost = process.env.VITE_HOST || 'localhost'; // hostname websocket yang diakses browser
const port = Number(process.env.VITE_PORT) || 5173;

export default defineConfig({
    server: {
        host: serverHost,
        port: port,
        strictPort: true,
        hmr: {
            host: hmrHost,
            protocol: 'ws',
        },
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
        chunkSizeWarningLimit: 1600,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/tinymce')) {
                        return 'tinymce';
                    }
                    if (id.includes('node_modules/daisyui')) {
                        return 'daisyui';
                    }
                },
            },
        },
    },
});
