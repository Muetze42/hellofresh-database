<script setup>
import Image from '@assets/NotFoundImage.vue'
</script>
<template>
  <div class="text-center">
    <div class="lg:flex justify-center mb-4">
      <p
        class="text-2xl mb-4 bg-rose-200/90 text-red-700 shadow-lg shadow-rose-500 font-medium p-2 lg:px-4 rounded-sm"
      >
        {{ __('Oops. No recipes were found using these criteria.') }}
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
