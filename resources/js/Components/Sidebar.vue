<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
defineEmits(['close'])
defineProps({
  isLeft: {
    type: Boolean,
    required: false,
    default: false
  }
})
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
      <div class="fixed inset-0 overflow-hidden" @click="$emit('close')">
        <div class="absolute inset-0 overflow-hidden">
          <div
            class="fixed inset-y-0 flex pointer-events-none"
            :class="isLeft ? 'left-0 pr-10 max-w-sm' : 'right-0 pl-10 max-w-full'"
          >
            <TransitionChild
              as="template"
              enter="transform transition ease-in-out duration-700 sm:duration-1000"
              :enter-from="isLeft ? '-translate-x-full' : 'translate-x-full'"
              enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700"
              leave-from="translate-x-0"
              :leave-to="isLeft ? '-translate-x-full' : 'translate-x-full'"
            >
              <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                <div class="flex h-full flex-col bg-primary-200 shadow-xl">
                  <slot />
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
