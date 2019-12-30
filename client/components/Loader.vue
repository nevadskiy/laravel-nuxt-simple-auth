<template functional>
  <svg class="loader" viewBox="25 25 50 50" xmlns="http://www.w3.org/2000/svg">
    <circle
      class="path stroke-current"
      cx="50"
      cy="50"
      r="20"
      fill="none"
      stroke-width="3"
      stroke-miterlimit="10"
    />
  </svg>
</template>

<style scoped>
  .loader {
    height: 100%;
    width: 100%;
    transform-origin: center center;
    animation: rotate 2s linear infinite;
  }

  .path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    animation: dash 1.5s ease-in-out infinite;
    stroke-linecap: round;
  }

  @keyframes rotate {
    100% {
      transform: rotate(360deg);
    }
  }

  @keyframes dash {
    0% {
      stroke-dasharray: 1, 200;
      stroke-dashoffset: 0;
    }
    50% {
      stroke-dasharray: 89, 200;
      stroke-dashoffset: -35px;
    }
    100% {
      stroke-dasharray: 89, 200;
      stroke-dashoffset: -124px;
    }
  }
</style>
