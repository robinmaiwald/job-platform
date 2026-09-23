<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';

const title = ref('');
const companyId = ref('');
const description = ref('');
const location = ref('');

const companies = ref<any[]>([]);
const loading = ref(true);
const submitting = ref(false);
const error = ref('');

async function loadCompanies() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in to create a job.';
        loading.value = false;
        return;
    }

    try {
        const response = await fetch('/api/companies', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        if (!response.ok) {
            throw new Error(`API returned ${response.status}`);
        }

        const data = await response.json();

        companies.value = data.data ?? data;
    } catch (err) {
        console.error(err);
        error.value = 'Failed to load companies.';
    } finally {
        loading.value = false;
    }
}

async function createJob() {
    error.value = '';

    if (!title.value.trim()) {
        error.value = 'Title is required.';
        return;
    }

    if (!companyId.value) {
        error.value = 'Please select a company.';
        return;
    }

    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in to create a job.';
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch('/api/jobs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                title: title.value,
                company_id: Number(companyId.value),
                description: description.value || null,
                location: location.value || null,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Failed to create job.');
        }

        const job = data.data ?? data;

        window.location.href = `/jobs/${job.id}`;
    } catch (err) {
        console.error(err);

        if (err instanceof Error) {
            error.value = err.message;
        } else {
            error.value = 'Failed to create job.';
        }
    } finally {
        submitting.value = false;
    }
}

onMounted(() => {
    loadCompanies();
});
</script>

<template>
    <Head title="Create Job" />

    <main class="min-h-screen bg-black text-white">
        <AuthNav />


        <section class="mx-auto max-w-3xl px-6 py-16">

            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">

                <h1 class="text-4xl font-bold">
                    Create Job
                </h1>

                <p class="mt-3 text-gray-400">
                    Create a new job opportunity.
                </p>

                <p
                    v-if="error"
                    class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
                >
                    {{ error }}
                </p>

                <form
                    class="mt-10 space-y-6"
                    @submit.prevent="createJob"
                >

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Title
                        </label>

                        <input
                            v-model="title"
                            type="text"
                            placeholder="Job title"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        />
                    </div>

                    <!-- Company -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Company
                        </label>

                        <select
                            v-model="companyId"
                            :disabled="loading"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30 disabled:opacity-50"
                        >
                            <option value="">
                                {{ loading ? 'Loading companies...' : 'Select a company' }}
                            </option>

                            <option
                                v-for="company in companies"
                                :key="company.id"
                                :value="company.id"
                            >
                                {{ company.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Description
                        </label>

                        <textarea
                            v-model="description"
                            rows="6"
                            placeholder="Describe the job..."
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        ></textarea>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Location
                        </label>

                        <input
                            v-model="location"
                            type="text"
                            placeholder="e.g. Berlin, Germany"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        />
                    </div>

                    <!-- Submit -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="submitting || loading"
                            class="rounded-lg bg-white px-6 py-3 font-semibold transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                            style="color: black;"
                        >
                            {{ submitting ? 'Creating...' : 'Create Job' }}
                        </button>
                    </div>

                </form>

            </div>

        </section>

    </main>
</template>