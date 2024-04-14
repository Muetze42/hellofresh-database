<script setup>
import axios, { AxiosError } from 'axios'
import { computed, onMounted } from 'vue'
import { __ } from '@/mixins.js'

const props = defineProps({
  size: {
    type: String,
    required: false,
    default: 'max-w-md'
  },
  error: {
    type: [String, Object],
    required: true
  }
})
defineEmits(['close'])

const title = computed(() => {
  if (props.error instanceof AxiosError) {
    if (props.error.response.status === 419) {
      return __('Session expired')
    }
    if (props.error.response.status === 503) {
      return __('Maintenance')
    }
  }
  return __('Error')
})
const withFooter = computed(() => {
  if (props.error instanceof AxiosError) {
    return ![419, 503].includes(props.error.response.status)
  }
  return true
})

function reloadPage() {
  window.location.reload()
}
function checkMaintenance(timeout = 1000) {
  setTimeout(() => {
    axios
      .get('/api')
      .then(() => reloadPage())
      .catch(() => checkMaintenance())
  }, timeout)
}
onMounted(() => {
  if (props.error instanceof AxiosError && props.error.response.status === 503) {
    checkMaintenance(2500)
  }
})
</script>
<template>
  <Modal :show="true" :title="title" :with-footer="withFooter" @close="$emit('close')">
    <div class="p-2">
      <template v-if="error instanceof AxiosError && error.response.status === 419">
        <p>{{ __('Your session has expired.') }}</p>
        <p>{{ __('Click OK to reload the page.') }}</p>
      </template>
      <template v-else-if="error instanceof AxiosError && error.response.status === 503">
        <p>{{ __('There is an update in progress.') }}</p>
        <p>{{ __('This lasts only a few seconds.') }}</p>
      </template>
      <template v-else-if="error instanceof AxiosError && error.response.status === 422">
        {{ error.response.data.message }}
      </template>
      <template v-else-if="error instanceof AxiosError">
        {{ error.response.data.message ? error.response.data.message : error.response.data }}
      </template>
      <template v-else>
        {{ error }}
      </template>
    </div>
    <div v-if="!withFooter" class="modal-footer">
      <button
        v-if="error instanceof AxiosError && error.response.status === 503"
        type="button"
        class="btn btn-disabled px-4 py-1"
        disabled
      >
        <Loading class="w-4 h-4" />
      </button>
      <button v-else type="button" class="btn" @click="reloadPage">
        {{ __('OK') }}
      </button>
    </div>
  </Modal>
</template>
