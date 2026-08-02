import { defineConfig, loadEnv } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
    // Nạp các biến môi trường từ file .env
    const env = loadEnv(mode, process.cwd(), '');

    return {
        root: 'resources/react',
        plugins: [
            tailwindcss(),
            react(),
        ],
        build: {
            outDir: '../../dist',
            emptyOutDir: true,
        },
        define: {
            'import.meta.env.VITE_API_URL': JSON.stringify(env.VITE_API_URL || ''),
        },
    };
});
