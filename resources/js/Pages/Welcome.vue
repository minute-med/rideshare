<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import LandingPage from '@/Pages/Landing.vue'

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <GuestLayout>
        <LandingPage></LandingPage>
    </GuestLayout>
</template>

<style>
</style>
