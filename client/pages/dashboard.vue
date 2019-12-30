<template>
  <h1>Secret dashboard for authenticated users only.</h1>
</template>

<script>
export default {
  middleware: [
    'auth'
  ]
}
</script>
