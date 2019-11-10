<template>
  <header class="bg-teal-600">
    <div class="container mx-auto">
      <div class="flex py-3">
        <nuxt-link
          :to="{ name: 'index' }"
          class="font-bold text-gray-100"
        >
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
