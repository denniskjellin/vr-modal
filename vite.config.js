import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  build: {
    manifest: true,
    outDir: 'dist',
    assetsDir: 'assets',
    cssCodeSplit: true,
    minify: false,  // Set minify to false to disable JavaScript minification
    rollupOptions: {
      output: {
        entryFileNames: 'index.js',
        assetFileNames: 'assets/main.css',
      },
    },
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
});
