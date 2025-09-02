<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
  <GuestLayout>
    <Head title="Forgot Password" />

    <!-- Info text -->
    <div class="mb-3 text-muted">
      Forgot your password? No problem. Just let us know your email
      address and we will email you a password reset link that will allow
      you to choose a new one.
    </div>

    <!-- Status message -->
    <div v-if="status" class="mb-3 text-success">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div class="mb-3">
        <InputLabel for="email" value="Email" class="form-label" />

        <TextInput
          id="email"
          type="email"
          class="form-control"
          v-model="form.email"
          required
          autofocus
          autocomplete="username"
        />

        <InputError class="form-text text-danger mt-1" :message="form.errors.email" />
      </div>

      <div class="d-flex justify-content-end">
        <PrimaryButton
          class="btn btn-primary"
          :class="{ 'disabled': form.processing }"
          :disabled="form.processing"
        >
          Email Password Reset Link
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
