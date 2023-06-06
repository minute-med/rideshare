<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import Footer from './Footer.vue';

import SelectLanguage from '@/Components/Custom/SelectLanguage.vue'

const showingNavigationDropdown = ref(false);

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
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('welcome')">
                                        <ApplicationLogo
                                            class="block h-9 w-auto fill-current text-gray-800"
                                        />
                                    </Link>
                                </div>
        
                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                    <NavLink :href="route('welcome')" :active="route().current('welcome')">
                                        {{ $t('nav.home') }}
                                    </NavLink>
                                    <NavLink :href="route('contact')" :active="route().current('contact')">
                                        {{ $t('nav.contact') }}
                                    </NavLink>
                                </div>
                            </div>
                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <!-- language selection -->
                                <SelectLanguage></SelectLanguage>
                                <!-- Login / Register links -->
                                <div class="ml-3 relative">
                                    <div v-if="!$page.props.auth.user" class="sm:fixed sm:top-0 sm:right-0 p-6">
                                            <NavLink
                                                :href="route('login')"
                                                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                                >{{ $t('login') }}</NavLink
                                            >
        
                                            <NavLink
                                                :href="route('register')"
                                                class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                                >{{ $t('register') }}</NavLink
                                            >
                                    </div>
                                    <div v-else class="sm:fixed sm:top-0 sm:right-0 p-6">
                                        <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                                >logout</Link
                                            >
                                    </div>
                                </div>
                            </div>
        
                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button
                                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                >
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{
                                                hidden: showingNavigationDropdown,
                                                'inline-flex': !showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{
                                                hidden: !showingNavigationDropdown,
                                                'inline-flex': showingNavigationDropdown,
                                            }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
        
                    <!-- Responsive Navigation Menu -->
                    <div
                        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                        class="sm:hidden"
                    >
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('welcome')" :active="route().current('welcome')">
                                {{ $t('nav.home') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('contact')" :active="route().current('contact')">
                                {{ $t('nav.contact') }}
                            </ResponsiveNavLink>
                        </div>
        
                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <!-- language selection -->
                            <SelectLanguage></SelectLanguage>
                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('login')"> {{ $t('login') }} </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('register')">{{ $t('register') }}</ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>
                <main>
                    <slot />
                </main>
                <Footer></Footer>
        </div>
    </div>
</template>
