<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import AdminNav from '@/components/AdminNav.vue'

interface User {
    id: number
    name: string
    email: string
    companies?: {
        id: number
        name: string
    }[]
}

const users = ref<User[]>([])
const loading = ref(true)
const error = ref('')

const search = ref('')

const showCreateForm = ref(false)
const creating = ref(false)

const editingUser = ref<User | null>(null)
const updating = ref(false)

const deletingUserId = ref<number | null>(null)

const name = ref('')
const email = ref('')
const password = ref('')

const filteredUsers = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) {
        return users.value
    }

    return users.value.filter(user =>
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query)
    )
})

async function loadUsers() {
    loading.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch('/api/admin/users', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        })

        if (!response.ok) {
            throw new Error('Unable to load users.')
        }

        users.value = await response.json()
    } catch (err) {
        error.value = err instanceof Error
            ? err.message
            : 'Unable to load users.'
    } finally {
        loading.value = false
    }
}

function resetForm() {
    name.value = ''
    email.value = ''
    password.value = ''
}

function closeCreateForm() {
    showCreateForm.value = false
    resetForm()
}

function openEditForm(user: User) {
    showCreateForm.value = false
    editingUser.value = user

    name.value = user.name
    email.value = user.email
    password.value = ''

    error.value = ''
}

function closeEditForm() {
    editingUser.value = null
    resetForm()
}

async function createUser() {
    creating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch('/api/admin/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
                password: password.value,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to create user.'
            return
        }

        users.value.push(data)

        closeCreateForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        creating.value = false
    }
}

async function updateUser() {
    if (!editingUser.value) {
        return
    }

    updating.value = true
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const body: Record<string, string> = {
            name: name.value,
            email: email.value,
        }

        if (password.value) {
            body.password = password.value
        }

        const response = await fetch(
            `/api/admin/users/${editingUser.value.id}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify(body),
            }
        )

        const data = await response.json()

        if (!response.ok) {
            error.value = data.message ?? 'Unable to update user.'
            return
        }

        const index = users.value.findIndex(
            user => user.id === editingUser.value?.id
        )

        if (index !== -1) {
            users.value[index] = {
                ...data,
                companies: users.value[index].companies,
            }
        }

        closeEditForm()
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        updating.value = false
    }
}

async function deleteUser(user: User) {
    const confirmed = window.confirm(
        `Are you sure you want to delete "${user.name}"?`
    )

    if (!confirmed) {
        return
    }

    deletingUserId.value = user.id
    error.value = ''

    try {
        const token = localStorage.getItem('admin_token')

        const response = await fetch(
            `/api/admin/users/${user.id}`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            }
        )

        if (!response.ok) {
            let message = 'Unable to delete user.'

            try {
                const data = await response.json()
                message = data.message ?? message
            } catch {
                //
            }

            error.value = message
            return
        }

        users.value = users.value.filter(
            existingUser => existingUser.id !== user.id
        )

        if (editingUser.value?.id === user.id) {
            closeEditForm()
        }
    } catch {
        error.value = 'Unable to connect to the server.'
    } finally {
        deletingUserId.value = null
    }
}

onMounted(loadUsers)
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
                        Users
                    </h1>

                    <p class="mt-3 text-gray-400">
                        Manage registered platform users.
                    </p>
                </div>

                <button
                    type="button"
                    @click="showCreateForm = true; editingUser = null; resetForm()"
                    class="border border-gray-500 bg-white px-4 py-2 text-sm font-medium text-black transition hover:bg-gray-200"
                >
                    Add user
                </button>
            </div>

            <!-- Search -->

            <div class="mt-8">
                <label
                    for="user-search"
                    class="mb-2 block text-sm text-gray-300"
                >
                    Search users
                </label>

                <input
                    id="user-search"
                    v-model="search"
                    type="search"
                    placeholder="Search by name or email..."
                    class="w-full border border-gray-700 bg-gray-950 px-4 py-3 text-white outline-none placeholder:text-gray-600 focus:border-white"
                />
            </div>

            <!-- Create user -->

            <div
                v-if="showCreateForm"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Add user
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Create a new platform user.
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
                    @submit.prevent="createUser"
                    class="mt-6 grid gap-5 md:grid-cols-3"
                >
                    <div>
                        <label
                            for="user-name"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="user-name"
                            v-model="name"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="user-email"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Email
                        </label>

                        <input
                            id="user-email"
                            v-model="email"
                            type="email"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="user-password"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Password
                        </label>

                        <input
                            id="user-password"
                            v-model="password"
                            type="password"
                            minlength="8"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <button
                            type="submit"
                            :disabled="creating"
                            class="border border-gray-500 bg-white px-5 py-3 text-sm font-medium text-black transition hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ creating ? 'Creating...' : 'Create user' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Edit user -->

            <div
                v-if="editingUser"
                class="mt-8 border border-gray-800 bg-gray-950 p-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium">
                            Edit user
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Update {{ editingUser.name }}.
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
                    @submit.prevent="updateUser"
                    class="mt-6 grid gap-5 md:grid-cols-3"
                >
                    <div>
                        <label
                            for="edit-user-name"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Name
                        </label>

                        <input
                            id="edit-user-name"
                            v-model="name"
                            type="text"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="edit-user-email"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            Email
                        </label>

                        <input
                            id="edit-user-email"
                            v-model="email"
                            type="email"
                            required
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div>
                        <label
                            for="edit-user-password"
                            class="mb-2 block text-sm text-gray-300"
                        >
                            New password
                        </label>

                        <input
                            id="edit-user-password"
                            v-model="password"
                            type="password"
                            minlength="8"
                            placeholder="Leave blank to keep current"
                            class="w-full border border-gray-700 bg-black px-4 py-3 text-white outline-none focus:border-white"
                        />
                    </div>

                    <div class="md:col-span-3">
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

            <!-- Users table -->

            <div class="mt-10">
                <p
                    v-if="loading"
                    class="text-gray-400"
                >
                    Loading users...
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
                    <div class="grid grid-cols-[80px_1fr_1fr_1fr_150px] border-b border-gray-800 bg-gray-950 px-5 py-4 text-xs uppercase tracking-wider text-gray-500">
                        <span>ID</span>
                        <span>Name</span>
                        <span>Email</span>
                        <span>Companies</span>
                        <span>Actions</span>
                    </div>

                    <div
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="grid grid-cols-[80px_1fr_1fr_1fr_150px] border-b border-gray-800 px-5 py-5 last:border-b-0"
                    >
                        <span class="text-gray-500">
                            {{ user.id }}
                        </span>

                        <span class="font-medium">
                            {{ user.name }}
                        </span>

                        <span class="text-gray-300">
                            {{ user.email }}
                        </span>

                        <span class="text-gray-400">
                            {{ user.companies?.length ?? 0 }}
                        </span>

                        <div class="flex items-center gap-4">
                            <button
                                type="button"
                                @click="openEditForm(user)"
                                class="text-sm text-gray-300 transition hover:text-white"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="deleteUser(user)"
                                :disabled="deletingUserId === user.id"
                                class="text-sm text-red-400 transition hover:text-red-300 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{
                                    deletingUserId === user.id
                                        ? 'Deleting...'
                                        : 'Delete'
                                }}
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="filteredUsers.length === 0"
                        class="px-5 py-8 text-center text-gray-500"
                    >
                        {{
                            search.trim()
                                ? 'No users match your search.'
                                : 'No users found.'
                        }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>