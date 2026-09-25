<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { isLoggedIn } from '../../lib/auth';

const company = ref<any>(null);
const loading = ref(true);
const error = ref('');
const loggedIn = ref(false);

async function loadCompany() {
    try {
        const companyId = window.location.pathname.split('/')[2];

        const response = await fetch(`/api/companies/${companyId}`);

        if (!response.ok) {
            throw new Error(`API returned ${response.status}`);
        }

        const data = await response.json();

        company.value = data.data ?? data;
    } catch (err) {
        console.error(err);
        error.value = 'Failed to load company.';
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loggedIn.value = isLoggedIn();
    loadCompany();
});
</script>

<template>
    <Head title="Company Details" />

    <main class="min-h-screen bg-black text-white">
        <nav class="flex items-center justify-between border-b border-white/10 px-6 py-6">
            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <Link
                :href="loggedIn ? '/profile' : '/companies'"
                class="rounded-lg bg-black px-4 py-2 font-semibold transition hover:bg-gray-900"
                style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
            >
                Back
            </Link>
        </nav>

        <section class="mx-auto max-w-5xl px-6 py-16">

            <p v-if="loading" class="text-gray-400">
                Loading company...
            </p>

            <div v-else-if="error">
                <p class="text-red-400">
                    {{ error }}
                </p>
            </div>

            <div v-else-if="company">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8">

                    <div class="flex items-start justify-between gap-8">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-gray-500">
                                Company
                            </p>

                            <h1 class="mt-3 text-4xl font-bold">
                                {{ company.name }}
                            </h1>
                        </div>

                        <div
                            class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-white/20 bg-black"
                        >
                            <img
                                v-if="company.logo"
                                :src="`/storage/${company.logo}`"
                                :alt="`${company.name} logo`"
                                class="h-full w-full object-cover"
                            />

                            <span
                                v-else
                                class="text-sm text-gray-500"
                            >
                                Logo
                            </span>
                        </div>
                    </div>

                    <div class="mt-10 border-t border-white/10 pt-8">
                        <p class="text-sm uppercase tracking-wider text-gray-500">
                            Description
                        </p>

                        <p class="mt-4 text-gray-300">
                            {{ company.description || 'No description provided.' }}
                        </p>
                    </div>

                    <div
                        v-if="company.website"
                        class="mt-8"
                    >
                        <p class="text-sm uppercase tracking-wider text-gray-500">
                            Website
                        </p>

                        <p class="mt-3 text-gray-300">
                            {{ company.website }}
                        </p>
                    </div>

                    <div class="mt-10">
                        <Link
                            :href="`/companies/${company.id}/jobs`"
                            class="inline-block rounded-lg bg-white px-6 py-3 font-semibold"
                            style="color: black;"
                        >
                            Jobs
                        </Link>
                    </div>

                </div>
            </div>

            <div v-else>
                <p class="text-gray-400">
                    Company not found.
                </p>
            </div>

        </section>
    </main>
</template>