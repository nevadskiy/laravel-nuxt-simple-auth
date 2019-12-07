<template>
  <div class="h-full font-sans antialiased flex flex-col bg-gray-200 text-gray-800">
    <AppHeader />
    <nuxt class="flex-1" />
    <AppFooter />
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
