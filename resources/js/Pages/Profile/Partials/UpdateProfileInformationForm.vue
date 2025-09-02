<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import {route} from 'ziggy-js'
defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
  <section>
    <header class="mb-3">
      <h2 class="h5">Profile Information</h2>
      <p class="text-muted small">
        Update your account's profile information and email address.
      </p>
    </header>

    <form @submit.prevent="form.patch(route('profile.update'))" class="mb-3">
      <!-- Name -->
      <div class="mb-3">
        <InputLabel for="name" value="Name" class="form-label" />
        <TextInput
          id="name"
          type="text"
          class="form-control"
          v-model="form.name"
          required
          autofocus
          autocomplete="name"
        />
        <InputError class="form-text text-danger mt-1" :message="form.errors.name" />
      </div>

      <!-- Email -->
      <div class="mb-3">
        <InputLabel for="email" value="Email" class="form-label" />
        <TextInput
          id="email"
          type="email"
          class="form-control"
          v-model="form.email"
          required
          autocomplete="username"
        />
        <InputError class="form-text text-danger mt-1" :message="form.errors.email" />
      </div>

      <!-- Email Verification -->
      <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mb-3">
        <p class="text-muted small">
          Your email address is unverified.
          <Link
            :href="route('verification.send')"
            method="post"
            as="button"
            class="btn btn-link p-0 text-decoration-underline"
          >
            Click here to re-send the verification email.
          </Link>
        </p>

        <div v-show="status === 'verification-link-sent'" class="text-success small mt-1">
          A new verification link has been sent to your email address.
        </div>
      </div>

      <!-- Save Button & Success Message -->
      <div class="d-flex align-items-center gap-2">
        <PrimaryButton class="btn btn-primary" :disabled="form.processing">Save</PrimaryButton>

        <Transition
          enter-active-class="transition-opacity duration-200"
          enter-from-class="opacity-0"
          leave-active-class="transition-opacity duration-200"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-muted small mb-0">Saved.</p>
        </Transition>
      </div>
    </form>
  </section>
</template>
