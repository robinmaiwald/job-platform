<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

const openMenu = ref<string | null>(null);

function toggleMenu(menu: string) {
    openMenu.value = openMenu.value === menu ? null : menu;
}

function isActive(paths: string[]) {
    return paths.some(
        (path) =>
            page.url === path ||
            page.url.startsWith(`${path}/`)
    );
}
</script>

<template>
    <div class="h-[89px]">
        <nav
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between border-b border-white/10 bg-black px-6 py-6"
        >
            <Link
                href="/profile"
                class="text-xl font-bold"
            >
                Jobs Around the Globe
            </Link>

            <div class="flex items-center gap-4">

                <!-- Search -->
                <div class="relative">
                    <button
                        type="button"
                        @click="toggleMenu('search')"
                        class="rounded-lg px-4 py-2 font-semibold"
                        :class="
                            isActive(['/jobs', '/companies'])
                                ? 'bg-white text-black'
                                : 'border border-white/30 bg-black text-white'
                        "
                    >
                        Search
                    </button>

                    <div
                        v-if="openMenu === 'search'"
                        class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-white/10 bg-black p-2"
                    >
                        <Link
                            href="/jobs"
                            class="block rounded-md px-4 py-3 hover:bg-white/10"
                            @click="openMenu = null"
                        >
                            Jobs
                        </Link>

                        <Link
                            href="/companies"
                            class="block rounded-md px-4 py-3 hover:bg-white/10"
                            @click="openMenu = null"
                        >
                            Companies
                        </Link>
                    </div>
                </div>

                <!-- Create -->
                <div class="relative">
                    <button
                        type="button"
                        @click="toggleMenu('create')"
                        class="rounded-lg px-4 py-2 font-semibold"
                        :class="
                            isActive(['/create'])
                                ? 'bg-white text-black'
                                : 'border border-white/30 bg-black text-white'
                        "
                    >
                        Create
                    </button>

                    <div
                        v-if="openMenu === 'create'"
                        class="absolute right-0 z-10 mt-2 w-48 rounded-lg border border-white/10 bg-black p-2"
                    >
                        <Link
                            href="/create/job"
                            class="block rounded-md px-4 py-3 hover:bg-white/10"
                            @click="openMenu = null"
                        >
                            Job
                        </Link>

                        <Link
                            href="/create/company"
                            class="block rounded-md px-4 py-3 hover:bg-white/10"
                            @click="openMenu = null"
                        >
                            Company
                        </Link>
                    </div>
                </div>

                <!-- Network -->
                <Link
                    href="/network"
                    class="rounded-lg px-4 py-2 font-semibold"
                    :class="
                        isActive(['/network'])
                            ? 'bg-white text-black'
                            : 'border border-white/30 bg-black text-white'
                    "
                >
                    Network
                </Link>

                <!-- Profile -->
                <Link
                    href="/profile"
                    class="rounded-lg px-4 py-2 font-semibold"
                    :class="
                        isActive(['/profile'])
                            ? 'bg-white text-black'
                            : 'border border-white/30 bg-black text-white'
                    "
                >
                    Profile
                </Link>

                <!-- Settings -->
                <Link
                    href="/settings"
                    class="rounded-lg px-4 py-2 font-semibold"
                    :class="
                        isActive(['/settings'])
                            ? 'bg-white text-black'
                            : 'border border-white/30 bg-black text-white'
                    "
                >
                    Settings
                </Link>

            </div>
        </nav>
    </div>
</template>