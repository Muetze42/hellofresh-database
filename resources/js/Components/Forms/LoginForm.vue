<script setup>
import { ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import InputField from '@/Components/Forms/InputField.vue'
import CheckboxField from '@/Components/Forms/CheckboxField.vue'

const showError = ref(false)
const errors = ref(null)
const baseUrl = usePage().props.country.route + '/'
const processing = ref(false)

const emit = defineEmits(['close'])

const form = useForm({
  email: null,
  password: null,
  remember: false
})
const loginErrors = ref(null)
function login() {
  processing.value = true
  loginErrors.value = null
  axios
    .post(baseUrl + 'login', form)
    .then(() => {
      router.reload({ only: ['user'] })
      processing.value = false
      emit('close')
    })
    .catch(function (error) {
      processing.value = false
      if (error.response.status === 422) {
        loginErrors.value = {
          message: error.response.data.message,
          errors: error.response.data.errors
        }
      } else {
        showError.value = true
        errors.value = error
      }
    })
  form.reset('password')
}
</script>

<template>
  <form class="form-modal" @submit.prevent="login">
    <div v-if="loginErrors && loginErrors.message" class="invalid">
      {{ loginErrors.message }}
    </div>
    <InputField v-model="form.email" :label="__('Email Address')" type="email" />
    <InputField v-model="form.password" :label="__('Password')" type="password" />
    <CheckboxField v-model="form.remember" :label="__('Remember Me')" />
    <div class="f">
      <button type="button" class="btn btn-danger" @click="emit('close')">
        {{ __('Cancel') }}
      </button>
      <button
        type="submit"
        class="btn"
        :disabled="processing || !form.isDirty || !form.email || !form.password"
        :class="{
          'btn-disabled':
            processing || !form.isDirty || !form.email || !form.password
        }"
      >
        <Loading class="w-4 transition-all" :class="{ 'opacity-0': !processing }" />
        {{ __('Submit') }}
        <Loading class="w-4 opacity-0" />
      </button>
    </div>
  </form>
</template>
