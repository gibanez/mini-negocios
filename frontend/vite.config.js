import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: { '@': resolve(__dirname, 'src') }
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:9980',
        changeOrigin: true
      }
    },
    historyApiFallback: {
      rewrites: [{ from: /^\/app\/.*$/, to: '/app/index.html' }]
    }
  },
  build: {
    outDir: '../public',
    emptyOutDir: false
  },
  base: '/'
})
