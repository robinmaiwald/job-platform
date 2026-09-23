<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';

const name = ref('');
const description = ref('');
const website = ref('');

const submitting = ref(false);
const error = ref('');

async function createCompany() {
    error.value = '';

    if (!name.value.trim()) {
        error.value = 'Company name is required.';
        return;
    }

    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in to create a company.';
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch('/api/companies', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                name: name.value,
                description: description.value || null,
                website: website.value || null,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Failed to create company.');
        }

        const company = data.data ?? data;

        window.location.href = `/companies/${company.id}`;
    } catch (err) {
        console.error(err);

        if (err instanceof Error) {
            error.value = err.message;
        } else {
            error.value = 'Failed to create company.';
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <Head title="Create Company" />

    <main class="min-h-screen bg-black text-white">
       <AuthNav />

        <section class="mx-auto max-w-3xl px-6 py-16">

            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">

                <h1 class="text-4xl font-bold">
                    Create Company
                </h1>

                <p class="mt-3 text-gray-400">
                    Create a new company profile.
                </p>

                <p
                    v-if="error"
                    class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
                >
                    {{ error }}
                </p>

                <form
                    class="mt-10 space-y-6"
                    @submit.prevent="createCompany"
                >

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Name
                        </label>

                        <input
                            v-model="name"
                            type="text"
                            placeholder="Company name"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Description
                        </label>

                        <textarea
                            v-model="description"
                            rows="6"
                            placeholder="Describe the company..."
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        ></textarea>
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-semibold">
                            Website
                        </label>

                        <input
                            v-model="website"
                            type="url"
                            placeholder="https://example.com"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white/30"
                        />
                    </div>

                    <!-- Submit -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-lg bg-white px-6 py-3 font-semibold transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                            style="color: black;"
                        >
                            {{ submitting ? 'Creating...' : 'Create Company' }}
                        </button>
                    </div>

                </form>

            </div>

        </section>

    </main>
</template>