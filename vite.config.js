import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/dist',
        emptyOutDir: true,
    },
    optimizeDeps: {
        exclude: [
            'public/assets/vendor_components/zingchart_branded_version/modules/zingchart-maps-mda.min.js'
        ],
    },
});
