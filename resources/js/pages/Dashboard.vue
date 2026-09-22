<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <main class="min-h-screen bg-black text-white">

        <!-- Navigation -->
        <nav class="flex items-center justify-between border-b border-white/10 px-6 py-6">

            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <div class="flex items-center gap-4">

                <!-- Search -->
                <div class="relative">
                    <details>
                        <summary
                            class="cursor-pointer list-none rounded-lg bg-black px-4 py-2 font-semibold"
                            style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                        >
                            Search
                        </summary>

                        <div class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-white/10 bg-black p-2">
                            <Link
                                href="/jobs"
                                class="block rounded-md px-4 py-3 hover:bg-white/10"
                            >
                                Jobs
                            </Link>

                            <Link
                                href="/companies"
                                class="block rounded-md px-4 py-3 hover:bg-white/10"
                            >
                                Companies
                            </Link>
                        </div>
                    </details>
                </div>

                <!-- Create -->
                <div class="relative">
                    <details>
                        <summary
                            class="cursor-pointer list-none rounded-lg bg-white px-4 py-2 font-semibold"
                            style="color: black;"
                        >
                            Create
                        </summary>

                        <div class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-white/10 bg-black p-2">
                            <Link
                                href="#"
                                class="block rounded-md px-4 py-3 hover:bg-white/10"
                            >
                                Job
                            </Link>

                            <Link
                                href="#"
                                class="block rounded-md px-4 py-3 hover:bg-white/10"
                            >
                                Company
                            </Link>
                        </div>
                    </details>
                </div>

                <!-- Dashboard -->
                <Link
                    href="/dashboard"
                    class="rounded-lg bg-black px-4 py-2 font-semibold"
                    style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                >
                    Dashboard
                </Link>

                <!-- User -->
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10"
                >
                    U
                </div>

            </div>
        </nav>

        <!-- Dashboard -->
        <section class="mx-auto max-w-6xl px-6 py-16">

            <h1 class="text-4xl font-bold">
                Dashboard
            </h1>

            <p class="mt-3 text-gray-400">
                Manage your account and activity.
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-2">

                <Link
                    href="#"
                    class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                >
                    <h2 class="text-xl font-bold">
                        Profile
                    </h2>

                    <p class="mt-2 text-gray-400">
                        View and manage your profile.
                    </p>
                </Link>

                <Link
                    href="#"
                    class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                >
                    <h2 class="text-xl font-bold">
                        My Jobs
                    </h2>

                    <p class="mt-2 text-gray-400">
                        View the jobs you have created.
                    </p>
                </Link>

                <Link
                    href="#"
                    class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                >
                    <h2 class="text-xl font-bold">
                        My Companies
                    </h2>

                    <p class="mt-2 text-gray-400">
                        View companies you are a member of.
                    </p>
                </Link>

                <Link
                    href="#"
                    class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                >
                    <h2 class="text-xl font-bold">
                        Settings
                    </h2>

                    <p class="mt-2 text-gray-400">
                        Manage your account settings.
                    </p>
                </Link>

            </div>

        </section>
    </main>
</template>