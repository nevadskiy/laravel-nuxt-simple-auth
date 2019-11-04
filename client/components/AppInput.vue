<template>
  <div>
    <label :for="id" class="text-gray-600 text-sm font-bold">{{ label }}</label>

    <div class="relative flex items-center">
      <input
        :id="id"
        :value="value"
        v-bind="$attrs"
        :class="inputClasses"
        class="w-full py-2 px-3 pr-10 border-2 border-transparent truncate bg-white text-gray-800 shadow rounded focus:outline-none focus:border-blue-500"
        @input="update"
      >

      <slot name="icon" />
    </div>

    <span v-show="hasError" class="text-red-600 text-sm">{{ errors[0] }}</span>
  </div>
</template>

<script>
export default {
  // class="w-full py-2 px-3 border-2 border-gray-300 bg-gray-300 text-gray-800 rounded focus:outline-none focus:border-blue-400 focus:bg-transparent"

  inheritAttrs: false,

  props: {
    id: {
      type: String,
      required: true
    },

    label: {
      type: String,
      required: true
    },

    value: {
      type: [Number, String],
      default: null
    },

    errors: {
      type: Array,
      default: () => []
    }
  },

  computed: {
    hasError () {
      return this.errors.length
    },

    inputClasses () {
      // return this.hasError ? 'border-red-600' : 'border-gray-400'
      return ''
    }
  },

  methods: {
    update (event) {
      this.$emit('input', event.target.value)
    }
  }
}
</script>
