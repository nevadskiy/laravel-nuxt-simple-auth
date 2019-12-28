<template>
  <div class="flex justify-center items-center">
    <div class="max-w-sm w-full p-8 bg-gray-300 shadow-lg rounded-lg">
      <h2 class="font-light text-center text-3xl text-blue-500">
        Forgot your password?
      </h2>

      <form
        class="mt-8"
        @input="form.errors.clear($event.target.id)"
        @submit.prevent="submit"
      >
        <p class="mt-4 px-2 text-center font-light text-gray-700">
          Enter the email address associated with your account and we will email you a link to reset your password
        </p>

        <AppInput
          id="email"
          v-model="form.email"
          :errors="form.errors.get('email')"
          label="Email"
          placeholder="example@mail.com"
          class="mt-6"
        >
          <svg slot="icon" class="w-4 h-4 fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 395 395">
            <polygon points="395,320.089 395,74.911 258.806,197.5" />
            <polygon points="197.5,252.682 158.616,217.682 22.421,340.271 372.579,340.271 236.384,217.682" />
            <polygon points="372.579,54.729 22.421,54.729 197.5,212.318" />
            <polygon points="0,74.911 0,320.089 136.194,197.5" />
          </svg>
        </AppInput>

        <AppButton
          :loading="form.isPending"
          type="submit"
          class="mt-8 w-full"
        >
          Send
        </AppButton>
      </form>
    </div>
  </div>
</template>

<script>
import Form from '~/utils/form/Form'
import AppInput from '~/components/AppInput.vue'
import AppButton from '~/components/AppButton.vue'

export default {
  layout: 'auth',

  transition: {
    name: 'slide-left',
    mode: 'out-in'
  },

  components: {
    AppInput,
    AppButton
  },

  data () {
    return {
      form: new Form({
        email: ''
      })
    }
  },

  methods: {
    async submit () {
      try {
        await this.form.submitUsing(async () => {
          await this.$store.dispatch('auth/forgot', this.form)
          this.$router.push({ name: 'auth-password-sent' })
        })
      } catch (e) {
      }
    }
  }
}
</script>
