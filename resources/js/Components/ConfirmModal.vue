<template>
  <div v-if="visible">
    <!-- Modal -->
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ title }}</h5>
            <button type="button" class="btn-close" @click="cancel"></button>
          </div>
          <div class="modal-body">
            <p>{{ message }}</p>
            <small v-if="note" class="text-muted">{{ note }}</small>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="cancel">Cancel</button>
            <button class="btn btn-primary" @click="confirm">Confirm</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Backdrop -->
    <div class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref, defineExpose } from "vue";

const visible = ref(false);
const title = ref("Confirm Action");
const message = ref("Are you sure?");
const note = ref("");

let resolveFn, rejectFn;

function open(options = {}) {
  title.value = options.title || "Confirm Action";
  message.value = options.message || "Are you sure?";
  note.value = options.note || "";
  visible.value = true;

  return new Promise((resolve, reject) => {
    resolveFn = resolve;
    rejectFn = reject;
  });
}

function confirm() {
  visible.value = false;
  resolveFn?.(true);
}

function cancel() {
  visible.value = false;
  rejectFn?.(false);
}

defineExpose({ open });
</script>
