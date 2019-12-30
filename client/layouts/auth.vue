<template>
  <div class="h-full flex flex-col font-sans antialiased bg-blue-300 text-gray-800 bg-page-gradient flex flex-col">
    <AppHeader class="flex-shrink-0" />

    <div class="flex-1 container mx-auto px-mi4 py-5">
      <nuxt class="mt-10" />
    </div>
  </div>
</template>

<script>
import AppHeader from '../components/AppHeader.vue'
export default {
  components: {
    AppHeader
  }
}
</script>

<style>
  .bg-page-gradient {
    background-image: linear-gradient(to right bottom, #3672f8, #b01eff);
  }

  .slide-left-enter-active,
  .slide-left-leave-active,
  .slide-right-enter-active,
  .slide-right-leave-active {
    transition-duration: 0.250s;
    transition-property: height, opacity, transform;
    transition-timing-function: cubic-bezier(0.55, 0, 0.1, 1);
  }

  .slide-left-enter,
  .slide-right-leave-to {
    opacity: 0;
    transform: translate(2em, 0);
  }

  .slide-left-leave-to,
  .slide-right-enter {
    opacity: 0;
    transform: translate(-2em, 0);
  }
</style>
