<script setup>
import { usePage } from '@inertiajs/vue3'

const page = usePage()
/**
 * @property countries
 * @property countries.countries
 * @property countries.countries.country
 * @property countries.countries.code
 * @property countries.countries.locales
 * @property countries.countries.locales.locale
 * @property countries.countries.locales.lang
 */
</script>

<template>
  <div v-for="country in page.props.countries" :key="country.code" class="p-2 card w-72">
    <h3 class="font-medium mb-2 text-lg flex flex-wrap justify-center gap-2 items-center">
      <span class="fi" :class="'fi-' + country.code.toLowerCase()" />
      <span> HelloFresh {{ country.country }} </span>
    </h3>
    <div class="flex flex-wrap gap-2 justify-center pb-1">
      <a
        v-for="locale in country.locales"
        :key="country.code + locale.locale"
        :href="'/' + country.code.toLowerCase() + '-' + locale.locale"
        :hreflang="locale.locale + '-' + country.code"
        class="btn"
      >
        {{ locale.lang }}
      </a>
    </div>
  </div>
</template>
