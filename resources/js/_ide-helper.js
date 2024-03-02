/* Pseudo file for IDE */
import Vue from 'vue'
import { Link } from '@inertiajs/vue3'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import config from './../../data/config.json'

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
      return __(key, replace = {})
    },
    filterLink(link) {
      return link
    }
  },
  data() {
    return {
      country: {
        code: 'String',
        domain: 'String',
        data: { prepMin: 4, prepMax: 200 },
        route: 'String'
      },
      config: config,
      filterKey: 'String'
    }
  }
})
