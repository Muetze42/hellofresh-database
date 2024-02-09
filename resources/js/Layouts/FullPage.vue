<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>

<template>
  <Header />
  <main>
    <slot />
  </main>
  <Footer />
</template>
