<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {route} from 'ziggy-js'
const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

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

            <!-- Submit -->
            <div class="d-flex justify-content-end mt-4">
                <PrimaryButton
                    class="btn btn-primary"
                    :class="{ 'disabled': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
