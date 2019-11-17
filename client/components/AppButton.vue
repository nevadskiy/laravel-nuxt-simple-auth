<template>
  <component
    :is="tag"
    :to="to"
    class="relative inline-block py-3 px-4 text-center rounded-full uppercase font-bold text-xs shadow-lg leading-none focus:outline-none focus:shadow-outline"
    :class="classes"
    v-on="$listeners"
  >
    <div :class="{ 'opacity-0' : loading }">
      <slot />
    </div>

    <transition name="fade">
      <div v-if="loading" class="absolute p-1 inset-0 flex items-center justify-center">
        <AppLoader />
      </div>
    </transition>
  </component>
</template>

<script>
import AppLoader from './AppLoader.vue'

export default {
  components: {
    AppLoader
  },

  props: {
    to: {
      type: [String, Object],
      default: null
    },

    loading: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    tag () {
      if (this.to) {
        return 'router-link'
      }

      return 'button'
    },

    classes () {
      return [
        ...this.colorClasses
      ]
    },

    colorClasses () {
      if (this.loading) {
        return ['bg-gradient-blue-550', 'text-gray-200']
      }

      if (this.$attrs.disabled) {
        return ['bg-gray-500', 'text-gray-800']
      }

      return ['bg-gradient-blue-450 hover:bg-gradient-blue-550', 'text-gray-100']
    }
  }
}
</script>

<style lang="scss">
  .bg-gradient-blue-450 {
    background-image: linear-gradient(to right, theme('colors.blue.400'), theme('colors.blue.500'));
  }

  .bg-gradient-blue-550 {
    background-image: linear-gradient(to right, theme('colors.blue.500'), theme('colors.blue.600'));
  }

  .hover\:bg-gradient-blue-550 {
    &:hover {
      background-image: linear-gradient(to right, theme('colors.blue.500'), theme('colors.blue.600'));
    }
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 300ms;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }
</style>
