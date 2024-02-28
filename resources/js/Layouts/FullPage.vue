<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>

<template>
  <Header :filterable="filterable" />
  <main>
    <div class="flex justify-center mb-2">
      <div
        class="mb-6 bg-rose-800/90 text-red-200 shadow-lg shadow-rose-500 font-medium p-2 lg:px-4 rounded-sm"
      >
        <p class="text-lg text-center">HelloFresh DB Beta Version</p>
        <p class="text-center">GitHub: <a href="https://github.com/Muetze42/hellofresh-database" target="_blank" class="link">Muetze42/hellofresh-database</a></p>
      </div>
    </div>
    <slot />
  </main>
  <Footer />
</template>
<script>
export default {
  props: {
    filterable: {
      type: Boolean,
      required: false,
      default: false
    }
  }
}
</script>
