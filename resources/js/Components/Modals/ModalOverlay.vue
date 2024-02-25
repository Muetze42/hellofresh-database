<script setup>
import { DialogOverlay } from '@headlessui/vue'
defineEmits(['close'])
</script>

<template>
  <DialogOverlay class="fixed inset-0 overlay transition-opacity" @click="$emit('close')" />
</template>
