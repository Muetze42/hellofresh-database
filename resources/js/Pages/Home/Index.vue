<script setup>
import Flag from 'vue-country-flag-next'
</script>
<template>
  <div class="flex flex-wrap justify-center gap-4 pt-4 container text-center mx-auto">
    <h1 class="sr-only">Countries</h1>
    <h2 class="w-full text-lg">Choose a country and a language</h2>
    <div v-for="country in countries" :key="country.code" class="p-2 card w-72">
      <h3 class="font-medium mb-2 text-lg flex flex-wrap justify-center gap-2 items-center">
        <span>
          <Flag :country="country.code" />
        </span>
        <span> HelloFresh {{ country.country }} </span>
      </h3>
      <div class="flex flex-wrap gap-2 justify-center pb-1">
        <a
          v-for="locale in country.locales"
          :key="country.code + locale.locale"
          :href="'/' + country.code.toLowerCase() + '-' + locale.locale"
          class="btn"
        >
          {{ locale.lang }}
        </a>
      </div>
    </div>
  </div>
</template>
<script>
/**
 * @property countries
 * @property countries.countries
 * @property countries.countries.country
 * @property countries.countries.code
 * @property countries.countries.locales
 * @property countries.countries.locales.locale
 * @property countries.countries.locales.lang
 */
import Layout from '@/Layouts/WithoutHeader.vue'

export default {
  layout: Layout,
  props: {
    countries: {
      type: Object,
      required: true
    }
  }
}
</script>
