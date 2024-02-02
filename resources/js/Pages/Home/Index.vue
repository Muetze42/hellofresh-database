<template>
  <div class="p-2 flex justify-center items-center h-screen bg-neutral-800">
    <div
      class="bg-sky-900 p-4 text-white rounded flex flex-col w-full max-w-sm hover:bg-fuchsia-900"
    >
      <h1 class="font-medium text-xl">It works!</h1>
      <div>Your IP: {{ ip }}</div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    ip: {
      type: String,
      required: true
    }
  }
}
</script>
