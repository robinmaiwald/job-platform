<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

async function login() {
    error.value = '';
    loading.value = true;

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ?? 'Login failed.'
            );
        }

        localStorage.setItem('auth_token', data.token);

        window.location.href = '/dashboard';
    } catch (err) {
        error.value =
            err instanceof Error
                ? err.message
                : 'Login failed.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Login" />

    <main class="flex min-h-screen items-center justify-center bg-black px-6 text-white">
        <div class="w-full max-w-md">

            <div class="text-center">
                <Link
                    href="/"
                    class="text-2xl font-bold"
                >
                    Jobs Around the Globe
                </Link>

                <h1 class="mt-10 text-4xl font-bold">
                    Login
                </h1>

                <p class="mt-3 text-gray-400">
                    Sign in to your account.
                </p>
            </div>

            <form
                @submit.prevent="login"
                class="mt-10 space-y-6"
            >
                <div>
                    <label class="mb-2 block text-sm font-semibold">
                        Email
                    </label>

                    <input
                        v-model="email"
                        type="email"
                        required
                        class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                    />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold">
                        Password
                    </label>

                    <input
                        v-model="password"
                        type="password"
                        required
                        class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                    />
                </div>

                <p
                    v-if="error"
                    class="text-sm text-red-400"
                >
                    {{ error }}
                </p>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-white px-6 py-3 font-semibold transition hover:bg-gray-200 disabled:opacity-50"
                    style="color: black;"
                >
                    {{ loading ? 'Logging in...' : 'Login' }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <Link
                    href="/entry"
                    class="text-sm text-gray-400 transition hover:text-white"
                >
                    Back
                </Link>
            </div>

        </div>
    </main>
</template>