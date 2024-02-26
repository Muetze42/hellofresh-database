<template>
  <FullPage :filterable="true">
    <h1 class="sr-only">{{ __('Recipes') }}</h1>
    <div class="flex flex-wrap justify-center items-stretch gap-8">
      <div
        v-for="recipe in recipes.data"
        :key="recipe.id"
        class="card w-full sm:w-96 2xl:w-[29.3rem]"
      >
        <Link v-if="recipe.image" :href="country.route + '/' + recipe.id + '-' + recipe.slug">
          <div
            v-if="recipe.label"
            :style="{ color: recipe.label.color, backgroundColor: recipe.label.bg }"
            class="absolute py-0.5 px-1.5 rounded-br font-medium"
          >
            {{ recipe.label.text }}
          </div>
          <img :src="recipe.image" :alt="recipe.name" class="w-full" />
        </Link>
        <div class="py-2 flex flex-col h-full gap-2">
          <h2 class="px-2 font-medium text-lg">
            {{ recipe.name }}
          </h2>
          <div class="px-2 grow text-gray-300 leading-tight pb-1 flex flex-col">
            {{ recipe.headline }}
            <ul
              v-if="recipe.tags.length"
              class="text-sm inline-flex flex-wrap text-gray-300/90 dotted-list pt-4"
            >
              <li v-for="tag in recipe.tags" :key="tag">{{ tag }}</li>
            </ul>
          </div>
          <div class="px-2 flex flex-wrap gap-2 justify-center">
            <a
              class="btn whitespace-nowrap btn-sm"
              target="_blank"
              :href="country.domain + '/recipes/' + recipe.slug + '-' + recipe.id"
            >
              <font-awesome-icon :icon="['fas', 'lemon']" fixed-width />
              {{ __('View on HelloFresh') }}
            </a>
            <Component
              :is="recipe.pdf ? 'a' : 'span'"
              :aria-disabled="!recipe.pdf"
              :class="{ 'btn-disabled': !recipe.pdf }"
              :href="recipe.pdf"
              class="btn whitespace-nowrap btn-sm"
              target="_blank"
            >
              <font-awesome-icon :icon="['fas', 'file-lines']" fixed-width />
              {{ __('View PDF') }}
            </Component>
          </div>
          <div class="text-center">
            <Link :href="country.route + '/' + recipe.id + '-' + recipe.slug" class="btn">
              <font-awesome-icon :icon="['fas', 'scroll']" fixed-width />
              {{ __('Details') }}
            </Link>
          </div>
        </div>
      </div>
    </div>
    <Pagination :links="recipes.meta.links" class="mt-8" />
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
export default {
  props: {
    recipes: {
      type: Object,
      required: true
    }
  }
}
</script>
