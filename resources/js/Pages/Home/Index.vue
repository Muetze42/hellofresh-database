<script setup>
/**
 * @property countries
 * @property countries.countries
 * @property countries.countries.country
 * @property countries.countries.code
 * @property countries.countries.locales
 * @property countries.countries.locales.locale
 * @property countries.countries.locales.lang
 */
defineProps({
  countries: {
    type: Object,
    required: true
  }
})
</script>
<template>
  <div class="flex flex-wrap justify-center gap-4 pt-4 container text-center">
    <h1 class="sr-only">Countries</h1>
    <h2 class="w-full text-lg">Choose a country and language</h2>
    <CountrySelect />
  </div>
</template>
