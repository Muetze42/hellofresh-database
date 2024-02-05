/* Pseudo file for IDE */
import Vue from 'vue'
import { Link } from '@inertiajs/vue3'

/**
 * @property {Object} user
 * @property {String} user.email
 * @property {String} user.name
 * */
Vue.component('Link', Link)
Vue.mixin({
  methods: {
    __(key, replace = {}) {
      return __(key, replace)
    }
  }
})
