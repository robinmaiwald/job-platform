<script setup lang="ts">
import { router } from '@inertiajs/vue3'

function logout() {
    localStorage.removeItem('admin_token')
    router.visit('/admin/login')
}
</script>

<template>
    <header class="border-b border-gray-800">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <div class="flex items-center gap-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500">
                        Administration
                    </p>

                    <p class="mt-1 text-lg font-semibold">
                        Jobs Around the World
                    </p>
                </div>

                <nav class="hidden items-center gap-6 md:flex">
                    <a
                        href="/admin"
                        class="text-sm text-gray-300 transition hover:text-white"
                    >
                        Home
                    </a>

                    <a
                        href="/admin/users"
                        class="text-sm text-gray-300 transition hover:text-white"
                    >
                        Users
                    </a>

                    <a
                        href="/admin/jobs"
                        class="text-sm text-gray-300 transition hover:text-white"
                    >
                        Jobs
                    </a>

                    <a
                        href="/admin/companies"
                        class="text-sm text-gray-300 transition hover:text-white"
                    >
                        Companies
                    </a>
                </nav>
            </div>

            <button
                type="button"
                @click="logout"
                class="border border-gray-600 bg-black px-4 py-2 text-sm text-white transition hover:bg-white hover:text-black"
            >
                Sign out
            </button>
        </div>
    </header>
</template>