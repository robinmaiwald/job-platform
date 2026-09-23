<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');

const submitting = ref(false);
const error = ref('');
const success = ref('');

async function register() {
    error.value = '';
    success.value = '';

    if (!name.value.trim()) {
        error.value = 'Name is required.';
        return;
    }

    if (!email.value.trim()) {
        error.value = 'Email is required.';
        return;
    }

    if (!password.value) {
        error.value = 'Password is required.';
        return;
    }

    if (password.value.length < 8) {
        error.value = 'Password must be at least 8 characters.';
        return;
    }

    if (password.value !== passwordConfirmation.value) {
        error.value = 'Passwords do not match.';
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                const firstError = Object.values(data.errors)[0];

                error.value = Array.isArray(firstError)
                    ? firstError[0]
                    : 'Failed to create account.';
            } else {
                error.value = data.message ?? 'Failed to create account.';
            }

            return;
        }

        success.value = 'Account created successfully.';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to create account.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <Head title="Sign Up" />

    <main class="min-h-screen bg-black px-6 py-16 text-white">
        <section class="mx-auto max-w-xl">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                    Jobs Around the Globe
                </p>

                <h1 class="mt-4 text-4xl font-bold">
                    Create Account
                </h1>

                <p class="mt-3 text-gray-400">
                    Create your account to start using Jobs Around the Globe.
                </p>

                <p
                    v-if="error"
                    class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
                >
                    {{ error }}
                </p>

                <p
                    v-if="success"
                    class="mt-6 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-green-400"
                >
                    {{ success }}
                </p>

                <form
                    v-if="!success"
                    class="mt-8 space-y-6"
                    @submit.prevent="register"
                >
                    <div>
                        <label class="block text-sm font-semibold">
                            Name
                        </label>

                        <input
                            v-model="name"
                            type="text"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="Your name"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Email
                        </label>

                        <input
                            v-model="email"
                            type="email"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="you@example.com"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Password
                        </label>

                        <input
                            v-model="password"
                            type="password"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="At least 8 characters"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Confirm Password
                        </label>

                        <input
                            v-model="passwordConfirmation"
                            type="password"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="Repeat your password"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full rounded-lg bg-white px-6 py-3 font-semibold disabled:opacity-50"
                        style="color: black;"
                    >
                        {{ submitting ? 'Creating Account...' : 'Create Account' }}
                    </button>
                </form>

                <div
                    v-if="success"
                    class="mt-8"
                >
                    <Link
                        href="/login"
                        class="block w-full rounded-lg bg-white px-6 py-3 text-center font-semibold"
                        style="color: black;"
                    >
                        Go to Login
                    </Link>
                </div>

                <div class="mt-8 text-center">
                    <Link
                        href="/entry"
                        class="inline-block rounded-lg bg-black px-6 py-3 font-semibold"
                        style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </section>
    </main>
</template>