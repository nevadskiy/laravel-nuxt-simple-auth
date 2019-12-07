<template>
  <div class="flex justify-center items-center">
    <div class="max-w-sm w-full p-8 bg-gray-300 shadow-lg rounded-lg">
      <h2 class="font-light text-center text-3xl text-blue-500">
        Done!
      </h2>

      <p class="mt-4 px-2 text-center font-light text-gray-800">
        We've emailed your password reset link
      </p>

      <AppButton :to="{ name: 'index' }" class="mt-8 w-full">
        Go to home page
      </AppButton>
    </div>
  </div>
</template>

<script>
import AppButton from '~/components/AppButton.vue'

export default {
  components: {
    AppButton
  },

  layout: 'auth',

  transition: {
    name: 'slide-left',
    mode: 'out-in'
  }
}
</script>
