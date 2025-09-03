<template>
  <div v-if="visible" 
       class="alert alert-floating shadow-sm d-flex justify-content-between align-items-center"
       :class="`alert-${type}`" 
       role="alert">
    <span>{{ message }}</span>
    <button type="button" class="btn-close" @click="close"></button>
  </div>
</template>

<script setup>
import { ref, defineExpose } from "vue";

const visible = ref(false);
const message = ref("");
const type = ref("info");
let timeoutId = null;

function show(msg, variant = "info", duration = 3000) {
  message.value = msg;
  type.value = variant;
  visible.value = true;

  // Clear any previous timer
  if (timeoutId) {
    clearTimeout(timeoutId);
  }

  // Auto-hide after duration
  timeoutId = setTimeout(() => {
    close();
  }, duration);
}

function close() {
  visible.value = false;
}

defineExpose({ show, close });
</script>

<style scoped>
.alert-floating {
  position: fixed;
  top: 80px;
  left: 50%;
  transform: translateX(-50%); /* center horizontally */
  z-index: 1550; /* above modals/backdrop */
  max-width: 500px;
  text-align: center;
}
</style>
