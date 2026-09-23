<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { isLoggedIn } from '../../lib/auth';
import AuthNav from '../../components/AuthNav.vue';

const title = ref('');
const company = ref('');
const location = ref('');

const jobs = ref<any[]>([]);
const companies = ref<any[]>([]);
const loading = ref(false);
const searched = ref(false);

const loggedIn = ref(false);

onMounted(() => {
    loggedIn.value = isLoggedIn();
});

async function loadJobs() {
    loading.value = true;

    try {
        const [jobsResponse, companiesResponse] = await Promise.all([
            fetch('/api/jobs'),
            fetch('/api/companies'),
        ]);

        if (!jobsResponse.ok || !companiesResponse.ok) {
            throw new Error('Failed to load jobs or companies.');
        }

        const jobsData = await jobsResponse.json();
        const companiesData = await companiesResponse.json();

        jobs.value = Array.isArray(jobsData)
            ? jobsData
            : jobsData.data ?? [];

        companies.value = Array.isArray(companiesData)
            ? companiesData
            : companiesData.data ?? [];

        searched.value = true;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function searchJobs() {
    await loadJobs();

    const titleSearch = title.value.toLowerCase().trim();
    const companySearch = company.value.toLowerCase().trim();
    const locationSearch = location.value.toLowerCase().trim();

    jobs.value = jobs.value.filter((job) => {
        const matchesTitle =
            !titleSearch ||
            job.title?.toLowerCase().includes(titleSearch);

        const jobCompany = companies.value.find(
            (company) => company.id === job.company_id
        );

        const matchesCompany =
            !companySearch ||
            jobCompany?.name?.toLowerCase().includes(companySearch);

        const matchesLocation =
            !locationSearch ||
            job.location?.toLowerCase().includes(locationSearch);

        return matchesTitle && matchesCompany && matchesLocation;
    });
}

async function listAll() {
    title.value = '';
    company.value = '';
    location.value = '';

    await loadJobs();
}
</script>

<template>
    <Head title="Search Jobs" />

    <main class="min-h-screen bg-black text-white">

        <!-- Navigation -->
        <AuthNav v-if="loggedIn" />

        <nav
            v-else
            class="flex items-center justify-between border-b border-white/10 px-6 py-6"
        >
            <Link
                href="/"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <Link
                href="/guest"
                class="rounded-lg bg-black px-4 py-2 transition hover:bg-gray-900"
                style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
            >
                Back
            </Link>
        </nav>

        <!-- Search -->
        <section class="mx-auto max-w-6xl px-6 py-16">

            <h1 class="text-4xl font-bold">
                Search Jobs
            </h1>

            <p class="mt-3 text-gray-400">
                Find jobs by title, company or location.
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-3">

                <!-- Job Title -->
                <div>
                    <label class="mb-2 block text-sm font-semibold">
                        Job Title
                    </label>

                    <input
                        v-model="title"
                        @keyup.enter="searchJobs"
                        type="text"
                        placeholder="e.g. Software Engineer"
                        class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                    />
                </div>

                <!-- Company -->
                <div>
                    <label class="mb-2 block text-sm font-semibold">
                        Company
                    </label>

                    <input
                        v-model="company"
                        @keyup.enter="searchJobs"
                        type="text"
                        placeholder="e.g. Example Company"
                        class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                    />
                </div>

                <!-- Location -->
                <div>
                    <label class="mb-2 block text-sm font-semibold">
                        Location
                    </label>

                    <input
                        v-model="location"
                        @keyup.enter="searchJobs"
                        type="text"
                        placeholder="e.g. Berlin"
                        class="w-full rounded-lg border border-white/20 bg-white px-4 py-3 text-black outline-none focus:border-white"
                    />
                </div>

            </div>

            <!-- Buttons -->
            <div class="mt-8 flex gap-4">

                <button
                    type="button"
                    @click="searchJobs"
                    class="rounded-lg bg-white px-6 py-3 font-semibold"
                    style="color: black;"
                >
                    Search
                </button>

                <button
                    type="button"
                    @click="listAll"
                    class="rounded-lg bg-black px-6 py-3 font-semibold ring-1 ring-white/30 transition hover:bg-gray-900"
                    style="color: white;"
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
                    Loading jobs...
                </p>

                <p
                    v-else-if="searched && jobs.length === 0"
                    class="mt-6 text-gray-400"
                >
                    No jobs found.
                </p>

                <div
                    v-else
                    class="mt-6 grid gap-6 md:grid-cols-2"
                >
                    <Link
                        v-for="job in jobs"
                        :key="job.id"
                        :href="`/jobs/${job.id}`"
                        class="rounded-xl border border-white/10 bg-white/5 p-6 transition hover:bg-white/10"
                    >
                        <h3 class="text-xl font-bold">
                            {{ job.title }}
                        </h3>

                        <p class="mt-2 text-gray-400">
                            {{
                                companies.find(company => company.id === job.company_id)?.name
                                ?? 'Company'
                            }}
                        </p>

                        <p
                            v-if="job.location"
                            class="mt-4 text-sm text-gray-500"
                        >
                            {{ job.location }}
                        </p>
                    </Link>
                </div>

            </div>

        </section>

    </main>
</template>