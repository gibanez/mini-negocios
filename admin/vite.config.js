import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  base: '/admin/',
  resolve: { alias: { '@': resolve(__dirname, 'src') } },
  build: { outDir: '../public/admin', emptyOutDir: true },
  server: {
    port: 5174,
    proxy: { '/api': { target: 'http://localhost:9980', changeOrigin: true } }
  }
})
