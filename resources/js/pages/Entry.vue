<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Get Started" />

    <main class="flex min-h-screen items-center justify-center bg-black px-6 text-white">
        <div class="w-full text-center">

            <h1 class="text-4xl font-bold">
                Jobs Around the Globe
            </h1>

            <p class="mt-4 text-gray-400">
                How would you like to continue?
            </p>

            <div class="mx-auto mt-10 w-3/5 space-y-4">

                <!-- Guest -->
                <Link
                    href="/guest"
                    class="block rounded-lg bg-white px-6 py-3 font-semibold transition hover:bg-gray-200"
                    style="color: black;"
                >
                    Continue as Guest
                </Link>

                <!-- Login -->
                <Link
                    href="/login"
                    class="block rounded-lg bg-black px-6 py-3 font-semibold ring-1 ring-white/30 transition hover:bg-gray-900"
                    style="color: white;"
                >
                    Login
                </Link>

                <!-- Sign Up -->
                <Link
                    href="#"
                    class="block rounded-lg bg-white px-6 py-3 font-semibold transition hover:bg-gray-200"
                    style="color: black;"
                >
                    Sign Up
                </Link>

            </div>

        </div>
    </main>
</template>