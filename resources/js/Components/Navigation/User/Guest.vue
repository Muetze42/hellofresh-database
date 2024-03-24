<script setup>
import {
  Dialog,
  DialogPanel,
  Tab,
  TabGroup,
  TabList,
  TabPanel,
  TabPanels,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'
import { ref } from 'vue'
import LoginForm from '@/Components/Forms/LoginForm.vue'
import RegisterForm from '@/Components/Forms/RegisterForm.vue'
import ForgotPasswordForm from '@/Components/Forms/ForgotPasswordForm.vue'

const isOpen = ref(false)

const loginTabs = ref({
  login: 'Login',
  register: 'Register',
  forgot: 'Forgot your password?'
})
</script>

<template>
  <button class="btn" @click="isOpen = true">Login</button>
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
                      class="w-full rounded-t-sm text-xs sm:text-sm leading-snug sm:leading-tight font-medium py-1 ring-white/60 ring-offset-1 ring-offset-accent-500 focus:outline-none focus:ring-2"
                      :class="
                        selected
                          ? 'bg-accent-400 shadow'
                          : 'bg-primary-600 text-neutral-700 hover:bg-accent-400/50'
                      "
                    >
                      {{ __(label) }}
                    </button>
                  </Tab>
                </TabList>
                <TabPanels>
                  <TabPanel :key="loginTabs.login">
                    <LoginForm @close="isOpen = false" />
                  </TabPanel>
                  <TabPanel :key="loginTabs.register">
                    <RegisterForm @close="isOpen = false" />
                  </TabPanel>
                  <TabPanel :key="loginTabs.forgot">
                    <ForgotPasswordForm @close="isOpen = false" />
                  </TabPanel>
                </TabPanels>
              </TabGroup>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
