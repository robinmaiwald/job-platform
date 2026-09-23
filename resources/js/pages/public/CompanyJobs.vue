<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const company = ref<any>(null);
const jobs = ref<any[]>([]);
const loading = ref(true);

const companyId = window.location.pathname.split('/')[2];

async function loadCompanyJobs() {
    try {
        const [companyResponse, jobsResponse] = await Promise.all([
            fetch(`/api/companies/${companyId}`),
            fetch('/api/jobs'),
        ]);

        if (!companyResponse.ok || !jobsResponse.ok) {
            throw new Error('Failed to load company or jobs.');
        }

        const companyData = await companyResponse.json();
        const jobsData = await jobsResponse.json();

        company.value = companyData.data ?? companyData;

        const allJobs = Array.isArray(jobsData)
            ? jobsData
            : jobsData.data ?? [];

        jobs.value = allJobs.filter(
            (job: any) => job.company_id === Number(companyId)
        );
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadCompanyJobs();
});
</script>

<template>
    <Head :title="company?.name ? `${company.name} Jobs` : 'Company Jobs'" />

    <main class="min-h-screen bg-black text-white">

        <nav class="flex items-center justify-between border-b border-white/10 px-6 py-6">
            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <Link
                :href="`/companies/${companyId}`"
                class="rounded-lg bg-black px-4 py-2 font-semibold transition hover:bg-gray-900"
                style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
            >
                Back
            </Link>
        </nav>

        <section class="mx-auto max-w-5xl px-6 py-16">

            <p
                v-if="loading"
                class="text-gray-400"
            >
                Loading jobs...
            </p>

            <div v-else>

                <p class="text-sm uppercase tracking-wider text-gray-500">
                    Jobs at
                </p>

                <h1 class="mt-3 text-4xl font-bold">
                    {{ company?.name ?? 'Company' }}
                </h1>

                <p
                    v-if="jobs.length === 0"
                    class="mt-8 text-gray-400"
                >
                    This company currently has no jobs.
                </p>

                <div
                    v-else
                    class="mt-10 space-y-4"
                >
                    <Link
                        v-for="job in jobs"
                        :key="job.id"
                        :href="`/jobs/${job.id}`"
                        class="block rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                    >
                        <h2 class="text-xl font-bold">
                            {{ job.title }}
                        </h2>

                        <p
                            v-if="job.location"
                            class="mt-2 text-gray-400"
                        >
                            {{ job.location }}
                        </p>

                        <p
                            v-if="job.description"
                            class="mt-3 text-gray-500"
                        >
                            {{ job.description }}
                        </p>
                    </Link>
                </div>

            </div>

        </section>

    </main>
</template>