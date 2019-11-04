<template>
  <div class="flex justify-center items-center">
    <div class="max-w-sm w-full p-8 bg-gray-200 shadow-lg rounded-lg">
      <h2 class="font-bold text-3xl text-blue-500">
        Welcome back
      </h2>

      <p class="mt-1">
        Sign into your account
      </p>

      <form class="mt-10">
        <AppInput
          id="email"
          label="Email"
          placeholder="example@mail.com"
        >
          <svg slot="icon" class="absolute right-0 mr-4 w-4 h-4 fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 395 395">
            <polygon points="395,320.089 395,74.911 258.806,197.5" />
            <polygon points="197.5,252.682 158.616,217.682 22.421,340.271 372.579,340.271 236.384,217.682" />
            <polygon points="372.579,54.729 22.421,54.729 197.5,212.318" />
            <polygon points="0,74.911 0,320.089 136.194,197.5" />
          </svg>
        </AppInput>

        <AppInput
          id="password"
          type="password"
          class="mt-6"
          label="Password"
          placeholder="Your password..."
        >
          <svg slot="icon" class="absolute right-0 mr-4 w-4 h-4 fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <g>
              <path d="M191,155c0-35.841,29.159-65,65-65s65,29.159,65,65v61h90v-61C411,69.533,341.467,0,256,0S101,69.533,101,155v61h90V155z" />
              <path d="M71,246v81c0,102.009,82.991,185,185,185s185-82.991,185-185v-81H71z M271,373.42V416h-30v-42.58c-17.459-6.192-30-22.865-30-42.42c0-24.813,20.187-45,45-45s45,20.187,45,45C301,350.555,288.459,367.228,271,373.42z" />
              <path d="M256,316c-8.271,0-15,6.729-15,15s6.729,15,15,15s15-6.729,15-15S264.271,316,256,316z" />
            </g>
          </svg>
        </AppInput>

        <nuxt-link :to="{ name: 'auth-reset-password' }" class="inline-block mt-2 font-bold">
          Forgot your password?
        </nuxt-link>

        <AppButton class="mt-8 w-full">
          Sign in
        </AppButton>
      </form>

      <p class="mt-6 text-center">
        Don't have an account?
        <nuxt-link :to="{ name: 'auth-signup' }" class="font-bold">
          Sign up
        </nuxt-link>
      </p>
    </div>
  </div>
</template>

<script>
import AppInput from '~/components/AppInput.vue'
import AppButton from '~/components/AppButton.vue'

export default {
  layout: 'auth',

  components: {
    AppInput,
    AppButton
  }
}
</script>
