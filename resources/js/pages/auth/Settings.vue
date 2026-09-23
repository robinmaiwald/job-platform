<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';

const name = ref('');
const email = ref('');

const loading = ref(true);
const saving = ref(false);
const error = ref('');
const success = ref('');

const password = ref('');
const passwordConfirmation = ref('');

const changingPassword = ref(false);
const deletingAccount = ref(false);

async function loadUser() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        loading.value = false;
        return;
    }

    try {
        const response = await fetch('/api/user', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message ?? 'Failed to load account.');
        }

        name.value = data.name ?? '';
        email.value = data.email ?? '';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to load account.';
    } finally {
        loading.value = false;
    }
}

async function updateAccount() {
    error.value = '';
    success.value = '';

    if (!name.value.trim()) {
        error.value = 'Name is required.';
        return;
    }

    if (!email.value.trim()) {
        error.value = 'Email is required.';
        return;
    }

    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        return;
    }

    saving.value = true;

    try {
        const userResponse = await fetch('/api/user', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const userData = await userResponse.json();

        if (!userResponse.ok) {
            throw new Error(
                userData.message ?? 'Failed to load account.'
            );
        }

        const response = await fetch(`/api/users/${userData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ?? 'Failed to update account.'
            );
        }

        success.value = 'Account updated successfully.';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to update account.';
    } finally {
        saving.value = false;
    }
}

async function changePassword() {
    error.value = '';
    success.value = '';

    if (!password.value) {
        error.value = 'New password is required.';
        return;
    }

    if (password.value.length < 8) {
        error.value = 'Password must be at least 8 characters.';
        return;
    }

    if (password.value !== passwordConfirmation.value) {
        error.value = 'Passwords do not match.';
        return;
    }

    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        return;
    }

    changingPassword.value = true;

    try {
        const userResponse = await fetch('/api/user', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const userData = await userResponse.json();

        if (!userResponse.ok) {
            throw new Error(
                userData.message ?? 'Failed to load account.'
            );
        }

        const response = await fetch(`/api/users/${userData.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message ?? 'Failed to change password.'
            );
        }

        password.value = '';
        passwordConfirmation.value = '';

        success.value = 'Password changed successfully.';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to change password.';
    } finally {
        changingPassword.value = false;
    }
}

function logout() {
    localStorage.removeItem('auth_token');
    window.location.href = '/entry';
}

async function deleteAccount() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
        error.value = 'You must be logged in.';
        return;
    }

    if (
        !confirm(
            'Are you sure you want to delete your account? This cannot be undone.'
        )
    ) {
        return;
    }

    deletingAccount.value = true;
    error.value = '';

    try {
        const userResponse = await fetch('/api/user', {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const userData = await userResponse.json();

        if (!userResponse.ok) {
            throw new Error(
                userData.message ?? 'Failed to load account.'
            );
        }

        const response = await fetch(`/api/users/${userData.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });

        const data = await response.json().catch(() => null);

        if (!response.ok) {
            throw new Error(
                data?.message ?? 'Failed to delete account.'
            );
        }

        localStorage.removeItem('auth_token');
        window.location.href = '/entry';
    } catch (err) {
        console.error(err);

        error.value = err instanceof Error
            ? err.message
            : 'Failed to delete account.';
    } finally {
        deletingAccount.value = false;
    }
}

onMounted(() => {
    loadUser();
});
</script>

<template>
    <Head title="Settings" />

    <main class="min-h-screen bg-black text-white">
        <AuthNav />

        <section class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                Settings
            </p>

            <h1 class="mt-4 text-5xl font-bold">
                Settings
            </h1>

            <p class="mt-4 text-gray-400">
                Manage your account and application preferences.
            </p>

            <div class="mt-10 space-y-6">
                <!-- Account -->
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                    <h2 class="text-2xl font-bold">
                        Account
                    </h2>

                    <p class="mt-3 text-gray-400">
                        Manage your account information.
                    </p>

                    <p
                        v-if="error"
                        class="mt-6 rounded-lg border border-red-500/20 bg-red-500/10 px-4 py-3 text-red-400"
                    >
                        {{ error }}
                    </p>

                    <p
                        v-if="success"
                        class="mt-6 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-green-400"
                    >
                        {{ success }}
                    </p>

                    <form
                        v-if="!loading"
                        class="mt-8 space-y-6"
                        @submit.prevent="updateAccount"
                    >
                        <div>
                            <label class="block text-sm font-semibold">
                                Name
                            </label>

                            <input
                                v-model="name"
                                type="text"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold">
                                Email
                            </label>

                            <input
                                v-model="email"
                                type="email"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="saving"
                            class="rounded-lg bg-white px-6 py-3 font-semibold disabled:opacity-50"
                            style="color: black;"
                        >
                            {{ saving ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </form>

                    <p
                        v-else
                        class="mt-8 text-gray-400"
                    >
                        Loading account...
                    </p>
                </div>

                <!-- Security -->
                <div class="rounded-2xl border border-white/10 bg-white/5 p-8">
                    <h2 class="text-2xl font-bold">
                        Security
                    </h2>

                    <p class="mt-3 text-gray-400">
                        Manage your password and account security.
                    </p>

                    <form
                        class="mt-8 space-y-6"
                        @submit.prevent="changePassword"
                    >
                        <div>
                            <label class="block text-sm font-semibold">
                                New Password
                            </label>

                            <input
                                v-model="password"
                                type="password"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                                placeholder="At least 8 characters"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold">
                                Confirm New Password
                            </label>

                            <input
                                v-model="passwordConfirmation"
                                type="password"
                                class="mt-2 w-full rounded-lg border border-white/10 bg-black px-4 py-3 text-white outline-none focus:border-white/30"
                                placeholder="Repeat your new password"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="changingPassword"
                            class="rounded-lg bg-white px-6 py-3 font-semibold disabled:opacity-50"
                            style="color: black;"
                        >
                            {{ changingPassword ? 'Changing...' : 'Change Password' }}
                        </button>
                    </form>

                    <div class="mt-10 border-t border-white/10 pt-8">
                        <h3 class="text-lg font-semibold">
                            Session
                        </h3>

                        <p class="mt-2 text-gray-400">
                            Sign out of your current account.
                        </p>

                        <button
                            type="button"
                            @click="logout"
                            class="mt-5 rounded-lg bg-black px-6 py-3 font-semibold"
                            style="color: white; border: 1px solid rgba(255, 255, 255, 0.3);"
                        >
                            Logout
                        </button>
                    </div>

                    <div class="mt-10 border-t border-red-500/20 pt-8">
                        <h3 class="text-lg font-semibold text-red-400">
                            Delete Account
                        </h3>

                        <p class="mt-2 text-gray-400">
                            Permanently delete your account and its access to the platform.
                        </p>

                        <button
                            type="button"
                            @click="deleteAccount"
                            :disabled="deletingAccount"
                            class="mt-5 rounded-lg bg-black px-6 py-3 font-semibold disabled:opacity-50"
                            style="color: #f87171; border: 1px solid rgba(248, 113, 113, 0.4);"
                        >
                            {{ deletingAccount ? 'Deleting...' : 'Delete Account' }}
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>