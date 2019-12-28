<template>
  <header class="h-16 bg-gray-800 shadow-md flex items-center">
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
          @click.prevent="signout"
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
    async signout () {
      try {
        await this.$store.dispatch('auth/signout')
      } catch (e) {
      }
    }
  }
}
</script>
