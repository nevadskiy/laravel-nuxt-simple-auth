<template>
  <div class="h-full antialiased bg-blue-300 text-gray-800 bg-page-gradient">
    <div class="mx-auto container h-full p-4 flex flex-col">
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
