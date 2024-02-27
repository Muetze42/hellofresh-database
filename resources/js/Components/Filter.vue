<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Multiselect from '@/Components/Forms/Multiselect.vue'
import Slider from '@vueform/slider'
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const showError = ref(false)
const errors = ref(null)

const open = ref(false)
const processing = ref(false)
const page = usePage()
const filter = page.props.filter
const baseUrl = page.props.country.route + '/'

// noinspection JSCheckFunctionSignatures
const form = useForm(filter)

const searchInit = new URLSearchParams(window.location.search).get('search')
const search = reactive({
  value: searchInit
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
  <button type="button" class="btn" @click="open = true">
    {{ __('Filter') }} / {{ __('Search') }}
  </button>
  <ErrorModal v-if="showError" :error="errors" @close="showError = false" />
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-40">
      <TransitionChild
        as="template"
        enter="ease-in-out duration-500"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in-out duration-500"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 overlay transition-opacity" />
      </TransitionChild>
      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              leave-to="translate-x-full"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                <div class="flex h-full flex-col bg-gray-700 shadow-xl">
                  <DialogTitle class="text-base font-medium p-2 select-none">
                    {{ __('Filters') }} / {{ __('Search') }}
                    <button type="button" class="sr-only" />
                  </DialogTitle>
                  <div
                    class="flex-1 flex pb-12 flex-col gap-4 border p-2 border-gray-600/90 overflow-y-auto m-1 rounded scrollbar-thin scrollbar-thumb-rounded-full"
                  >
                    <div class="filter-row py-2">
                      <label class="child flex flex-col">
                        {{ __('Search') }}
                        <input
                          v-model="search.value"
                          :placeholder="__('Enter search term') + '...'"
                          type="search"
                          class="form-input w-full"
                        />
                      </label>
                    </div>
                    <div class="filter-row">
                      <label class="clickable-label child">
                        <input v-model="form.pdf" type="checkbox" class="" />
                        {{ __('Show only recipes with PDF') }}
                      </label>
                    </div>
                    <div class="flex flex-col filter-row">
                      <div>
                        <label class="clickable-label child">
                          <input v-model="form.iMode" type="radio" :value="false" />
                          {{
                            __('Show only recipes with one of these :item', {
                              item: __('Ingredients')
                            })
                          }}
                          ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                        </label>
                      </div>
                      <div>
                        <label class="clickable-label child">
                          <input v-model="form.iMode" type="radio" :value="true" />
                          {{ __('Show only recipes with each of these ingredients') }}
                          ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                        </label>
                      </div>
                      <div class="mt-1">
                        <Multiselect v-model="form.ingredients" route="ingredients" />
                      </div>
                    </div>
                    <label
                      v-for="filterable in $page.props.filterable"
                      :key="filterable"
                      class="filter-row"
                    >
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
                        ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
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
                      <div class="p-4 border-t border-gray-600">
                        <Slider
                          v-model="form.prepTime"
                          :min="country.data.prepMin"
                          :max="country.data.prepMax"
                          :tooltips="false"
                          :lazy="false"
                        />
                      </div>
                    </label>
                  </div>
                  <div
                    class="flex flex-wrap gap-2 items-center justify-center xs:justify-between p-2"
                  >
                    <button
                      type="button"
                      class="btn btn-danger w-32 whitespace-nowrap"
                      :disabled="processing"
                      @click="[form.reset(), (open = false)]"
                    >
                      {{ __('Cancel') }}
                    </button>
                    <button
                      type="button"
                      class="btn w-44 whitespace-nowrap"
                      :disabled="processing || (!form.isDirty && searchInit == search.value)"
                      :class="{
                        'btn-disabled': processing || (!form.isDirty && searchInit == search.value)
                      }"
                      @click="submit"
                    >
                      <Loading v-if="processing" class="w-6 h-6" />
                      <span v-else>
                        {{ __('Apply') }}
                      </span>
                    </button>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
