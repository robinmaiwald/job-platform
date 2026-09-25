<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import darkPlanet from '@/images/dark-planet.jpg';
</script>

<template>
    <Head title="Jobs Around the Globe" />

    <main class="min-h-screen bg-black text-white">
        <section class="flex min-h-screen flex-col items-center justify-center px-6">

            <!-- Earth -->
            <img
                :src="darkPlanet"
                alt="Earth"
                class="h-[800px] w-[800px] rounded-full object-cover"
            />

            <!-- Main entry -->
            <Link
                href="/entry"
                class="mt-10 text-center text-5xl font-bold tracking-tight transition hover:scale-105"
            >
                Jobs Around the Globe
            </Link>

        </section>  
    </main>
</template>