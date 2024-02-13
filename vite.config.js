import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/scss/app.scss', 'resources/js/app.js'],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  resolve: {
    alias: {
      '@': resolve('./resources/js')
    }
  },

  build: {
    sourcemap: true
  }
})
