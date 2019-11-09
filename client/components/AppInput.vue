<template>
  <div>
    <label :for="id" class="text-gray-600 text-sm">{{ label }}</label>

    <div class="relative flex items-center">
      <input
        :id="id"
        :value="value"
        v-bind="$attrs"
        class="mt-1 w-full py-2 px-3 pr-10 font-light border-2 border-transparent truncate bg-white text-gray-800 shadow rounded focus:outline-none focus:border-blue-400"
        @input="update"
      >

      <slot name="icon" />
    </div>

    <span v-show="hasError" class="inline-block mt-1 text-red-600 text-sm">{{ errors[0] }}</span>
  </div>
</template>

<script>
export default {
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
    }
  },

  methods: {
    update (event) {
      this.$emit('input', event.target.value)
    }
  }
}
</script>
