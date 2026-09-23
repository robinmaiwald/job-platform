<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';



const user = ref<any>(null);
const jobs = ref<any[]>([]);
const companies = ref<any[]>([]);
const loading = ref(true);
const error = ref('');



async function loadNetwork() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        loading.value = false;
        return;
    }

    try {
        const userResponse = await fetch('/api/user', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        if (!userResponse.ok) {
            throw new Error(`User API returned ${userResponse.status}`);
        }

        user.value = await userResponse.json();

        const jobsResponse = await fetch('/api/jobs', {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!jobsResponse.ok) {
            throw new Error(`Jobs API returned ${jobsResponse.status}`);
        }

        const jobsData = await jobsResponse.json();

        const allJobs = Array.isArray(jobsData)
            ? jobsData
            : jobsData.data ?? [];

        jobs.value = allJobs.filter(
            (job: any) => Number(job.user_id) === Number(user.value.id)
        );

        const companiesResponse = await fetch('/api/companies', {
            headers: {
                Accept: 'application/json',
            },
        });

        if (!companiesResponse.ok) {
            throw new Error(
                `Companies API returned ${companiesResponse.status}`
            );
        }

        const companiesData = await companiesResponse.json();

        const allCompanies = Array.isArray(companiesData)
            ? companiesData
            : companiesData.data ?? [];

        companies.value = allCompanies.filter((company: any) => {
            const members = company.users ?? company.members ?? [];

            return members.some(
                (member: any) =>
                    Number(member.id) === Number(user.value.id)
            );
        });
    }  catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to load your network.';
    } finally {
        loading.value = false;
    }
}

async function deleteJob(jobId: number) {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        return;
    }

    if (!confirm('Are you sure you want to delete this job?')) {
        return;
    }

    try {
        const response = await fetch(`/api/jobs/${jobId}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        if (!response.ok) {
            const data = await response.json().catch(() => null);

            throw new Error(
                data?.message ?? `Failed to delete job (${response.status}).`
            );
        }

        jobs.value = jobs.value.filter(
            (job: any) => Number(job.id) !== Number(jobId)
        );
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to delete job.';
    }
}

async function deleteCompany(companyId: number) {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        return;
    }

    if (!confirm('Are you sure you want to delete this company?')) {
        return;
    }

    try {
        const response = await fetch(`/api/companies/${companyId}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        if (!response.ok) {
            const data = await response.json().catch(() => null);

            throw new Error(
                data?.message ?? `Failed to delete company (${response.status}).`
            );
        }

        companies.value = companies.value.filter(
            (company: any) => Number(company.id) !== Number(companyId)
        );
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to delete company.';
    }
}

onMounted(() => {
    loadNetwork();
});
</script>

<template>
    <Head title="Network" />

    <main class="min-h-screen bg-black text-white">
        <AuthNav />

        <section class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                Network
            </p>

            <h1 class="mt-4 text-5xl font-bold">
                Your Network
            </h1>

            <p class="mt-4 text-gray-400">
                Manage the jobs and companies connected to your account.
            </p>

            <p
                v-if="error"
                class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
            >
                {{ error }}
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-2">

                <!-- My Jobs -->
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                    <h2 class="text-2xl font-bold">
                        My Jobs
                    </h2>

                    <p class="mt-3 text-gray-400">
                        View and manage the jobs you have created.
                    </p>

                    <div class="mt-6 space-y-3">
                        <p
                            v-if="loading"
                            class="text-gray-500"
                        >
                            Loading jobs...
                        </p>

                        <p
                            v-else-if="jobs.length === 0"
                            class="text-gray-500"
                        >
                            You haven't created any jobs yet.
                        </p>

                        <div
                            v-for="job in jobs"
                            :key="job.id"
                            class="rounded-xl border border-white/10 bg-black/40 p-4"
                        >
                            <Link
                                :href="`/jobs/${job.id}`"
                                class="block transition hover:opacity-80"
                            >
                                <h3 class="font-semibold">
                                    {{ job.title }}
                                </h3>

                                <p
                                    v-if="job.location"
                                    class="mt-1 text-sm text-gray-400"
                                >
                                    {{ job.location }}
                                </p>
                            </Link>

                            <div class="mt-4 flex gap-3">
                                <Link
                                    :href="`/jobs/${job.id}/edit`"
                                    class="rounded-lg bg-white px-4 py-2 text-sm font-semibold"
                                    style="color: black;"
                                >
                                    Edit
                                </Link>

                                <button
                                    type="button"
                                    @click="deleteJob(job.id)"
                                    class="rounded-lg bg-black px-4 py-2 text-sm font-semibold"
                                    style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Companies -->
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                    <h2 class="text-2xl font-bold">
                        My Companies
                    </h2>

                    <p class="mt-3 text-gray-400">
                        View the companies you are a member of.
                    </p>

                    <div class="mt-6 space-y-3">
                        <p
                            v-if="loading"
                            class="text-gray-500"
                        >
                            Loading companies...
                        </p>

                        <p
                            v-else-if="companies.length === 0"
                            class="text-gray-500"
                        >
                            You are not a member of any companies yet.
                        </p>

                        <div
                            v-for="company in companies"
                            :key="company.id"
                            class="rounded-xl border border-white/10 bg-black/40 p-4"
                        >
                            <Link
                                :href="`/companies/${company.id}`"
                                class="block transition hover:opacity-80"
                            >
                                <h3 class="font-semibold">
                                    {{ company.name }}
                                </h3>

                                <p
                                    v-if="company.description"
                                    class="mt-1 text-sm text-gray-400"
                                >
                                    {{ company.description }}
                                </p>
                            </Link>

                            <div class="mt-4 flex gap-3">
                                <Link
                                    :href="`/companies/${company.id}/edit`"
                                    class="rounded-lg bg-white px-4 py-2 text-sm font-semibold"
                                    style="color: black;"
                                >
                                    Edit
                                </Link>

                                <button
                                    v-if="Number(company.owner_id) === Number(user.id)"
                                    type="button"
                                    @click="deleteCompany(company.id)"
                                    class="rounded-lg bg-black px-4 py-2 text-sm font-semibold"
                                    style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
</template>