<template>
  <header class="h-16 flex items-center bg-gray-800 shadow-md">
    <div class="container mx-auto px-4 flex items-center">
      <nuxt-link :to="{ name: 'index' }" class="font-bold text-white">
        Home
      </nuxt-link>

      <nuxt-link
        v-if="$store.getters['auth/auth']"
        :to="{ name: 'dashboard' }"
        class="ml-12 font-bold text-gray-100"
      >
        Dashboard
      </nuxt-link>

      <div class="ml-auto">
        <nuxt-link
          v-if="$store.getters['auth/guest']"
          :to="{ name: 'auth-signin' }"
          class="font-bold text-gray-100"
        >
          Sign in
        </nuxt-link>

        <nuxt-link
          v-if="$store.getters['auth/guest']"
          :to="{ name: 'auth-signup' }"
          class="ml-12 font-bold text-gray-100"
        >
          Sign up
        </nuxt-link>

        <button
          v-if="$store.getters['auth/auth']"
          class="ml-12 font-bold text-gray-100"
          @click.prevent="signOut"
        >
          Sign out
        </button>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  methods: {
    async signOut () {
      try {
        await this.$store.dispatch('auth/signOut')
      } catch (e) {
      }
    }
  }
}
</script>
