<template>
  <div>
    <!-- Global Header -->
    <HeaderBar />

    <!-- Main Page Content -->
    <main class="p-4">
      <slot />
    </main>
  </div>
</template>

<script setup>
import HeaderBar from './HeaderBar.vue';
import  '../app.js';
</script>
