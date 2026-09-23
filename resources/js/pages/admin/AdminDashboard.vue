<script setup lang="ts">
import AdminNav from '@/components/AdminNav.vue'
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <AdminNav />

        <main class="flex min-h-[calc(100vh-89px)] items-center justify-center px-6">
            <div class="max-w-2xl text-center">
                <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                    Admin
                </p>

                <h2 class="mt-4 text-5xl font-semibold">
                    Welcome
                </h2>

                <p class="mt-5 text-gray-400">
                    Manage users, jobs and companies from the administration area.
                </p>
            </div>
        </main>
    </div>
</template>