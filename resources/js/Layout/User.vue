<script setup>
import {
  Dialog,
  DialogDescription,
  DialogPanel,
  DialogTitle,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  Tab,
  TabGroup,
  TabList,
  TabPanel,
  TabPanels,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'
import { ref } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'

const baseUrl = usePage().props.country.route + '/'
const processing = ref(false)
const isOpen = ref(false)
const loginForm = useForm({
  email: null,
  password: null,
  remember: false
})
const loginErrors = ref(null)
const registerForm = useForm({
  name: null,
  email_confirmation: null,
  email: null,
  password: null,
  password_confirmation: null
})
const registerErrors = ref(null)
const resetForm = useForm({
  email: null,
  email_confirmation: null
})
const resetErrors = ref(null)
const loginTabs = ref({
  login: 'Login',
  register: 'Register',
  forgot: 'Forgot your password?'
})

async function login() {
  processing.value = true
  loginErrors.value = null
  await axios
    .post(baseUrl + 'login', loginForm)
    .then(() => {
      router.reload({ only: ['user'] })
      processing.value = false
      isOpen.value = false
    })
    .catch(function (error) {
      processing.value = false
      if (error.response.status === 422) {
        loginErrors.value = {
          message: error.response.data.message,
          errors: error.response.data.errors
        }
      } else {
        alert(error.response.data)
      }
    })
}
</script>

<template>
  <div v-if="$page.props.user">
    {{ $page.props.user.name }}
  </div>
  <button v-else class="btn" @click="isOpen = true">Login</button>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-40" @close="isOpen = false">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/25" />
      </TransitionChild>

      <div class="fixed inset-0 overlay transition-opacity">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-lg transform overflow-hidden rounded-sm bg-primary-200 text-left align-middle shadow-xl transition-all"
            >
              <TabGroup>
                <TabList
                  class="flex space-x-1 rounded-t-sm px-1.5 pt-1.5 bg-primary-500 border-b border-primary-700/50"
                >
                  <Tab
                    v-for="(label, index) in loginTabs"
                    :key="index"
                    v-slot="{ selected }"
                    as="template"
                  >
                    <button
                      disabled
                      :class="[
                        'w-full rounded-t-sm text-sm font-medium leading-5 py-1',
                        'ring-white/60 ring-offset-1 ring-offset-accent-500 focus:outline-none focus:ring-2',
                        selected
                          ? 'bg-accent-400 shadow'
                          : 'bg-primary-600 text-neutral-700 hover:bg-accent-400/50'
                      ]"
                    >
                      {{ __(label) }}
                    </button>
                  </Tab>
                </TabList>
                <TabPanels>
                  <TabPanel :key="loginTabs.login">
                    <form class="form-modal" @submit.prevent="login">
                      <div v-if="loginErrors && loginErrors.message" class="invalid">
                        {{ loginErrors.message }}
                      </div>
                      <label>
                        {{ __('Email Address') }}
                        <input v-model="loginForm.email" type="email" class="w-full" />
                        <div
                          v-if="
                            loginErrors &&
                            loginErrors.errors &&
                            loginErrors.errors.email &&
                            loginErrors.errors.email.length !== 1
                          "
                          class="invalid"
                        >
                          <ul>
                            <li v-for="(error, index) in loginErrors.errors.email" :key="index">
                              {{ error }}
                            </li>
                          </ul>
                        </div>
                      </label>
                      <label>
                        {{ __('Password') }}
                        <input v-model="loginForm.password" type="password" class="w-full" />
                        <div
                          v-if="
                            loginErrors &&
                            loginErrors.errors &&
                            loginErrors.errors.password &&
                            loginErrors.errors.password.length !== 1
                          "
                          class="invalid"
                        >
                          <ul>
                            <li v-for="(error, index) in loginErrors.errors.password" :key="index">
                              {{ error }}
                            </li>
                          </ul>
                        </div>
                      </label>
                      <div class="text-center">
                        <label class="clickable-label">
                          <input v-model="loginForm.remember" type="checkbox" />
                          {{ __('Remember Me') }}
                        </label>
                      </div>
                      <div class="f">
                        <button type="button" class="btn btn-danger" @click="isOpen = false">
                          {{ __('Cancel') }}
                        </button>
                        <button
                          type="submit"
                          class="btn"
                          :disabled="
                            processing ||
                            !loginForm.isDirty ||
                            !loginForm.email ||
                            !loginForm.password
                          "
                          :class="{
                            'btn-disabled':
                              processing ||
                              !loginForm.isDirty ||
                              !loginForm.email ||
                              !loginForm.password
                          }"
                        >
                          <Loading
                            class="w-4 transition-all"
                            :class="{ 'opacity-0': !processing }"
                          />
                          {{ __('Submit') }}
                          <Loading class="w-4 opacity-0" />
                        </button>
                      </div>
                    </form>
                  </TabPanel>
                  <TabPanel :key="loginTabs.register">register</TabPanel>
                  <TabPanel :key="loginTabs.forgot">forgot</TabPanel>
                </TabPanels>
              </TabGroup>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
