<template>
  <div v-if="visible" class="alert shadow-sm d-flex justify-content-between align-items-center"
       :class="`alert-${type}`" role="alert">
    <span>{{ message }}</span>
    <button type="button" class="btn-close" @click="close"></button>
  </div>
</template>

<script setup>
import { ref, defineExpose } from "vue";

const visible = ref(false);
const message = ref("");
const type = ref("info");

function show(msg, variant = "info") {
  message.value = msg;
  type.value = variant;
  visible.value = true;
}

function close() {
  visible.value = false;
}

defineExpose({ show, close });
</script>
