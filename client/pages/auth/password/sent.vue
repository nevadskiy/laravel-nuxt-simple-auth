<template functional>
  <div class="flex justify-center items-center">
    <AppAuthCard>
      <template v-slot:title>
        Done!
      </template>

      <template>
        <p class="mt-4 px-2 text-center font-light text-gray-800">
          We've emailed your password reset link
        </p>

        <AppButton :to="{ name: 'index' }" class="mt-8 w-full">
          Go to home page
        </AppButton>
      </template>
    </AppAuthCard>
  </div>
</template>

<script>
import AppAuthCard from '~/components/partials/AppAuthCard.vue'
import AppButton from '~/components/AppButton.vue'

export default {
  layout: 'auth',

  transition: {
    name: 'slide-left',
    mode: 'out-in'
  },

  components: {
    AppAuthCard,
    AppButton
  }
}
</script>
