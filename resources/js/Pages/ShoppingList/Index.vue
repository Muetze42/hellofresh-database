<script setup>
import { ref, reactive } from 'vue'

const shoppingList = ref(JSON.parse(localStorage.getItem('shoppingList') || '[]'))
const shoppingListForm = ref(JSON.parse(localStorage.getItem('shoppingListForm') || '[]'))
const recipes = ref({})
const ingredients = ref({})
const form = reactive({})

function remove(id) {
  // noinspection JSUnresolvedReference
  shoppingList.value = shoppingList.value.filter((item) => item !== id)
  localStorage.setItem('shoppingList', JSON.stringify(shoppingList.value))
}
</script>

<template>
  <FullPage>

  </FullPage>
</template>
