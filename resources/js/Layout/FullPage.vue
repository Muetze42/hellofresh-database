<script setup>
import Header from '@/Layout/Header.vue'
import Footer from '@/Layout/Footer.vue'
import { TransitionRoot } from '@headlessui/vue'
import { usePage } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

const props = usePage().props
const flashMessage = ref(null)
const showFlashMessage = ref(false)

function closeFlashMessage() {
  showFlashMessage.value = false
}

onMounted(() => {
  if (props.flash.message) {
    flashMessage.value = props.flash.message
    showFlashMessage.value = true
    setTimeout(() => {
      closeFlashMessage()
    }, props.settings.flash.duration * 1000)
  }
})
</script>

<template>
  <TransitionRoot
    :show="showFlashMessage"
    enter="transition-opacity duration-500"
    enter-from="opacity-0"
    enter-to="opacity-100"
    leave="transition-opacity duration-700"
    leave-from="opacity-100"
    leave-to="opacity-0"
    class="z-60"
  >
    <div class="fixed z-60 w-full top-12">
      <div class="container flex justify-end">
        <div
          class="sm:ml-auto max-sm:w-full inline-flex items-center gap-2 font-medium border-l-4 border-blue-400 bg-blue-200/95 text-blue-700 p-3 shadow shadow-blue-500"
        >
          <div class="flex gap-2 items-center">
            <font-awesome-icon :icon="['fas', 'arrow-right']" class="animate-bounce-x" />
            {{ flashMessage }}
          </div>
          <button
            type="button"
            class="btn"
            :aria-label="__('Close notification')"
            @click="closeFlashMessage()"
          >
            <font-awesome-icon :icon="['fas', 'xmark']" />
          </button>
        </div>
      </div>
    </div>
  </TransitionRoot>
  <Header />
  <main>
    <slot />
  </main>
  <Footer />
</template>
