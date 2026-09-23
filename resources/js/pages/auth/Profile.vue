<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthNav from '../../components/AuthNav.vue';

const user = ref<any>(null);
const loading = ref(true);

async function loadUser() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
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

        if (!response.ok) {
            throw new Error(`API returned ${response.status}`);
        }

        user.value = await response.json();
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadUser();
});
</script>

<template>
    <Head title="Profile" />

    <main class="min-h-screen bg-black text-white">
        <AuthNav />

        <section class="mx-auto max-w-5xl px-6 py-16">
            <p class="text-sm uppercase tracking-[0.25em] text-gray-500">
                Profile
            </p>

            <h1 class="mt-4 text-5xl font-bold">
                Your Profile
            </h1>

            <p class="mt-4 max-w-2xl text-lg text-gray-400">
                Manage your profile and access your activity across Jobs Around the Globe.
            </p>

            <div class="mt-10 rounded-2xl border border-white/10 bg-white/5 p-8">
                <div class="flex items-center gap-6">
                    <div
                        class="flex h-24 w-24 items-center justify-center rounded-full border border-white/20 bg-white/10 text-2xl font-bold"
                    >
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold">
                            {{ user?.name ?? 'Loading...' }}
                        </h2>

                        <p class="mt-2 text-gray-400">
                            {{ user?.email ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>