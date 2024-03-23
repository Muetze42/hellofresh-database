<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import InputField from '@norman-huth/helpers-collection-js/components/forms/InputField.vue'
const showError = ref(false)
const errors = ref(null)
const baseUrl = usePage().props.country.route + '/'
const processing = ref(false)
const formErrors = ref(null)
const message = ref(null)

const emit = defineEmits(['close'])

const form = useForm({
  email: null
})
function submit() {
  processing.value = true
  formErrors.value = null

  axios
    .post(baseUrl + 'password/email', form)
    .then((response) => {
      processing.value = false
      message.value = response.data.message
    })
    .catch(function (error) {
      processing.value = false
      if (error.response.status === 422) {
        formErrors.value = {
          message: error.response.data.message,
          errors: error.response.data.errors
        }
      } else {
        showError.value = true
        errors.value = error
      }
    })
}
</script>

<template>
  <ErrorModal v-if="showError" :error="errors" @close="showError = false" />
  <div v-if="message" class="successfully">
    {{ message }}
  </div>
  <form @submit.prevent="submit" v-else>
    <div class="form-modal">
      <div class="text-center font-bold">
        {{ __('Link zum Zurücksetzen des Passworts senden') }}
      </div>
      <div v-if="formErrors && formErrors.message" class="invalid">
        {{ formErrors.message }}
      </div>
      <InputField
        v-model="form.email"
        :label="__('Email Address')"
        type="email"
        :maxlength="255"
        required
      />
    </div>
    <div class="f">
      <button type="button" class="btn btn-danger" @click="emit('close')">
        {{ __('Cancel') }}
      </button>
      <button
        type="submit"
        class="btn"
        :disabled="processing || !form.isDirty || !form.email"
        :class="{
          'btn-disabled': processing || !form.isDirty || !form.email
        }"
      >
        <Loading class="w-4 transition-all" :class="{ 'opacity-0': !processing }" />
        {{ __('Submit') }}
        <Loading class="w-4 opacity-0" />
      </button>
    </div>
  </form>
</template>
