import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faScroll, faFileLines, faLemon } from '@fortawesome/free-solid-svg-icons'
library.add(faScroll, faFileLines, faLemon)

import FullPage from '@/Layouts/FullPage.vue'
import Pagination from '@/Components/Pagination.vue'
import * as Sentry from '@sentry/vue'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    Sentry.init({
      app,
      dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC
    })

    app
      .use(plugin)
      .mixin({
        computed: {
          country() {
            return this.$page.props.country
          }
        },
        methods: {
          __(key, replace = {}) {
            let translations = this.$page.props.translations
            if (translations && translations[key]) {
              key = translations[key]
            }
            Object.keys(replace).forEach(function (search) {
              key = key.replace(':' + search, replace[search])
            })
            return key
          }
        }
      })
      .component('Link', Link)
      .component('FullPage', FullPage)
      .component('Pagination', Pagination)
      .component('FontAwesomeIcon', FontAwesomeIcon)
      .mount(el)
  }
})
