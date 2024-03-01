<script setup>
import Header from '@/Layout/Header.vue'
import Footer from '@/Layout/Footer.vue'
</script>

<template>
  <Header />
  <main>
    <slot />
  </main>
  <Footer />
</template>
