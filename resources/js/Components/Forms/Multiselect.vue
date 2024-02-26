<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Multiselect from '@vueform/multiselect'

const emit = defineEmits(['update:modelValue'])

const showError = ref(false)
const errors = ref(null)

const props = defineProps({
  route: {
    type: String,
    required: true
  },
  modelValue: {
    type: Object,
    required: true
  }
})

const modelItems = ref(props.modelValue)
const searchValue = ref('')
const isLoading = ref(false)

const page = usePage()
const baseUrl = page.props.country.route + '/'

function searchValueChange(value) {
  searchValue.value = value
}

async function getItems(query) {
  isLoading.value = true
  let data = {}

  await axios
    .post(baseUrl + 'filters/' + props.route, { query: query })
    .then((response) => {
      data = response.data
    })
    .catch((error) => {
      showError.value = true
      errors.value = error
    })

  isLoading.value = false

  if (!data.length) {
    return null
  }

  return data
}
</script>
<template>
  <ErrorModal v-if="showError" :error="errors" @close="showError = false" />
  <Multiselect
    v-model="modelItems"
    mode="tags"
    :searchable="true"
    :filter-results="false"
    :delay="200"
    :min-chars="2"
    :max="config.filter.max_filterable_items"
    :object="true"
    :resolve-on-load="false"
    :no-options-text="searchValue.length > 2 ? __('No results found') : null"
    :placeholder="__('Enter to search')"
    value="id"
    value-prop="id"
    label="name"
    :options="async (query) => await getItems(query)"
    @search-change="searchValueChange"
    @select="emit('update:modelValue', modelItems)"
    @deselect="emit('update:modelValue', modelItems)"
  />
</template>
