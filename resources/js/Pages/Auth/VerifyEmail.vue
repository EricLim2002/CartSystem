<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="alert alert-info mb-4">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div v-if="verificationLinkSent" class="alert alert-success mb-4">
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Resend Verification Email</span>
                </button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="btn btn-link text-decoration-underline text-muted"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
