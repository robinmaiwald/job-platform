<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import AdminNav from '@/components/AdminNav.vue'

interface Company {
    id: number
    name: string
}

interface User {
    id: number
    name: string
    email: string
}

interface Job {
    id: number
    company_id: number
    user_id: number 
    title: string
    description: string | null
    location: string | null
    company?: Company
    user?: User
}

const jobs = ref<Job[]>([])
const companies = ref<Company[]>([])
const users = ref<User[]>([])

const loading = ref(true)
const error = ref('')

const search = ref('')

const showCreateForm = ref(false)
const creating = ref(false)

const editingJob = ref<Job | null>(null)
const updating = ref(false)

const deletingJobId = ref<number | null>(null)

const title = ref('')
const companyId = ref('')
const userId = ref('')
const description = ref('')
const location = ref('')

const filteredJobs = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) {
        return jobs.value
    }

    return jobs.value.filter(job =>
        job.title.toLowerCase().includes(query) ||
        job.company?.name.toLowerCase().includes(query)
    )
})

async function loadData() {
    loading.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const headers = {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
        }

        const [jobsResponse, companiesResponse, usersResponse] =
            await Promise.all([
                fetch('/api/admin/jobs', { headers }),
                fetch('/api/admin/companies', { headers }),
                fetch('/api/admin/users', { headers }),
            ])

        if (
            !jobsResponse.ok ||
            !companiesResponse.ok ||
            !usersResponse.ok
        ) {
            throw new Error('Unable to load jobs.')
        }

        const jobsData = await jobsResponse.json()
        const companiesData = await companiesResponse.json()
        const usersData = await usersResponse.json()

        jobs.value = jobsData.data
        companies.value = companiesData.data
        users.value = usersData.data

    } catch (err) {
        error.value = err instanceof Error
            ? err.message
            : 'Unable to load jobs.'
    } finally {
        loading.value = false
    }
}

function resetForm() {
    title.value = ''
    companyId.value = ''
    userId.value = ''
    description.value = ''
    location.value = ''
}

function closeCreateForm() {
    showCreateForm.value = false
    resetForm()
}

function openEditForm(job: Job) {
    showCreateForm.value = false
    editingJob.value = job

    title.value = job.title
    companyId.value = String(job.company_id)
    userId.value = String(job.user_id)
    description.value = job.description ?? ''
    location.value = job.location ?? ''

    error.value = ''
}

function closeEditForm() {
    editingJob.value = null
    resetForm()
}

async function createJob() {
    creating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch('/api/admin/jobs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                title: title.value,
                company_id: Number(companyId.value),
                user_id: Number(userId.value),
                description: description.value || null,
                location: location.value || null,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to create job.'
            return
        }

        jobs.value.push(data.data)

        closeCreateForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        creating.value = false
    }
}

async function updateJob() {
    if (!editingJob.value) {
        return
    }

    updating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch(
            `/api/admin/jobs/${editingJob.value.id}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                    title: title.value,
                    company_id: Number(companyId.value),
                    user_id: Number(userId.value),
                    description: description.value || null,
                    location: location.value || null,
                }),
            }
        )

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to update job.'
            return
        }

        const index = jobs.value.findIndex(
            job => job.id === editingJob.value?.id
        )

        if (index !== -1) {
            jobs.value[index] = data.data
        }

        closeEditForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        updating.value = false
    }
}

async function deleteJob(job: Job) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${job.title}"?`
    )

    if (!confirmed) {
        return
    }

    deletingJobId.value = job.id
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch(
            `/api/admin/jobs/${job.id}`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        if (!response.ok) {
            let message = 'Unable to delete job.'

            try {
                const data = await response.json()
                message = data.message ?? message
            } catch {
                //
            }

            error.value = message
            return
        }

        jobs.value = jobs.value.filter(
            existingJob => existingJob.id !== job.id
        )

        if (editingJob.value?.id === job.id) {
            closeEditForm()
        }
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        deletingJobId.value = null
    }
}

onMounted(loadData)
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <AdminNav />

        <main class="mx-auto max-w-7xl px-6 py-12">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                        Management
                    </p>

                    <h1 class="mt-3 text-4xl font-semibold">
                        Jobs
                    </h1>

                    <p class="mt-3 text-gray-400">
                        Manage platform jobs.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateForm = true; editingJob = null; resetForm()"
                    class="border border-gray-500 bg-white px-4 py-2 text-sm font-medium text-black transition hover:bg-gray-200"
                >
                    Add job
                </button>
            </div>

            <!-- Search -->

            <div class="mt-8">
                <label
                    for="job-search"
                    class="mb-2 block text-sm text-gray-300"
                >
                    Search jobs
                </label>

                <input
                    id="job-search"
                    v-model="search"
                    type="search"
                    placeholder="Search by name or company..."
                    class="w-full border border-gray-700 bg-gray-950 px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white"
                />
            </div>

            <!-- Create job -->

            <div
                v-if="showCreateForm"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Add job
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Create a new platform job.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeCreateForm"
                        class="text-sm text-gray-400 transition hover:text-white"
                    >
                        Cancel
                    </button>
                </div>

                <form
                    @submit.prevent="createJob"
                    class="mt-6 grid gap-5 md:grid-cols-2"
                >
                    <div>
                        <label
                            for="job-title"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="job-title"
                            v-model="title"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="job-company"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Company
                        </label>

                        <select
                            id="job-company"
                            v-model="companyId"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value="">
                                Select company
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

                    <div>
                        <label
                            for="job-user"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            User
                        </label>

                        <select
                            id="job-user"
                            v-model="userId"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value=""disabled>
                                Select user
                            </option>

                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }} — {{ user.email }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            for="job-location"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Location
                        </label>

                        <input
                            id="job-location"
                            v-model="location"
                            type="text"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="job-description"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Description
                        </label>

                        <textarea
                            id="job-description"
                            v-model="description"
                            rows="4"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            :disabled="creating"
                            class="border border-gray-500 bg-white px-5 py-3 text-sm font-medium text-black transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ creating ? 'Creating...' : 'Create job' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit job -->

            <div
                v-if="editingJob"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Edit job
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Update {{ editingJob.title }}.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="closeEditForm"
                        class="text-sm text-gray-400 transition hover:text-white"
                    >
                        Cancel
                    </button>
                </div>

                <form
                    @submit.prevent="updateJob"
                    class="mt-6 grid gap-5 md:grid-cols-2"
                >
                    <div>
                        <label
                            for="edit-job-title"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="edit-job-title"
                            v-model="title"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="edit-job-company"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Company
                        </label>

                        <select
                            id="edit-job-company"
                            v-model="companyId"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value="">
                                Select company
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

                    <div>
                        <label
                            for="edit-job-user"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            User
                        </label>

                        <select
                            id="edit-job-user"
                            v-model="userId"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value="" disabled>
                                Select user
                            </option>

                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }} — {{ user.email }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            for="edit-job-location"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Location
                        </label>

                        <input
                            id="edit-job-location"
                            v-model="location"
                            type="text"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label
                            for="edit-job-description"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Description
                        </label>

                        <textarea
                            id="edit-job-description"
                            v-model="description"
                            rows="4"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            :disabled="updating"
                            class="border border-gray-500 bg-white px-5 py-3 text-sm font-medium text-black transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ updating ? 'Saving...' : 'Save changes' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Jobs table -->

            <div class="mt-10">
                <p
                    v-if="loading"
                    class="text-gray-400"
                >
                    Loading jobs...
                </p>

                <p
                    v-else-if="error"
                    class="border border-red-900 bg-red-950/40 px-4 py-3 text-sm text-red-300"
                >
                    {{ error }}
                </p>

                <div
                    v-else
                    class="overflow-hidden border border-gray-800"
                >
                    <div class="grid grid-cols-[80px_1fr_1fr_150px] border-b border-gray-800 bg-gray-950 px-5 py-4 text-xs uppercase tracking-wider text-gray-500">
                        <span>ID</span>
                        <span>Name</span>
                        <span>Company</span>
                        <span>Actions</span>
                    </div>

                    <div
                        v-for="job in filteredJobs"
                        :key="job.id"
                        class="grid grid-cols-[80px_1fr_1fr_150px] border-b border-gray-800 px-5 py-5 last:border-b-0"
                    >
                        <span class="text-gray-500">
                            {{ job.id }}
                        </span>

                        <span class="font-medium">
                            {{ job.title }}
                        </span>

                        <span class="text-gray-400">
                            {{ job.company?.name ?? '—' }}
                        </span>

                        <div class="flex items-center gap-4">
                            <button
                                type="button"
                                @click="openEditForm(job)"
                                class="text-sm text-gray-300 transition hover:text-white"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="deleteJob(job)"
                                :disabled="deletingJobId === job.id"
                                class="text-sm text-red-400 transition hover:text-red-300 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    deletingJobId === job.id
                                        ? 'Deleting...'
                                        : 'Delete'
                                }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="filteredJobs.length === 0"
                        class="px-5 py-8 text-center text-gray-500"
                    >
                        {{
                            search.trim()
                                ? 'No jobs match your search.'
                                : 'No jobs found.'
                        }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>