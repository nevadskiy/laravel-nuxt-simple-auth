<template functional>
  <div class="max-w-sm w-full p-8 bg-gray-300 shadow-lg rounded-lg">
    <h2 v-if="$slots.title" class="font-light text-center text-3xl text-blue-500">
      <slot name="title">
        Title
      </slot>
    </h2>

    <slot />

    <p v-if="$slots.footer" class="mt-6 text-center font-light">
      <slot name="footer" />
    </p>
  </div>
</template>
