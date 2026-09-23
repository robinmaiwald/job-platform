<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const name = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function login() {
    error.value = ''
    loading.value = true

    try {
        const response = await fetch('/api/admin/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                name: name.value,
                password: password.value,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Login failed.'
            return
        }

        localStorage.setItem('admin_token', data.token)

        router.visit('/admin')
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-black text-white flex items-center justify-center px-6">
        <div class="w-full max-w-md">
            <div class="mb-10">
                <p class="text-sm uppercase tracking-[0.3em] text-gray-500">
                    Administration
                </p>

                <h1 class="mt-3 text-4xl font-semibold">
                    Admin Login
                </h1>

                <p class="mt-3 text-gray-400">
                    Sign in to manage users, jobs and companies.
                </p>
            </div>

            <form
                @submit.prevent="login"
                class="space-y-5"
            >
                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm text-gray-300"
                    >
                        Name
                    </label>

                    <input
                        id="name"
                        v-model="name"
                        type="text"
                        autocomplete="username"
                        required
                        class="w-full border border-gray-700 bg-gray-950 px-4 py-3 text-white outline-none focus:border-white"
                    />
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm text-gray-300"
                    >
                        Password
                    </label>

                    <input
                        id="password"
                        v-model="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full border border-gray-700 bg-gray-950 px-4 py-3 text-white outline-none focus:border-white"
                    />
                </div>

                <p
                    v-if="error"
                    class="border border-red-900 bg-red-950/40 px-4 py-3 text-sm text-red-300"
                >
                    {{ error }}
                </p>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full border border-gray-500 bg-white px-4 py-3 font-medium text-black transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ loading ? 'Signing in...' : 'Sign in' }}
                </button>
            </form>
            <Link
                href="/entry"
                class="mt-6 block text-center text-sm text-gray-500 transition hover:text-white"
            >
                Back to Jobs Around the Globe
            </Link>
        </div>
    </div>
</template> 