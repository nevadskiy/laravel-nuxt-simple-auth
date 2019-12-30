<template>
  <div class="h-full flex flex-col font-sans antialiased bg-gray-200 text-gray-800">
    <AppHeader class="flex-shrink-0" />
    <nuxt class="flex-1" />
    <AppFooter class="flex-shrink-0" />
  </div>
</template>

<script>
import AppHeader from '~/components/AppHeader.vue'
import AppFooter from '~/components/AppFooter.vue'
export default {
  components: {
    AppHeader,
    AppFooter
  }
}
</script>

<style>
  html,
  body,
  #__nuxt,
  #__layout {
    @apply h-full;
  }
</style>
