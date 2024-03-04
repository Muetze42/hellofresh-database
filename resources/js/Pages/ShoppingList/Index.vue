<script setup>
import { ref, reactive, onMounted } from 'vue'
import { TransitionRoot } from '@headlessui/vue'
import { router, usePage } from "@inertiajs/vue3";

const page = usePage()

const shoppingList = ref(JSON.parse(localStorage.getItem('shoppingList') || '[]'))
const form = reactive(JSON.parse(localStorage.getItem('shoppingListForm') || '[]'))
const recipes = ref({})
const ingredients = ref({})
const processing = ref(true)

function remove(id) {
  // noinspection JSUnresolvedReference
  shoppingList.value = shoppingList.value.filter((item) => item !== id)
  localStorage.setItem('shoppingList', JSON.stringify(shoppingList.value))
}

onMounted(() => {
  if (!shoppingList.value.length) {
    processing.value = false
    return
  }
  axios
    .post(page.props.country.route + "/shopping-list", {
      recipes: shoppingList.value,
      form: form
    })
    .then((response) => {
      processing.value = false
    })
  console.log(shoppingList.value)
  console.log(form)
})
</script>

<template>
  <h1 class="sr-only">{{ __('shopping list') }}</h1>
  <FullPage>
    <TransitionRoot
      :show="processing"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card p-4">
        <Loading class="h-20 mx-auto" />
      </section>
    </TransitionRoot>
    <TransitionRoot
      :show="!processing && !shoppingList.length"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      <section class="card text-center p-4 font-medium">
        {{ __('You don\'t have any recipes on your :list.', {list: __('shopping list')})}}
      </section>
    </TransitionRoot>

    <TransitionRoot
      :show="!processing && shoppingList.length > 0"
      enter="transition-opacity duration-250"
      enter-from="opacity-0"
      enter-to="opacity-100"
      leave="transition-opacity duration-250"
      leave-from="opacity-100"
      leave-to="opacity-0"
      class="flex flex-col gap-2"
    >
      Hi
    </TransitionRoot>
  </FullPage>
</template>
