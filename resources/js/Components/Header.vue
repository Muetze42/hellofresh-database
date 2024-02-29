<script setup>
import Filter from '@/Components/Filter.vue'
</script>
<template>
  <header class="nav-element border-b fixed py-2">
    <div class="container flex flex-wrap justify-between">
      <nav class="flex gap-2">
        <Link :href="filterLink(country.route)">
          {{ __('Recipes') }}
        </Link>
        <Link
          :href="filterLink(country.route + '/menus')"
        >
          {{ __('Menus') }}
        </Link>
      </nav>
      <div>
        <Filter v-if="filterable" />
      </div>
    </div>
  </header>
</template>
<script>
export default {
  props: {
    filterable: {
      type: Boolean,
      required: false,
      default: false
    }
  }
}
</script>
