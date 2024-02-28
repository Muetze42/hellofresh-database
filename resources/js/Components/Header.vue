<script setup>
import Filter from '@/Components/Filter.vue'
</script>
<template>
  <header class="nav-element border-b fixed py-2">
    <div class="container flex flex-wrap justify-between">
      <nav>
        <Link :href="filterLink(country.route)" :class="{ active: $page.url === country.route }">
          {{ __('Recipes') }}
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
