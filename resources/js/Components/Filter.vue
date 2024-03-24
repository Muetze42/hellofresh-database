<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { DialogTitle } from '@headlessui/vue'
import Multiselect from '@/Components/Forms/Multiselect.vue'
import Slider from '@vueform/slider'
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { __ } from '@/mixins.js'
import { optional } from '@norman-huth/helpers-collection-js/helpers/helpers.js'

const showError = ref(false)
const errors = ref(null)

const open = ref(false)
const processing = ref(false)
const page = usePage()
const filter = page.props.filter
const baseUrl = page.props.country.route + '/'

// noinspection JSCheckFunctionSignatures
const form = filter ? useForm(filter) : null

const searchInit = optional(new URLSearchParams(window.location.search).get('search'), '')
const search = reactive({
  value: searchInit
})

function reset() {
  if (confirm(__('Are you sure?'))) {
    router.get(page.url.split('?')[0])
  }
}

function cancel() {
  if (
    (!form.isDirty && searchInit.toLowerCase() === search.value.toLowerCase()) ||
    confirm(__('Do you want to discard the changes?'))
  ) {
    form.reset()
    open.value = false
  }
}

const isActive = computed(() => {
  return page.props.filterKey || new URL(document.location).searchParams.get('search')
})

async function submit() {
  // form.processing = true
  processing.value = true
  await axios
    .post(baseUrl + 'filter', form.data())
    .then((response) => {
      // form.processing = false
      let data = {}
      if (response.data) {
        data.filter = response.data
      }
      if (search.value && search.value.trim().length > 0) {
        data.search = search.value.trim()
      }
      router.get(page.url.split('?')[0], data)
    })
    .catch(function (error) {
      showError.value = true
      errors.value = error
      processing.value = false
    })
}

/**
 * @property {Object} form
 * @property {array} form.allergens_except
 * @property {array} form.difficulties
 * @property {Boolean} form.difficulties.d1
 * @property {Boolean} form.difficulties.d2
 * @property {Boolean} form.difficulties.d3
 * @property {Boolean} form.iMode
 * @property {array} form.ingredients
 * @property {array} form.ingredients_except
 * @property {Boolean} form.pdf
 * @property {array} form.prepTime
 * @property {array} form.tags
 * @property {array} form.tags_except
 */
</script>

<template>
  <button v-if="filter" type="button" class="relative btn h-8 px-4" @click="open = true">
    {{ __('Filter') }}<span class="max-mobile:sr-only"> / {{ __('Search') }}</span>
    <span v-if="isActive" class="flex absolute h-3 w-3 top-0 right-0 -mt-1 -mr-1">
      <span
        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
      />
      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500" />
    </span>
  </button>
  <ErrorModal v-if="showError" :error="errors" @close="showError = false" />
  <Sidebar v-if="filter" :show="open" @close="cancel">
    <DialogTitle class="text-base font-medium p-2 select-none" as="div">
      {{ __('Filters') }} / {{ __('Search') }}
      <button type="button" class="sr-only" />
    </DialogTitle>
    <div
      class="flex-1 flex pb-12 flex-col gap-4 border p-2 border-primary-600/90 overflow-y-auto m-1 rounded-sm scrollbar-thin scrollbar-thumb-rounded-full"
    >
      <label class="filter-row">
        <span class="child">
          {{ __('Search') }}
        </span>
        <div class="px-3 py-2 border-t border-primary-600">
          <input
            v-model="search.value"
            :placeholder="__('Enter search term') + '...'"
            type="search"
            class="w-full"
          />
        </div>
      </label>
      <div class="filter-row">
        <div class="child">
          <label class="clickable-label">
            <input v-model="form.pdf" type="checkbox" class="" />
            {{ __('Show only recipes with PDF') }}
          </label>
        </div>
      </div>
      <div class="filter-row">
        <div class="child">
          <label class="clickable-label">
            <input v-model="form.iMode" type="radio" :value="false" />
            {{
              __('Show only recipes with one of these :item', {
                item: __('Ingredients')
              })
            }}
            ({{ __('max :number', { number: settings.filter.max_filterable_items }) }})
          </label>
          <label class="clickable-label">
            <input v-model="form.iMode" type="radio" :value="true" />
            {{ __('Show only recipes with each of these ingredients') }}
            ({{ __('max :number', { number: settings.filter.max_filterable_items }) }})
          </label>
        </div>
        <div class="mt-1">
          <Multiselect v-model="form.ingredients" route="ingredients" />
        </div>
      </div>
      <label v-for="filterable in $page.props.filterable" :key="filterable" class="filter-row">
        <span class="child">
          {{
            filterable.endsWith('_except')
              ? __('Show only recipes without this :item', {
                  item: __(filterable.split('_')[0])
                })
              : __('Show only recipes with one of these :item', {
                  item: __(filterable.split('_')[0])
                })
          }}
          ({{ __('max :number', { number: settings.filter.max_filterable_items }) }})
        </span>
        <Multiselect
          v-model="form[filterable.toLowerCase()]"
          :route="filterable.split('_')[0].toLowerCase()"
        />
      </label>
      <label class="filter-row">
        <span class="child">
          {{ __('Prep time') }}: {{ form.prepTime[0] }} - {{ form.prepTime[1] }}
          {{ __('Minutes') }}
        </span>
        <div class="p-4 border-t border-primary-600">
          <Slider
            v-model="form.prepTime"
            :min="country.data.prepMin"
            :max="country.data.prepMax"
            :tooltips="false"
            :lazy="false"
          />
        </div>
      </label>
      <div class="filter-row">
        <span class="child">
          {{ __('Difficulties') }}
        </span>
        <div class="flex flex-col border-t border-primary-600">
          <div v-for="(state, difficulty) in form.difficulties" :key="difficulty" class="child">
            <label class="clickable-label">
              <input
                v-model="form.difficulties[difficulty]"
                type="checkbox"
              />
              <template v-if="difficulty === 'd1'">
                {{ __('Easy') }}
              </template>
              <template v-else-if="difficulty === 'd2'">
                {{ __('Medium') }}
              </template>
              <template v-else>
                {{ __('Hard') }}
              </template>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-wrap gap-2 items-center justify-center sm:justify-between p-2">
      <div class="flex gap-2">
        <button
          type="button"
          class="btn btn-danger w-32 whitespace-nowrap"
          :disabled="processing"
          @click="cancel"
        >
          {{ __('Cancel') }}
        </button>
        <button v-if="isActive" type="button" class="btn btn-danger" @click="reset">
          {{ __('Reset') }}
        </button>
      </div>
      <button
        type="button"
        class="btn w-44 whitespace-nowrap"
        :disabled="
          processing || (!form.isDirty && searchInit.toLowerCase() == search.value.toLowerCase())
        "
        :class="{
          'btn-disabled':
            processing || (!form.isDirty && searchInit.toLowerCase() == search.value.toLowerCase())
        }"
        @click="submit"
      >
        <Loading v-if="processing" class="w-6 h-6" />
        <span v-else>
          {{ __('Apply') }}
        </span>
      </button>
    </div>
  </Sidebar>
</template>
