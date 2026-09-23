<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { isLoggedIn } from '../../lib/auth';

const job = ref<any>(null);
const loading = ref(true);
const loggedIn = ref(false);
const jobId = ref('');

async function loadJob() {
    try {
        const response = await fetch(`/api/jobs/${jobId.value}`);

        if (!response.ok) {
            throw new Error('Failed to load job.');
        }

        const data = await response.json();

        job.value = data.data ?? data;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loggedIn.value = isLoggedIn();
    jobId.value = window.location.pathname.split('/').pop() ?? '';

    loadJob();
});
</script>

<template>
    <Head :title="job?.title ?? 'Job Details'" />

    <main class="min-h-screen bg-black text-white">

        <!-- Navigation -->
        <nav class="flex items-center justify-between border-b border-white/10 px-6 py-6">
            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <Link
                href="/jobs"
                class="rounded-lg bg-black px-4 py-2 font-semibold transition hover:bg-gray-900"
                style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
            >
                Back
            </Link>
        </nav>

        <section class="mx-auto max-w-5xl px-6 py-16">

            <p v-if="loading" class="text-gray-400">
                Loading job...
            </p>

            <div v-else-if="job">

                <!-- Job Header -->
                <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-white/[0.04]">

                    <div class="h-2 bg-white"></div>

                    <div class="flex flex-col gap-8 p-8 md:flex-row md:items-center md:justify-between">

                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-gray-500">
                                Job Opportunity
                            </p>

                            <h1 class="mt-4 text-4xl font-bold tracking-tight md:text-5xl">
                                {{ job.title }}
                            </h1>

                            <p class="mt-4 text-xl text-gray-300">
                                {{ job.company?.name ?? 'Company' }}
                            </p>
                        </div>

                        <!-- Company Logo -->
                        <div
                            class="flex h-32 w-32 shrink-0 items-center justify-center rounded-xl border border-white/15 bg-black text-sm uppercase tracking-widest text-gray-600"
                        >
                            Logo
                        </div>

                    </div>

                </div>

                <!-- Information -->
                <div class="mt-6 grid gap-6 md:grid-cols-2">

                    <!-- Description -->
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-7 md:col-span-2">

                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">
                            Description
                        </p>

                        <p class="mt-5 whitespace-pre-line text-lg leading-8 text-gray-300">
                            {{ job.description || 'No description provided.' }}
                        </p>

                    </div>

                    <!-- Location -->
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-7">

                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">
                            Location
                        </p>

                        <p class="mt-4 text-xl font-semibold">
                            {{ job.location || 'Location not specified' }}
                        </p>

                    </div>

                    <!-- Company -->
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-7">

                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-gray-500">
                            Company
                        </p>

                        <p class="mt-4 text-xl font-semibold">
                            {{ job.company?.name ?? 'Company' }}
                        </p>

                    </div>

                </div>

            </div>

            <div
                v-else
                class="rounded-2xl border border-white/10 bg-white/[0.03] p-10 text-center"
            >
                <p class="text-gray-400">
                    Job not found.
                </p>
            </div>

        </section>

    </main>
</template>