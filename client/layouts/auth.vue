<template>
  <div class="h-full antialiased bg-blue-300 text-gray-800 bg-gradient">
    <div class="mx-auto container h-full p-2 flex flex-col">
      <nuxt class="flex-1" />
    </div>
  </div>
</template>

<style>
  html,
  body,
  #__nuxt,
  #__layout {
    @apply h-full;
  }

  .bg-gradient {
    background-image: linear-gradient(to right, theme('colors.blue.300'), theme('colors.blue.500'));
  }
</style>
