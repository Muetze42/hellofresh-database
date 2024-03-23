<script setup>
import { ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import InputField from '@norman-huth/helpers-collection-js/components/forms/InputField.vue'

const showError = ref(false)
const errors = ref(null)
const baseUrl = usePage().props.country.route + '/'
const processing = ref(false)
const formErrors = ref(null)

const props =defineProps({
  token: {
    type: String,
    required: true
  }
})

const form = useForm({
  password: null,
  password_confirmation: null,
  token: props.token,
  email: null
})
function submit() {
  processing.value = true
  formErrors.value = null
  axios
    .post(baseUrl + 'password/reset', form)
    .then(() => {
      router.visit(baseUrl)
      processing.value = false
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
  form.reset('password', 'password_confirmation')
}
</script>

<template>
  <FullPage>
    <form class="card max-w-lg mx-auto" @submit.prevent="submit">
      <h1 class="font-bold p-2">{{ __('Reset Password') }}</h1>
      <ErrorModal v-if="showError" :error="errors" @close="showError = false" />
      <div class="bg-primary-300 form-modal">
        <div class="p-2">
          <div v-if="formErrors && formErrors.message" class="invalid">
            {{ formErrors.message }}
          </div>
        </div>
        <InputField
          v-model="form.email"
          :label="__('Email Address')"
          :errors="formErrors"
          errors-key="email"
          type="email"
          :maxlength="255"
          required
        />
        <InputField
          v-model="form.password"
          :label="__('Password')"
          :errors="formErrors"
          errors-key="password"
          type="password"
          required
        />
        <InputField
          v-model="form.password_confirmation"
          :label="__('Confirm Password')"
          :errors="formErrors"
          errors-key="password_confirmation"
          type="password"
          required
        />
      </div>
      <div class="f">
        <button
          type="submit"
          class="btn"
          :disabled="
            processing ||
            !form.isDirty ||
            !form.email ||
            !form.password ||
            !form.password_confirmation
          "
          :class="{
            'btn-disabled':
              processing ||
              !form.isDirty ||
              !form.email ||
              !form.password ||
              !form.password_confirmation
          }"
        >
          <Loading class="w-4 transition-all" :class="{ 'opacity-0': !processing }" />
          {{ __('Reset Password') }}
          <Loading class="w-4 opacity-0" />
        </button>
      </div>
    </form>
  </FullPage>
</template>
