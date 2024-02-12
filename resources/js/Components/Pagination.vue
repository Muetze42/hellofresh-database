<script setup>
/**
 * @property meta
 * @property meta.current_page
 * @property meta.from
 * @property meta.last_page
 * @property meta.links
 * @property meta.links.url
 * @property meta.links.label
 * @property meta.links.active
 */
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
