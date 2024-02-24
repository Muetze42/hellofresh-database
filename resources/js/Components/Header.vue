<script setup>
import { ref } from 'vue'
import Filter from '@/Components/Filter.vue'

const open = ref(false)

const openFilter = () => {
  open.value = true
}
const closeFilter = () => {
  open.value = false
}
</script>
<template>
  <header class="nav-element border-b fixed py-2">
    <div class="container flex flex-wrap justify-between">
      <ul>
        <li>nav</li>
      </ul>
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
