<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="mt-3">
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

                <InputError class="text-danger mt-1" :message="form.errors.name" />
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

                <InputError class="text-danger mt-1" :message="form.errors.email" />
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
                    autocomplete="new-password"
                />

                <InputError class="text-danger mt-1" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <InputLabel for="password_confirmation" value="Confirm Password" class="form-label" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="form-control"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="text-danger mt-1"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <Link
                    :href="route('login')"
                    class="text-decoration-underline small text-muted"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="btn btn-primary"
                    :class="{ 'disabled': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
