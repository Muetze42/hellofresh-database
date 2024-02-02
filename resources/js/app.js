import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

/**
 * Without Layout.
 */
createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  }
}).then()

/**
 * With Layout.
 */
// import Layout from './Layout'
//
// createInertiaApp({
//   resolve: (name) => {
//     const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
//     let page = pages[`./Pages/${name}.vue`]
//     page.default.layout = page.default.layout || Layout
//     return page
//   },
//   setup({ el, App, props, plugin }) {
//     createApp({ render: () => h(App, props) })
//       .use(plugin)
//       .mount(el)
//   }
// }).then()
