import { sentryVitePlugin } from '@sentry/vite-plugin'
import { defineConfig, splitVendorChunkPlugin } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'

export default defineConfig({
  plugins: [
    splitVendorChunkPlugin(),
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
    }),
    sentryVitePlugin({
      org: 'norman-huth',
      project: 'hellofresh-database',
      release: {
        name: new Date().getTime()
      },
      authToken: process.env.SENTRY_AUTH_TOKEN
    })
  ],

  resolve: {
    alias: {
      '@': resolve('./resources/js'),
      '@assets': resolve('./resources/assets')
    }
  },
  build: {
    sourcemap: true
  }
})
