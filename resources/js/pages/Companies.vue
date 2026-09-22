<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const company = ref('');
const companies = ref<any[]>([]);
const loading = ref(false);
const searched = ref(false);

async function loadCompanies() {
    loading.value = true;

    try {
        const response = await fetch('/api/companies');

        if (!response.ok) {
            throw new Error('Failed to load companies.');
        }

        const data = await response.json();

        companies.value = Array.isArray(data)
            ? data
            : data.data ?? [];

        searched.value = true;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function searchCompanies() {
    await loadCompanies();

    const companySearch = company.value.toLowerCase().trim();

    companies.value = companies.value.filter((item) =>
        item.name?.toLowerCase().includes(companySearch)
    );
}

async function listAll() {
    company.value = '';

    await loadCompanies();
}
</script>

<template>
    <Head title="Search Companies" />

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
                href="/guest"
                class="rounded-lg bg-black px-4 py-2 font-semibold transition hover:bg-gray-900"
                style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
            >
                Back
            </Link>
        </nav>

        <section class="mx-auto max-w-6xl px-6 py-16">

            <h1 class="text-4xl font-bold">
                Search Companies
            </h1>

            <p class="mt-3 text-gray-400">
                Find companies by name.
            </p>

            <!-- Search -->
            <div class="mt-10 max-w-xl">

                <label class="mb-2 block text-sm font-semibold">
                    Company Name
                </label>

                <input
                    v-model="company"
                    @keyup.enter="searchCompanies"
                    type="text"
                    placeholder="e.g. Example Technologies"
                    class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                />

            </div>

            <!-- Buttons -->
            <div class="mt-6 flex gap-4">

                <button
                    type="button"
                    @click="searchCompanies"
                    class="rounded-lg bg-white px-6 py-3 font-semibold"
                    style="color: black;"
                >
                    Search
                </button>

                <button
                    type="button"
                    @click="listAll"
                    class="rounded-lg bg-black px-6 py-3 font-semibold transition hover:bg-gray-900"
                    style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                >
                    List All
                </button>

            </div>

            <!-- Results -->
            <div class="mt-16">

                <h2 class="text-2xl font-bold">
                    Results
                </h2>

                <p
                    v-if="loading"
                    class="mt-6 text-gray-400"
                >
                    Loading companies...
                </p>

                <p
                    v-else-if="searched && companies.length === 0"
                    class="mt-6 text-gray-400"
                >
                    No companies found.
                </p>

                <div
                    v-else
                    class="mt-6 grid gap-6 md:grid-cols-2"
                >
                    <Link
                        v-for="item in companies"
                        :key="item.id"
                        :href="`/companies/${item.id}`"
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                    >
                        <h3 class="text-xl font-bold">
                            {{ item.name }}
                        </h3>

                        <p
                            v-if="item.description"
                            class="mt-3 text-gray-400"
                        >
                            {{ item.description }}
                        </p>
                    </Link>
                </div>

            </div>

        </section>

    </main>
</template>