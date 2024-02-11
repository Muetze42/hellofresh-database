import './bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faScroll, faFileLines, faLemon } from '@fortawesome/free-solid-svg-icons'
library.add(faScroll, faFileLines, faLemon)

import FullPage from '@/Layouts/FullPage.vue'

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
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
      .component('FontAwesomeIcon', FontAwesomeIcon)
      .mount(el)
  }
})

