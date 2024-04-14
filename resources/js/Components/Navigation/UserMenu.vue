<script setup>
import Guest from '@/Components/Navigation/User/Guest.vue'
import User from '@/Components/Navigation/User/User.vue'
</script>

<template>
  <User v-if="$page.props.user" />
  <Guest v-else />
</template>
