import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

import FullPage from '@/Layouts/FullPage.vue'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component('Link', Link)
      .component('FullPage', FullPage)
      .mount(el)
  }
})

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
//       .component('Link', Link)
//       .mixin({
//         methods: {
//           __(key, replace = {}) {
//             let translations = this.$page.props.translations
//             if (translations && translations[key]) {
//               key = translations[key]
//             }
//             Object.keys(replace).forEach(function (search) {
//               key = key.replace(':' + search, replace[search])
//             })
//             return key
//           }
//         }
//       })
//       .mount(el)
//   }
// })
