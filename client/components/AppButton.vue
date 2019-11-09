<template>
  <component
    :is="tag"
    :to="to"
    class="inline-block py-3 px-4 rounded-full uppercase font-bold text-xs shadow-lg bg-gradient-blue text-gray-100 leading-none focus:outline-none focus:shadow-outline"
    @click="$emit('click')"
  >
    <slot />
  </component>
</template>

<script>
export default {
  props: {
    to: {
      type: [String, Object],
      default: null
    }
  },

  data () {
    return {}
  },

  computed: {
    tag () {
      if (this.to) {
        return 'router-link'
      }

      return 'button'
    }
  }
}
</script>

<style scoped>
  .bg-gradient-blue {
    background-image: linear-gradient(to right, theme('colors.blue.400'), theme('colors.blue.500'));
  }

  .bg-gradient-blue:hover {
    background-image: linear-gradient(to right, theme('colors.blue.500'), theme('colors.blue.600'));
  }
</style>
