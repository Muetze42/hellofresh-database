<script setup>
import { ref } from 'vue'

const open = ref(false)
</script>
<template>
  <Modal :show="open" :title="__('Many thanks for any support')" @close="open = false">
    <div
      class="p-2 flex flex-col gap-2 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-rounded-full"
    >
      <div
        v-for="(section, key) in $page.props.support"
        :key="key"
        class="flex flex-col border rounded-sm text-center border-primary-600/90"
      >
        <a
          v-if="key == 'tinkerwell'"
          href="https://tinkerwell.app/ref/Qdxy2Abv19"
          class="p-2 flex hover:bg-primary-500 gap-2 items-center leading-4"
          target="_blank"
        >
          <img src="@assets/tinkerwell.png" alt="" class="h-14 w-14" />
          <span class="text-left">
            <span class="font-semibold block">Tinkerwell</span>
            <span class="text-xs leading-3">
              The magical code editor that runs your code within local and remote PHP applications.
            </span>
          </span>
        </a>
        <template v-else>
          <div class="font-medium border-b border-primary-600/80 p-1">
            {{ section.title }}
          </div>
          <div class="divide-y divide-primary-600/40">
            <div v-for="(item, index) in section.items" :key="index" class="p-0.5">
              <a :href="item.url" class="link" target="_blank">
                {{ item.label }}
              </a>
            </div>
          </div>
        </template>
      </div>
    </div>
  </Modal>
  <footer class="nav-element border-t pt-1 pb-2">
    <div class="container flex gap-1 max-sm:flex-col flex-wrap justify-center sm:justify-between items-center">
      <div class="max-sm:text-center text-sm inline-flex items-center gap-1">
        <font-awesome-icon :icon="['far', 'copyright']" flip="horizontal" /> 2024
        <a href="https://huth.it" target="_blank" class="link">Norman Huth</a>
      </div>
      <div class="max-sm:text-center">
        <button type="button" class="btn btn-sm" @click="open = true">
          {{ __('Support developer') }} /
          {{ __('Questions') }} /
          {{ __('Links') }}
        </button>
      </div>
    </div>
  </footer>
</template>
