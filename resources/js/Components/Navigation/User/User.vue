<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

import axios from 'axios'
const baseUrl = usePage().props.country.route + '/'
const processing = ref(false)

async function logout() {
  processing.value = true
  axios.post(baseUrl + 'logout').then(() => {
    router.reload({ only: ['user'] })
    processing.value = false
  })
}
</script>

<template>
  <div>
    {{ $page.props.user.name }}
    <button type="button" class="btn" @click="logout">{{ __('Logout') }}</button>
  </div>
</template>
