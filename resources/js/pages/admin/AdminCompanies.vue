<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import AdminNav from '@/components/AdminNav.vue'

interface User {
    id: number
    name: string
    email: string
}

interface Company {
    id: number
    name: string
    description: string | null
    website: string | null
    owner_id: number | null
    owner?: User | null
}

const companies = ref<Company[]>([])
const users = ref<User[]>([])

const loading = ref(true)
const error = ref('')

const search = ref('')

const showCreateForm = ref(false)
const creating = ref(false)

const editingCompany = ref<Company | null>(null)
const updating = ref(false)

const deletingCompanyId = ref<number | null>(null)

const name = ref('')
const description = ref('')
const website = ref('')
const ownerId = ref('')

const filteredCompanies = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) {
        return companies.value
    }

    return companies.value.filter(company =>
        company.name.toLowerCase().includes(query) ||
        company.owner?.name.toLowerCase().includes(query)
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

        const [companiesResponse, usersResponse] =
            await Promise.all([
                fetch('/api/admin/companies', { headers }),
                fetch('/api/admin/users', { headers }),
            ])

        if (!companiesResponse.ok || !usersResponse.ok) {
            throw new Error('Unable to load companies.')
        }

        companies.value = await companiesResponse.json()
        users.value = await usersResponse.json()
    } catch (err) {
        error.value = err instanceof Error
            ? err.message
            : 'Unable to load companies.'
    } finally {
        loading.value = false
    }
}

function resetForm() {
    name.value = ''
    description.value = ''
    website.value = ''
    ownerId.value = ''
}

function closeCreateForm() {
    showCreateForm.value = false
    resetForm()
}

function openEditForm(company: Company) {
    showCreateForm.value = false
    editingCompany.value = company

    name.value = company.name
    description.value = company.description ?? ''
    website.value = company.website ?? ''
    ownerId.value = company.owner_id
        ? String(company.owner_id)
        : ''

    error.value = ''
}

function closeEditForm() {
    editingCompany.value = null
    resetForm()
}

async function createCompany() {
    creating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch('/api/admin/companies', {
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
                owner_id: ownerId.value
                    ? Number(ownerId.value)
                    : null,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to create company.'
            return
        }

        companies.value.push(data)

        closeCreateForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        creating.value = false
    }
}

async function updateCompany() {
    if (!editingCompany.value) {
        return
    }

    updating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch(
            `/api/admin/companies/${editingCompany.value.id}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                    name: name.value,
                    description: description.value || null,
                    website: website.value || null,
                    owner_id: ownerId.value
                        ? Number(ownerId.value)
                        : null,
                }),
            }
        )

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to update company.'
            return
        }

        const index = companies.value.findIndex(
            company => company.id === editingCompany.value?.id
        )

        if (index !== -1) {
            companies.value[index] = data
        }

        closeEditForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        updating.value = false
    }
}

async function deleteCompany(company: Company) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${company.name}"?`
    )

    if (!confirmed) {
        return
    }

    deletingCompanyId.value = company.id
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch(
            `/api/admin/companies/${company.id}`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        if (!response.ok) {
            let message = 'Unable to delete company.'

            try {
                const data = await response.json()
                message = data.message ?? message
            } catch {
                //
            }

            error.value = message
            return
        }

        companies.value = companies.value.filter(
            existingCompany => existingCompany.id !== company.id
        )

        if (editingCompany.value?.id === company.id) {
            closeEditForm()
        }
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        deletingCompanyId.value = null
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
                        Companies
                    </h1>

                    <p class="mt-3 text-gray-400">
                        Manage platform companies.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateForm = true; editingCompany = null; resetForm()"
                    class="border border-gray-500 bg-white px-4 py-2 text-sm font-medium text-black transition hover:bg-gray-200"
                >
                    Add company
                </button>
            </div>

            <!-- Search -->

            <div class="mt-8">
                <label
                    for="company-search"
                    class="mb-2 block text-sm text-gray-300"
                >
                    Search companies
                </label>

                <input
                    id="company-search"
                    v-model="search"
                    type="search"
                    placeholder="Search by name or owner..."
                    class="w-full border border-gray-700 bg-gray-950 px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white"
                />
            </div>

            <!-- Create company -->

            <div
                v-if="showCreateForm"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Add company
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Create a new platform company.
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
                    @submit.prevent="createCompany"
                    class="mt-6 grid gap-5 md:grid-cols-2"
                >
                    <div>
                        <label
                            for="company-name"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="company-name"
                            v-model="name"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="company-owner"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Owner
                        </label>

                        <select
                            id="company-owner"
                            v-model="ownerId"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value="">
                                No owner
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
                            for="company-website"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Website
                        </label>

                        <input
                            id="company-website"
                            v-model="website"
                            type="url"
                            placeholder="https://example.com"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="company-description"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Description
                        </label>

                        <input
                            id="company-description"
                            v-model="description"
                            type="text"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            :disabled="creating"
                            class="border border-gray-500 bg-white px-5 py-3 text-sm font-medium text-black transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ creating ? 'Creating...' : 'Create company' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit company -->

            <div
                v-if="editingCompany"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Edit company
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Update {{ editingCompany.name }}.
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
                    @submit.prevent="updateCompany"
                    class="mt-6 grid gap-5 md:grid-cols-2"
                >
                    <div>
                        <label
                            for="edit-company-name"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="edit-company-name"
                            v-model="name"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="edit-company-owner"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Owner
                        </label>

                        <select
                            id="edit-company-owner"
                            v-model="ownerId"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        >
                            <option value="">
                                No owner
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
                            for="edit-company-website"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Website
                        </label>

                        <input
                            id="edit-company-website"
                            v-model="website"
                            type="url"
                            placeholder="https://example.com"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="edit-company-description"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Description
                        </label>

                        <input
                            id="edit-company-description"
                            v-model="description"
                            type="text"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
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

            <!-- Companies table -->

            <div class="mt-10">
                <p
                    v-if="loading"
                    class="text-gray-400"
                >
                    Loading companies...
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
                        <span>Owner</span>
                        <span>Actions</span>
                    </div>

                    <div
                        v-for="company in filteredCompanies"
                        :key="company.id"
                        class="grid grid-cols-[80px_1fr_1fr_150px] border-b border-gray-800 px-5 py-5 last:border-b-0"
                    >
                        <span class="text-gray-500">
                            {{ company.id }}
                        </span>

                        <span class="font-medium">
                            {{ company.name }}
                        </span>

                        <span class="text-gray-400">
                            {{ company.owner?.name ?? '—' }}
                        </span>

                        <div class="flex items-center gap-4">
                            <button
                                type="button"
                                @click="openEditForm(company)"
                                class="text-sm text-gray-300 transition hover:text-white"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="deleteCompany(company)"
                                :disabled="deletingCompanyId === company.id"
                                class="text-sm text-red-400 transition hover:text-red-300 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    deletingCompanyId === company.id
                                        ? 'Deleting...'
                                        : 'Delete'
                                }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="filteredCompanies.length === 0"
                        class="px-5 py-8 text-center text-gray-500"
                    >
                        {{
                            search.trim()
                                ? 'No companies match your search.'
                                : 'No companies found.'
                        }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>