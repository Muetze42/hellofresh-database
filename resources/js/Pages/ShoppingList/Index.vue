<script setup>
import { ref, onMounted, computed } from 'vue'
import { TransitionRoot } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3'
import { __ } from '@/mixins.js'
import {
  data_get,
  data_set,
  is_numeric
} from '@norman-huth/helpers-collection-js/helpers/helpers.js'

const page = usePage()
const shoppingList = ref(null)
const view = ref('')
if (
  typeof localStorage.getItem('shoppingListView') == 'string' &&
  ['A', 'B', 'C'].includes(localStorage.getItem('shoppingListView'))
) {
  view.value = localStorage.getItem('shoppingListView').toUpperCase()
} else {
  view.value = 'A'
}

const form = ref(null)
const recipes = ref({})
const ingredients = ref({})
const processing = ref(true)

function setView(value) {
  view.value = value.toUpperCase()
  localStorage.setItem('shoppingListView', view.value)
}

function amountUnitFormat(amount, unit) {
  let result = ''
  if (amount && is_numeric(amount) && amount > 0) {
    let suffix = ''

    if (unit === 'g' && amount >= 1000) {
      unit = 'kg'
      amount = amount / 1000
    } else if (unit === 'ml' && amount >= 1000) {
      unit = 'l'
      amount = amount / 1000
    }

    let split = amount.toString().split('.')
    if (split[1]) {
      if (split[1] === '1') {
        suffix = '⅒'
        amount = amount - 0.1
      } else if (split[1] === '2') {
        suffix = '⅕'
        amount = amount - 0.2
      } else if (split[1] === '125') {
        suffix = '⅛'
        amount = amount - 0.125
      } else if (split[1] === '25') {
        suffix = '¼'
        amount = amount - 0.25
      } else if (split[1] === '5') {
        suffix = '½'
        amount = amount - 0.5
      } else if (split[1] === '75') {
        suffix = '¾'
        amount = amount - 0.75
      }
    }

    amount = new Intl.NumberFormat(page.props.locale + '-' + page.props.country.code).format(amount)

    if (amount > 0) {
      result += amount
    }

    result += suffix + ' '
  }

  return result + unit
}

function remove(id) {
  if (!confirm(__('Are you sure?'))) {
    return
  }
  // noinspection JSUnresolvedReference
  shoppingList.value = shoppingList.value.filter((item) => item !== id)
  localStorage.setItem('shoppingList' + page.props.country.code, JSON.stringify(shoppingList.value))
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

const calculated = computed(() => {
  if (!ingredients.value || !form.value) {
    return null
  }

  let collection = {}

  for (const [ingredientName, ingredient] of Object.entries(ingredients.value)) {
    for (const [recipeId, item] of Object.entries(ingredient.recipe_yields)) {
      let unit = item[form.value[recipeId]].unit
      let itemValue = data_get(collection, ingredient.id + '.' + unit, 0)
      let value = item[form.value[recipeId]].amount
      if (value && is_numeric(value)) {
        itemValue += value
      }
      data_set(collection, ingredient.id + '.' + unit, itemValue)
    }
  }
  let merged = {}
  for (const [ingredientId, calculatedData] of Object.entries(collection)) {
    merged[ingredientId] = ''
    // noinspection JSCheckFunctionSignatures
    for (let [unit, value] of Object.entries(calculatedData)) {
      if (merged[ingredientId].length) {
        merged[ingredientId] += ' + '
      }
      merged[ingredientId] += value > 0 ? amountUnitFormat(value, unit) : unit
    }
  }

  return merged
})

onMounted(() => {
  shoppingList.value = JSON.parse(
    localStorage.getItem('shoppingList' + page.props.country.code) || '[]'
  )
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
      :show="!processing && !Object.keys(recipes).length"
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
      :show="!processing && Object.keys(recipes).length > 0"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card mb-4">
        <h2 class="font-medium p-4 border-b border-primary-600/90 rounded-sm print:px-2 print:py-1">
          {{ __('Recipes') }}
        </h2>
        <div class="p-2 flex flex-col gap-2">
          <div
            v-for="(recipe, recipeId) in recipes"
            :key="recipeId"
            class="border border-primary-600/90 p-1 flex flex-wrap gap-1 max-sm:flex-col"
          >
            <div class="text-center mx-auto p-1">
              <img :src="recipe.image" :alt="recipe.name" class="h-16" />
            </div>
            <div class="p-1 grow max-sm:text-center">
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
        <div class="flex justify-between gap-2 border-b border-primary-600/90 rounded-sm p-4 print:px-2 print:py-1 items-center">
          <h2 class="font-medium">
            {{ __('Ingredients') }}
          </h2>
          <div class="print:hidden text-right btn-group">
            {{ __('View') }}:
            <button
              v-for="viewOption in ['A', 'B', 'C']"
              :key="viewOption"
              type="button"
              class="btn"
              :class="{ 'btn-disabled': view === viewOption }"
              :disabled="view === viewOption"
              @click="setView(viewOption)"
            >
              {{ viewOption }}
            </button>
          </div>
        </div>
        <div class="p-2 flex flex-col gap-2 w-full max-w-xl mx-auto">
          <table
            v-for="(ingredient, ingredientName) in ingredients"
            :key="ingredientName"
            class="table-auto border-collapse border border-primary-600/90 rounded-sm bg-primary-500"
          >
            <thead>
              <tr>
                <th colspan="2" class="text-left px-2 py-0.5 inline-flex items-center gap-2">
                  <img :src="ingredient.image" :alt="ingredient.name" class="h-10 print:h-6" />
                  {{ ingredientName }}
                </th>
              </tr>
            </thead>
            <tbody :class="{ hidden: view === 'B' }">
              <tr
                v-for="(item, recipeId) in ingredient.recipe_yields"
                :key="ingredientName + recipeId"
              >
                <td class="px-2 border border-primary-600/90 border-r-0 bg-primary-400">
                  {{ recipes[recipeId].name }} ({{ form[recipeId] }})
                </td>
                <td class="px-2 text-right border border-primary-600/90 border-l-0 bg-primary-400">
                  {{ amountUnitFormat(item[form[recipeId]].amount, item[form[recipeId]].unit) }}
                </td>
              </tr>
            </tbody>
            <tfoot :class="{ hidden: view === 'C' }">
              <tr>
                <td colspan="2" class="px-2 text-right font-medium">
                  {{ calculated[ingredient.id] }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </section>
    </TransitionRoot>
  </FullPage>
</template>
