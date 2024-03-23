<script setup>
import { data_get } from '@norman-huth/helpers-collection-js/helpers/helpers.js'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number, null],
    required: true,
    default: null
  },
  type: {
    type: String,
    required: false,
    default: 'text'
  },
  label: {
    type: String,
    required: true
  },
  errors: {
    type: Object,
    required: false,
    default: null
  },
  errorsKey: {
    type: String,
    required: false,
    default: null
  },
  required: {
    type: Boolean,
    required: false,
    default: true
  }
})
defineEmits(['update:modelValue'])
const fieldErrors = computed(() => {
  if (!props.errorsKey) {
    return null
  }
  return data_get(props.errors, 'errors.' + props.errorsKey)
})
</script>

<template>
  <label>
    {{ label }}
    <input
      :value="modelValue"
      :type="type"
      :required="required"
      class="w-full"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <div v-if="fieldErrors" class="invalid">
      <ul>
        <li v-for="(error, index) in fieldErrors" :key="index">
          {{ error }}
        </li>
      </ul>
    </div>
  </label>
</template>
