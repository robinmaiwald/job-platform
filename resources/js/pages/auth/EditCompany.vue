<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';

const name = ref('');
const description = ref('');
const website = ref('');
const logo = ref<File | null>(null);

const companyId = ref('');
const loading = ref(true);
const submitting = ref(false);
const error = ref('');

async function loadCompany() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in to edit a company.';
        loading.value = false;
        return;
    }

    try {
        const response = await fetch(`/api/companies/${companyId.value}`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Failed to load company.');
        }

        const company = data.data ?? data;

        name.value = company.name ?? '';
        description.value = company.description ?? '';
        website.value = company.website ?? '';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to load company.';
    }
}

async function updateCompany() {
    error.value = '';

    if (!name.value.trim()) {
        error.value = 'Company name is required.';
        return;
    }

    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in to edit a company.';
        return;
    }

    submitting.value = true;

    try {
        const formData = new FormData();

        formData.append('_method', 'PUT');
        formData.append('name', name.value);
        formData.append('description', description.value);
        formData.append('website', website.value);

        if (logo.value) {
            formData.append('logo', logo.value);
        }
        const response = await fetch(`/api/companies/${companyId.value}`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Failed to update company.');
        }

        window.location.href = `/companies/${companyId.value}`;
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to update company.';
    } finally {
        submitting.value = false;
    }
}

onMounted(async () => {
    companyId.value = window.location.pathname.split('/')[2] ?? '';

    await loadCompany();

    loading.value = false;
});
</script>

<template>
    <Head title="Edit Company" />

    <main class="min-h-screen bg-black text-white">
        <AuthNav />

        <section class="mx-auto max-w-3xl px-6 py-16">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                <h1 class="text-4xl font-bold">
                    Edit Company
                </h1>

                <p class="mt-3 text-gray-400">
                    Update your company information.
                </p>

                <p
                    v-if="error"
                    class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
                >
                    {{ error }}
                </p>

                <div
                    v-if="loading"
                    class="mt-10 text-gray-400"
                >
                    Loading company...
                </div>

                <form
                    v-else
                    class="mt-10 space-y-6"
                    @submit.prevent="updateCompany"
                >
                    <div>
                        <label class="block text-sm font-semibold">
                            Name
                        </label>

                        <input
                            v-model="name"
                            type="text"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="Company name"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Description
                        </label>

                        <textarea
                            v-model="description"
                            rows="5"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="Company description"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Website
                        </label>

                        <input
                            v-model="website"
                            type="url"
                            class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            placeholder="https://example.com"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold">
                            Company Logo
                        </label>

                        <input
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="mt-2 block w-full text-sm text-gray-400"
                            @change="logo = ($event.target as HTMLInputElement).files?.[0] ?? null"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="rounded-lg bg-white px-6 py-3 font-semibold disabled:opacity-50"
                        style="color: black;"
                    >
                        {{ submitting ? 'Saving...' : 'Save Changes' }}
                    </button>
                </form>
            </div>
        </section>
    </main>
</template>