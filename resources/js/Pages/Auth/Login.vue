<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <!-- Status message -->
    <div v-if="status" class="mb-3 text-success">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <!-- Email -->
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

      <!-- Password -->
      <div class="mb-3">
        <InputLabel for="password" value="Password" class="form-label" />
        <TextInput
          id="password"
          type="password"
          class="form-control"
          v-model="form.password"
          required
          autocomplete="current-password"
        />
        <InputError class="form-text text-danger mt-1" :message="form.errors.password" />
      </div>

      <!-- Remember Me -->
      <div class="mb-3 form-check">
        <Checkbox name="remember" v-model:checked="form.remember" class="form-check-input" />
        <label class="form-check-label">Remember me</label>
      </div>

      <!-- Forgot Password & Submit -->
      <div class="d-flex justify-content-between align-items-center">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-decoration-underline text-muted"
        >
          Forgot your password?
        </Link>

        <PrimaryButton
          class="btn btn-primary"
          :class="{ 'disabled': form.processing }"
          :disabled="form.processing"
        >
          Log in
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
