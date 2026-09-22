<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Guest" />

    <main class="min-h-screen bg-black px-6 text-white">

        <!-- Navigation -->
        <nav class="flex items-center justify-between border-b border-white/10 py-6">
            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <div class="flex items-center gap-4">
                <Link
                    href="/entry"
                    class="rounded-lg bg-black px-4 py-2 font-semibold transition hover:bg-gray-900"
                    style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                >
                    Back
                </Link>

                <Link
                    href="/entry"
                    class="rounded-lg bg-white px-4 py-2 font-semibold"
                    style="color: black;"
                >
                    Login
                </Link>
            </div>
        </nav>

        <!-- Main content -->
        <section class="flex min-h-[80vh] flex-col items-center justify-center">

            <h1 class="text-5xl font-bold">
                Guest
            </h1>

            <p class="mt-4 text-gray-400">
                Search and explore jobs and companies.
            </p>

            <div class="mt-10 flex w-full max-w-md flex-col gap-4">

                <Link
                    href="/jobs"
                    class="rounded-lg bg-white px-6 py-4 text-center font-semibold"
                    style="color: black;"
                >
                    Search Jobs
                </Link>

                <Link
                    href="/companies"
                    class="rounded-lg bg-black px-6 py-4 text-center font-semibold ring-1 ring-white/30 transition hover:bg-gray-900"
                    style="color: white;"
                >
                    Search Companies
                </Link>

            </div>

        </section>

    </main>
</template>