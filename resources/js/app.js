import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faScroll,
  faFileLines,
  faLemon,
  faCircleCheck,
  faAngleRight,
  faBars,
  faXmark
} from '@fortawesome/free-solid-svg-icons'
library.add(faScroll, faFileLines, faLemon, faCircleCheck, faAngleRight, faBars, faXmark)
import { faCopyright } from '@fortawesome/free-regular-svg-icons'
library.add(faCopyright)

import CountrySelect from '@/Components/CountrySelect.vue'
import ErrorModal from '@/Components/Modals/ErrorModal.vue'
import FullPage from '@/Layout/FullPage.vue'
import Loading from '@/Components/Loading.vue'
import Modal from '@/Components/Modals/Modal.vue'
import NotFound from '@/Components/NotFound.vue'
import Pagination from '@/Components/Pagination.vue'
import Sidebar from '@/Components/Sidebar.vue'

import * as Sentry from '@sentry/vue'

import { __ } from '@/mixins.js'

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
          },
          filterKey() {
            return this.$page.props.filterKey
          }
        },
        methods: {
          __(key, replace = {}) {
            return __(key, replace)
          },
          filterLink(link) {
            if (this.filterKey) {
              link = link + '?filter=' + this.filterKey
            }

            return link
          }
        }
      })
      .component('FontAwesomeIcon', FontAwesomeIcon)
      .component('Link', Link)
      .component('CountrySelect', CountrySelect)
      .component('ErrorModal', ErrorModal)
      .component('FullPage', FullPage)
      .component('Loading', Loading)
      .component('Modal', Modal)
      .component('NotFound', NotFound)
      .component('Pagination', Pagination)
      .component('Sidebar', Sidebar)
      .mount(el)
  }
})
