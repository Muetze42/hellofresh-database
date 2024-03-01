<script setup>
import Filter from '@/Components/Filter.vue'
import { ref } from 'vue'

const showCountrySelect = ref(false)
</script>
<template>
  <header class="nav-element border-b fixed py-2">
    <div class="container flex flex-wrap gap-2 justify-between items-center">
      <nav class="flex gap-2">
        <button
          type="button"
          title="Choose a country and language"
          class="btn px-1"
          @click="showCountrySelect = true"
        >
          <span class="fi rounded-sm" :class="'fi-' + country.code.toLowerCase()" />
        </button>
        <Link :href="filterLink(country.route)">
          {{ __('Recipes') }}
        </Link>
        <Link :href="filterLink(country.route + '/menus')">
          {{ __('Menus') }}
        </Link>
      </nav>
      <div>
        <Filter />
      </div>
    </div>
  </header>
  <Modal
    :show="showCountrySelect"
    title="Choose a country and language"
    size="max-w-2xl"
    @close="showCountrySelect = false"
  >
    <div
      class="flex flex-wrap justify-center gap-4 p-4 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full"
    >
      <CountrySelect />
    </div>
  </Modal>
</template>
