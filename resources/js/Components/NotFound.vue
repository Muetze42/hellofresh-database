<script setup>
import Image from '@assets/NotFoundImage.vue'
import { router, usePage } from '@inertiajs/vue3'
import { __ } from '@/mixins.js'
const page = usePage()
function reset() {
  if (confirm(__('Are you sure?'))) {
    router.get(page.url.split('?')[0])
  }
}
</script>
<template>
  <div class="text-center">
    <div
      class="flex flex-col justify-center max-w-4xl gap-2 lg:gap-4 mx-auto mb-8 bg-rose-200/90 text-red-700 shadow-lg shadow-rose-500 rounded-sm p-2 lg:p-4"
    >
      <p class="text-xl md:text-2xl font-medium">
        {{ __('Oops. No recipes were found.') }}
      </p>
      <p v-if="page.url.includes('filter=')">
        <button type="button" class="btn btn-danger" @click="reset">
          {{ __('Delete current filter') }}
        </button>
      </p>
    </div>
    <Image class="rounded-sm" />
    {{ __('Image by') }}
    <a
      href="https://www.freepik.com/free-vector/inside-refrigerator-with-food_25675382.htm#fromView=search&page=6&position=6&uuid=6a902bac-6b3e-43f5-8ad1-49493f1b08e2"
      target="_blank"
      class="link"
      rel="noopener"
    >
      {{ __(':author on :page', { author: 'brgfx', page: 'Freepik' }) }}
    </a>
  </div>
</template>
