import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faScroll, faFileLines, faLemon, faCircleCheck } from '@fortawesome/free-solid-svg-icons'
library.add(faScroll, faFileLines, faLemon, faCircleCheck)

import FullPage from '@/Layouts/FullPage.vue'
import Loading from '@/Components/Loading.vue'
import Pagination from '@/Components/Pagination.vue'
import * as Sentry from '@sentry/vue'

// noinspection JSIgnoredPromiseFromCall
createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    Sentry.init({
      app,
      dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC,
      tunnel: '/api/sentry-tunnel',
      trackComponents: true,
      logErrors: true
    })

    app
      .use(plugin)
      .mixin({
        computed: {
          country() {
            return this.$page.props.country
          },
          config() {
            return this.$page.props.config
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
      .component('Loading', Loading)
      .component('Pagination', Pagination)
      .component('FontAwesomeIcon', FontAwesomeIcon)
      .mount(el)
  }
})
