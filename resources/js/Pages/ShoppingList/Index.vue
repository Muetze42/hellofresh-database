<script setup>
import { ref, onMounted } from 'vue'
import { TransitionRoot } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3'
import { __ } from '@/mixins.js'

const page = usePage()

const shoppingList = ref(JSON.parse(localStorage.getItem('shoppingList') || '[]'))
const form = ref(null)
const recipes = ref({})
const ingredients = ref({})
const processing = ref(true)

function remove(id) {
  if (!confirm(__('Are you sure?'))) {
    return
  }
  // noinspection JSUnresolvedReference
  shoppingList.value = shoppingList.value.filter((item) => item !== id)
  localStorage.setItem('shoppingList', JSON.stringify(shoppingList.value))
  processing.value = true
  updateData()
}

function setYields(recipe, yields) {
  let updatedForm = form.value
  updatedForm[recipe] = yields
  form.value = updatedForm
  localStorage.setItem('shoppingListForm', JSON.stringify(form.value))
}

async function updateData() {
  if (!shoppingList.value.length) {
    processing.value = false
    return
  }
  await axios
    .post(page.props.country.route + '/shopping-list', {
      recipes: shoppingList.value,
      form: JSON.parse(localStorage.getItem('shoppingListForm') || '[]')
    })
    .then((response) => {
      recipes.value = response.data.recipes
      ingredients.value = response.data.ingredients
      form.value = response.data.form
      processing.value = false
    })
}

function isFloat(n) {
  return n === +n && n !== (n|0);
}

function isInteger(n) {
  return n === +n && n === (n|0);
}

function isNumeric(n) {
  return isFloat(n) || isInteger(n)
}

onMounted(() => {
  updateData()
})
</script>

<template>
  <h1 class="sr-only">{{ __('shopping list') }}</h1>
  <FullPage>
    <TransitionRoot
      :show="processing"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card p-4">
        <Loading class="h-20 mx-auto" />
      </section>
    </TransitionRoot>
    <TransitionRoot
      :show="!processing && !shoppingList.length"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card text-center p-4 font-medium">
        {{ __("You don't have any recipes on your :list.", { list: __('shopping list') }) }}
      </section>
    </TransitionRoot>

    <TransitionRoot
      :show="!processing && shoppingList.length > 0"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card mb-4">
        <h2 class="font-medium p-4 border-b border-primary-600/90 rounded-sm">
          {{ __('Recipes') }}
        </h2>
        <div class="p-2 flex flex-col gap-2">
          <div
            v-for="(recipe, recipeId) in recipes"
            :key="recipeId"
            class="border border-primary-600/90 p-1 flex flex-wrap gap-2"
          >
            <img :src="recipe.image" :alt="recipe.name" class="h-16 m-1 mx-auto" />
            <div class="p-1 grow">
              <h3 class="font-medium">{{ recipe.name }}</h3>
              <p class="max-sm:hidden">{{ recipe.headline }}</p>
              <div class="text-center btn-group">
                <button
                  v-for="option in recipe.options"
                  :key="recipe.id + option"
                  :value="option"
                  class="btn"
                  :class="{ 'btn-disabled': form[recipeId] === option }"
                  :disabled="form[recipeId] === option"
                  @click="setYields(recipeId, option)"
                >
                  {{ option }}
                </button>
              </div>
            </div>
            <div class="mx-auto flex items-end">
              <button type="button" class="btn btn-danger mx-auto" @click="remove(recipeId)">
                {{ __('Removed from the :list', { list: __('shopping list') }) }}
              </button>
            </div>
          </div>
        </div>
      </section>
      <section class="card">
        <h2 class="font-medium p-4 border-b border-primary-600/90 rounded-sm">
          {{ __('Ingredients') }}
        </h2>
        <div class="p-2 flex flex-col gap-2 w-full max-w-xl mx-auto">
          <table
            v-for="(ingredient, ingredientName) in ingredients"
            :key="ingredientName"
            class="table-auto border-collapse border border-primary-600/90 rounded-sm"
          >
            <thead>
              <tr>
                <th colspan="2" class="text-left p-2 inline-flex items-center gap-2">
                  <img :src="ingredient.image" :alt="ingredient.name" class="h-12" />
                  {{ ingredientName }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, recipeId) in ingredient.recipe_yields"
                :key="ingredientName + recipeId"
              >
                <td class="px-2 border border-primary-600/90 border-r-0">
                  {{ recipes[recipeId].name }} ({{ form[recipeId] }})
                </td>
                <td class="px-2 text-right border border-primary-600/90 border-l-0 font-medium">
                  {{
                    isNumeric(item[form[recipeId]].amount)
                      ? new Intl.NumberFormat($page.props.locale + '-' + country.code).format(
                          item[form[recipeId]].amount
                        )
                      : item[form[recipeId]].amount
                  }}
                  {{ item[form[recipeId]].unit }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </TransitionRoot>
  </FullPage>
</template>
