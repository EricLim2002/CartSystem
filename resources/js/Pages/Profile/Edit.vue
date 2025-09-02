<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
  <Head title="Profile" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="h4 fw-semibold mb-0">
        Profile
      </h2>
    </template>

    <div class="py-4">
      <div class="container">
        <div class="row g-4">
          <!-- Update Profile Info -->
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="card-body">
                <UpdateProfileInformationForm
                  :must-verify-email="mustVerifyEmail"
                  :status="status"
                  class="w-100 mw-100"
                />
              </div>
            </div>
          </div>

          <!-- Update Password -->
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="card-body">
                <UpdatePasswordForm class="w-100 mw-100" />
              </div>
            </div>
          </div>

          <!-- Delete Account -->
          <div class="col-12">
            <div class="card shadow-sm">
              <div class="card-body">
                <DeleteUserForm class="w-100 mw-100" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
