<template>
  <div>
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <p>Welcome to your dashboard!</p>
  </div>
</template>

<script setup>
import DefaultLayout from '@/Layouts/DefaultLayout.vue';

// Attach the layout
defineOptions({
  layout: DefaultLayout,
});
</script>
