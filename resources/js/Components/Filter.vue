<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const page = usePage()
const filter = page.props.filter

// noinspection JSCheckFunctionSignatures
const form = useForm(filter)

defineEmits(['close'])

/**
 * @property {Object} form
 * @property {Boolean} form.pdf
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
                    id="filter"
                    class="flex-1 overflow-y-auto m-1 rounded scrollbar-thin scrollbar-thumb-rounded-full"
                  >
                    <div class="row">
                      <label class="cursor-pointer">
                        <input v-model="form.pdf" type="checkbox" class="" />
                        {{ __('Show only recipes with PDF') }}
                      </label>
                    </div>
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
