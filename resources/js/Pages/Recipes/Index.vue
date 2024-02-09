<template>
  <FullPage>
    <div class="flex flex-wrap justify-center items-stretch gap-6">
      <div v-for="recipe in recipes.data" :key="recipe.id" class="card w-96">
        <img v-if="recipe.image" :src="recipe.image" :alt="recipe.name" />
        <div class="p-2 font-medium text-lg">
          {{ recipe.name }}
        </div>
      </div>
    </div>
  </FullPage>
</template>

<script>
/**
 * @property recipes
 * @property recipes.links
 * @property recipes.meta
 * @property recipes.data
 * @property recipes.data.id
 * @property recipes.data.name
 * @property recipes.data.slug
 * @property recipes.data.pdf
 * @property recipes.data.headline
 * @property recipes.data.image
 * @property recipes.data.label
 * @property recipes.data.label.text
 * @property recipes.data.label.color
 * @property recipes.data.label.bg
 * @property recipes.data.tags
 * @property recipes.data.tags.name
 * @property recipes.data.tags.color
 */
import FullPage from '@/Layouts/FullPage.vue'

export default {
  components: { FullPage },
  props: {
    recipes: {
      type: Object,
      required: true
    }
  }
}
</script>
