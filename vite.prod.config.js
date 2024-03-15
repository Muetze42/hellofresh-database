import { sentryVitePlugin } from '@sentry/vite-plugin'
import { defineConfig, splitVendorChunkPlugin, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'

const env = loadEnv('all', process.cwd())

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
      authToken: env.VITE_SENTRY_AUTH_TOKEN.trim()
    })
  ],

  resolve: {
    alias: {
      '@': resolve('./resources/js'),
      '@assets': resolve('./resources/assets')
    }
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return id.toString().split('node_modules/')[1].split('/')[0].toString()
          }
        }
      }
    },
    sourcemap: true
  }
})
