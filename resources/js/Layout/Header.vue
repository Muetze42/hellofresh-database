<script setup>
import Filter from '@/Components/Filter.vue'
import { ref } from 'vue'
import { DialogTitle } from '@headlessui/vue'

const showCountries = ref(false)
const open = ref(false)
const links = {
  Recipes: '',
  Menus: '/menus'
}
</script>
<template>
  <header class="nav-element border-b fixed py-2">
    <div class="container flex flex-wrap gap-2 justify-between items-center">
      <nav class="flex gap-2 items-center">
        <button
          type="button"
          title="Choose a country and language"
          class="btn px-1 h-8 w-8"
          @click="showCountries = true"
        >
          <span class="fi rounded-sm" :class="'fi-' + country.code.toLowerCase()" />
        </button>
        <Link
          v-for="(link, label) in links"
          :key="label"
          :href="filterLink(country.route + link)"
          class="max-md:hidden"
        >
          {{ __(label) }}
        </Link>
        <button type="button" class="btn md:hidden h-8 w-8" @click="open = true">
          <font-awesome-icon :icon="['fas', 'bars']" />
        </button>
      </nav>
      <div>
        <Filter />
      </div>
    </div>
  </header>
  <Sidebar :show="open" :is-left="true" @close="open = false">
    <DialogTitle class="w-full text-right p-2">
      <button
        type="button"
        class="btn h-8 w-8"
        :aria-label="__('Close menu')"
        @click="open = false"
      >
        <font-awesome-icon :icon="['fas', 'xmark']" />
      </button>
    </DialogTitle>
    <nav
      class="mobile flex flex-col gap-2 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full p-2"
    >
      <Link v-for="(link, label) in links" :key="label" :href="filterLink(country.route + link)">
        {{ __(label) }}
      </Link>
    </nav>
  </Sidebar>
  <Modal
    :show="showCountries"
    title="Choose a country and language"
    size="max-w-2xl"
    @close="showCountries = false"
  >
    <div
      class="flex flex-wrap justify-center gap-4 p-4 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full"
    >
      <CountrySelect />
    </div>
  </Modal>
</template>
