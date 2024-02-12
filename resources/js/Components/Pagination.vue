<script setup>
defineProps({
  links: {
    type: Array,
    required: true
  }
})
</script>

<template>
  <div v-if="links.length > 3" class="pagination">
    <template v-for="link in links">
      <Component
        :is="link.url && !link.active ? 'Link' : 'span'"
        :key="link.label"
        v-if="link.url || link.label === '...'"
        :href="link.url"
        :class="{ 'btn-disabled': link.active }"
        class="btn"
      >
        {{ link.label }}
      </Component>
    </template>
  </div>
</template>
