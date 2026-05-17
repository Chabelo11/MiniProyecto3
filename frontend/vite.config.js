import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  // Para GitHub Pages: cambia 'agenda-app' por tu repo name
  base: '/MiniProyecto3/',
  build: {
    outDir: 'dist'
  }
})
