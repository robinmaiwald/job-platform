<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Jobs Around the Globe" />

    <main class="min-h-screen bg-black text-white">
        <section class="flex min-h-screen flex-col items-center justify-center px-6">

            <!-- Earth placeholder -->
            <div
                class="flex h-96 w-96 items-center justify-center rounded-full border border-white/20 bg-gray-900"
            >
                <span class="text-gray-500">
                    Earth
                </span>
            </div>

            <!-- Main entry -->
            <Link
                href="/entry"
                class="mt-10 text-center text-5xl font-bold tracking-tight transition hover:scale-105"
            >
                Jobs Around the Globe
            </Link>

        </section>
    </main>
</template>