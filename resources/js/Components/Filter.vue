<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import Multiselect from '@/Components/Forms/Multiselect.vue'

const page = usePage()
const filter = page.props.filter

// noinspection JSCheckFunctionSignatures
const form = useForm(filter)

defineEmits(['close'])

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
  <TransitionRoot as="template" :show="true">
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
                  <DialogTitle class="text-base font-medium p-2">
                    {{ __('Filters') }}
                  </DialogTitle>
                  <form
                    class="flex-1 flex flex-col gap-4 border p-2 border-gray-600/90 overflow-y-auto m-1 rounded scrollbar-thin scrollbar-thumb-rounded-full"
                  >
                    <div>
                      <label class="cursor-pointer">
                        <input v-model="form.pdf" type="checkbox" class="" />
                        {{ __('Show only recipes with PDF') }}
                      </label>
                    </div>
                    <label>
                      <div class="flex flex-col pb-2">
                        <label class="cursor-pointer">
                          <input v-model="form.iMode" type="radio" :value="false" />
                          {{ __('Show only recipes with one of these ingredient') }}
                          ({{ __('max :number', { number: 20 }) }})
                        </label>
                        <label class="cursor-pointer">
                          <input v-model="form.iMode" type="radio" :value="true" />
                          {{ __('Show only recipes with all these ingredient') }}
                          ({{ __('max :number', { number: 20 }) }})
                        </label>
                      </div>
                      <Multiselect v-model="form.ingredients" route="ingredients" />
                    </label>
                    <label>
                      {{ __('Show only recipes without this ingredient') }}
                      ({{ __('max :number', { number: 20 }) }})
                      <Multiselect v-model="form.ingredients_not" route="ingredients" />
                    </label>
                    <label>
                      {{ __('Show only recipes without this allergens') }}
                      ({{ __('max :number', { number: 20 }) }})
                      <Multiselect v-model="form.allergens" route="allergens" />
                    </label>
                  </form>
                  <div class="flex gap-2 items-center justify-end p-2">
                    <button
                      type="button"
                      class="btn btn-danger"
                      @click="[form.reset(), $emit('close')]"
                    >
                      {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn">
                      {{ __('Apply filters') }}
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
