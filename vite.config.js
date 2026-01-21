import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: true,
        // Allow overriding port via env and fallback automatically if busy
        port: Number(process.env.VITE_PORT || 5173),
        strictPort: false,
        hmr: {
            host: process.env.VITE_HMR_HOST || '127.0.0.1',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
