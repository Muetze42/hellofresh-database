<script setup>
import {
  Dialog,
  DialogPanel,
  TransitionRoot,
  DialogOverlay,
  TransitionChild
} from '@headlessui/vue'
defineEmits(['close'])
</script>

<template>
  <TransitionRoot as="template">
    <Dialog as="div" class="fixed z-50 inset-0 px-4" @close="$emit('close')">
      <div
        class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <button type="button" class="sr-only" aria-label="catch focus button" />
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay
            ad="div"
            class="fixed inset-0 overlay transition-opacity"
            @click.self="$emit('close')"
          />
        </TransitionChild>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">
          &#8203;
        </span>
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to="opacity-100 translate-y-0 sm:scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 translate-y-0 sm:scale-100"
          leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <DialogPanel
            as="div"
            class="border border-primary-800/50 bg-primary-300 rounded-sm inline-block align-bottom overflow-hidden shadow-xl transform transition-all sm:align-middle w-full text-left"
            :class="size"
          >
            <div v-if="title" class="border-b border-primary-900/30 px-2 py-1 font-medium">
              {{ title }}
            </div>
            <slot />
            <div v-if="withFooter" class="modal-footer">
              <button type="button" class="btn" @click="$emit('close')">
                {{ __('OK') }}
              </button>
            </div>
          </DialogPanel>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
export default {
  props: {
    size: {
      type: String,
      required: false,
      default: 'max-w-md'
    },
    title: {
      type: String,
      required: false,
      default: null
    },
    withFooter: {
      type: Boolean,
      required: false,
      default: true
    }
  },
  created() {
    let ref = this
    document.addEventListener('keyup', function (event) {
      if (event.key === 'Escape' || event.code === 'Escape') {
        ref.$emit('close')
      }
    })
  }
}
</script>
