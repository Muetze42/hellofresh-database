/* Pseudo file for IDE */
import Vue from 'vue'
import { Link } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/**
 * @property {Object} user
 * @property {String} user.email
 * @property {String} user.name
 * */
Vue.component('Link', Link)
Vue.component('FontAwesomeIcon', FontAwesomeIcon)
Vue.mixin({
  methods: {
    __(key, replace = {}) {
      return 'replaced'
    }
  },
  data() {
    return {
      country: {
        code: 'String',
        domain: 'String',
        route: 'String'
      },
      config: {
        filter: {
          max_filterable_items: 20
        }
      }
    }
  }
})
