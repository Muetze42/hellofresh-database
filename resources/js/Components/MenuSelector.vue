<script setup>
import { Menu, MenuButton, MenuItems } from '@headlessui/vue'

defineProps({
  menus: {
    type: Object,
    required: true
  }
})
/**
 * @property menus
 * @property menus.list
 * @property menus.list.value
 * @property menus.list.start
 * @property menus.list.end
 * @property menus.current
 * @property menus.current.value
 * @property menus.current.start
 * @property menus.current.end
 */
</script>
<template>
  <div class="mb-8 text-center">
    <Menu v-slot="{ open }" as="div" class="relative inline-block z-40">
      <MenuButton
        as="button"
        class="transition-all transform rounded-sm inline-flex items-center justify-center gap-3 font-medium bg-white text-neutral-900 py-0.5 px-2 border border-primary-700"
      >
        {{ menus.current.start }} - {{ menus.current.end }}
        <font-awesome-icon
          :icon="['fas', 'angle-right']"
          :class="open ? 'rotate-90 text-neutral-500' : 'text-neutral-500/50'"
        />
      </MenuButton>
      <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <MenuItems
          as="div"
          class="absolute bg-primary-700 flex flex-col w-full divide-y divide-primary-600"
        >
          <Link
            v-for="menu in menus.list"
            :key="menu.value"
            :href="filterLink(country.route + '/menus/' + menu.value)"
            class="p-1 bg-white text-neutral-900 hover:bg-blue-700 hover:text-white active:brightness-110"
          >
            {{ menu.start }} - {{ menu.end }}
          </Link>
        </MenuItems>
      </transition>
    </Menu>
  </div>
</template>
