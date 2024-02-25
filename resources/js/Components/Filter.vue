<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Multiselect from '@/Components/Forms/Multiselect.vue'
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const open = ref(false)
const processing = ref(false)
const page = usePage()
const filter = page.props.filter
const baseUrl = page.props.country.route

// noinspection JSCheckFunctionSignatures
const form = useForm(filter)

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
      router.get(page.url.split('?')[0], data)
    })
    .catch(function (error) {
      alert(error.response.data) // Todo Error Handler
    })
}

/**
 * @property {Object} form
 * @property {Boolean} form.pdf
 * @property {Boolean} form.iMode
 * @property {array} form.ingredients
 * @property {array} form.ingredients_not
 * @property {array} form.allergens
 */
</script>

<template>
  <button type="button" class="btn" @click="open = true">{{ __('Filter') }}</button>
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
                    {{ __('Filters') }}
                  </DialogTitle>
                  <div
                    class="flex-1 flex flex-col gap-4 border p-2 border-gray-600/90 overflow-y-auto m-1 rounded scrollbar-thin scrollbar-thumb-rounded-full"
                  >
                    <div class="filter-row">
                      <label class="clickable-label pad">
                        <input v-model="form.pdf" type="checkbox" class="" />
                        {{ __('Show only recipes with PDF') }}
                      </label>
                    </div>
                    <div class="flex flex-col filter-row">
                      <div>
                        <label class="clickable-label pad">
                          <input v-model="form.iMode" type="radio" :value="false" />
                          {{ __('Show only recipes with one of these ingredient') }}
                          ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                        </label>
                      </div>
                      <div>
                        <label class="clickable-label pad">
                          <input v-model="form.iMode" type="radio" :value="true" />
                          {{ __('Show only recipes with each of these ingredients') }}
                          ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                        </label>
                      </div>
                      <div class="mt-1">
                        <Multiselect v-model="form.ingredients" route="ingredients" />
                      </div>
                    </div>
                    <label class="flex flex-col gap-1 filter-row">
                      <span class="pad">
                        {{ __('Show only recipes without this ingredient') }}
                        ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                      </span>
                      <Multiselect v-model="form.ingredients_not" route="ingredients" />
                    </label>
                    <label class="flex flex-col gap-1 filter-row">
                      <span class="pad">
                        {{ __('Show only recipes without this allergens') }}
                        ({{ __('max :number', { number: config.filter.max_filterable_items }) }})
                      </span>
                      <Multiselect v-model="form.allergens" route="allergens" />
                    </label>
                  </div>
                  <div class="flex gap-2 items-center justify-end p-2">
                    <button
                      type="button"
                      class="btn btn-danger"
                      :disabled="processing"
                      @click="[form.reset(), (open = false)]"
                    >
                      {{ __('Cancel') }}
                    </button>
                    <button
                      type="button"
                      class="btn w-44"
                      :disabled="processing || !form.isDirty"
                      :class="{ 'btn-disabled': processing || !form.isDirty }"
                      @click="submit"
                    >
                      <Loading v-if="processing" class="w-6 h-6" />
                      <span v-else>
                        {{ __('Apply filters') }}
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
