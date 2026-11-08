/// <reference types="vitest/config" />
import { fileURLToPath, URL } from 'node:url';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import { defineConfig } from 'vite';

const target = process.env.VITE_PROXY_TARGET ?? 'http://localhost:8000';

export default defineConfig(({ command }) => ({
  // em produção o Laravel serve a SPA a partir de public/spa
  base: command === 'build' ? '/spa/' : '/',
  plugins: [react(), tailwindcss()],
  resolve: { alias: { '@': fileURLToPath(new URL('./src', import.meta.url)) } },
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    proxy: {
      '/api': target,
      '/sanctum': target,
      '/up': target,
      // painel /admin: o Filament e o Livewire ficam no Laravel
      '^/(admin|filament|livewire-|css/|fonts/|js/filament)': { target, changeOrigin: false },
    },
  },
  build: { outDir: '../backend/public/spa', emptyOutDir: true },
  test: {
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.ts'],
    // datas e horas dos testes no fuso do câmpus
    env: { TZ: 'America/Sao_Paulo' },
  },
}));
