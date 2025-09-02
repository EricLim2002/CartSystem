<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
  <section>
    <header class="mb-3">
      <h2 class="h5">Update Password</h2>
      <p class="text-muted small">
        Ensure your account is using a long, random password to stay secure.
      </p>
    </header>

    <form @submit.prevent="updatePassword" class="mb-3">
      <!-- Current Password -->
      <div class="mb-3">
        <InputLabel for="current_password" value="Current Password" class="form-label" />
        <TextInput
          id="current_password"
          ref="currentPasswordInput"
          v-model="form.current_password"
          type="password"
          class="form-control"
          autocomplete="current-password"
        />
        <InputError :message="form.errors.current_password" class="form-text text-danger mt-1" />
      </div>

      <!-- New Password -->
      <div class="mb-3">
        <InputLabel for="password" value="New Password" class="form-label" />
        <TextInput
          id="password"
          ref="passwordInput"
          v-model="form.password"
          type="password"
          class="form-control"
          autocomplete="new-password"
        />
        <InputError :message="form.errors.password" class="form-text text-danger mt-1" />
      </div>

      <!-- Confirm Password -->
      <div class="mb-3">
        <InputLabel for="password_confirmation" value="Confirm Password" class="form-label" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="form-control"
          autocomplete="new-password"
        />
        <InputError :message="form.errors.password_confirmation" class="form-text text-danger mt-1" />
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
