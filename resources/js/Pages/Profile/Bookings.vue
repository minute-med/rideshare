<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage()
const user = computed(() => page.props.auth.user)

defineProps({
    bookings: Array
});

const activeTrip = ref(null)

const form = useForm({
    passenger_id: null
})

function reloadChat (trip) {
    activeTrip.value = trip
}

function cancelBooking (trip) {
    form.post(route('trip.user_cancel_booking', { trip: trip.id }));
    form.reset();
}

</script>
<style>
    .status-pending {
        background-color: #94a3b8;
    }
    .status-approved {
        background-color: #4ade80;
    }
    .status-canceled {
        background-color: #be123c;
    }
    .status-user-canceled {
        background-color: #2563eb;
    }
</style>
<template>
    <Head title="My Bookings" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Bookings</h2>
        </template>
        <div class="grid grid-cols-12 py-2">
            <div class="col-span-4">
                <div class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li 
                                v-for="trip in bookings" 
                                :key="trip.id" 
                                @click="reloadChat(trip)"
                                class="py-3 sm:py-4"
                                :active="activeTrip === trip.id"
                            >
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            From {{ trip.departure_addr }} to {{ trip.arrival_addr }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                            {{ trip.departure_datetime }}
                                        </p>
                                    </div>
                                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                        {{ trip.price }} $
                                    </div>
                                </div>
                                <div>
                                    <div v-if="trip.passengers.length">
                                        <div class="py-1">passengers:</div>
                                        <ul>
                                            <li v-for="passenger in trip.passengers" class="py-1">
                                                <div class="inline-flex object-left">
                                                    {{ passenger.name }}: 
                                                    <span :class="'status-'.concat(passenger.pivot.status.toLowerCase())">{{ passenger.pivot.status }}</span>
                                                    <PrimaryButton v-if="passenger.id === user.id" @click="cancelBooking(trip)">Cancel</PrimaryButton>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-else class="py-1">no passenger yet</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-span-8">
                <div v-if="activeTrip !== null" class="container mx-auto shadow-lg rounded-lg">
                    <!-- Chatting -->
                    <div class="flex flex-row justify-between bg-white">
                    <!-- chat list -->
                    <div class="flex flex-col w-1/5 border-r-2 overflow-y-auto">
                        <!-- user list -->
                        <div class="flex flex-row py-4 px-2 justify-center items-center border-b-2">
                            <div class="w-1/4">
                                <img
                                src="https://source.unsplash.com/_7LbC5J-jw4/600x600"
                                class="object-cover h-12 w-12 rounded-full"
                                alt=""
                                />
                            </div>
                            <div class="w-full">
                                <div class="text-lg font-semibold">{{activeTrip.driver.name}}</div>
                                <!-- last msg -->
                                <!-- <span class="text-gray-500">Pick me at 9:00 Am</span> -->
                            </div>
                        </div>
                        <!-- end user list -->
                    </div>
                    <!-- end chat list -->
                    <!-- message -->
                    <div class="w-full px-5 flex flex-col justify-between">
                        <div class="flex flex-col mt-5">
                            <div class="flex justify-end mb-4">
                                <div
                                class="mr-2 py-3 px-4 bg-blue-600 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-black"
                                >
                                Welcome to group everyone !
                                </div>
                                <img
                                src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                                class="object-cover h-8 w-8 rounded-full"
                                alt=""
                                />
                            </div>
                            <div class="flex justify-start mb-4">
                                <img
                                src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                                class="object-cover h-8 w-8 rounded-full"
                                alt=""
                                />
                                <div
                                class="ml-2 py-3 px-4 bg-gray-400 rounded-br-3xl rounded-tr-3xl rounded-tl-xl text-black"
                                >
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat
                                at praesentium, aut ullam delectus odio error sit rem. Architecto
                                nulla doloribus laborum illo rem enim dolor odio saepe,
                                consequatur quas?
                                </div>
                            </div>
                            <div class="flex justify-end mb-4">
                                <div>
                                <div
                                    class="mr-2 py-3 px-4 bg-blue-400 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-black"
                                >
                                    Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                                    Magnam, repudiandae.
                                </div>

                                <div
                                    class="mt-4 mr-2 py-3 px-4 bg-blue-400 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-black"
                                >
                                    Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                    Debitis, reiciendis!
                                </div>
                                </div>
                                <img
                                src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                                class="object-cover h-8 w-8 rounded-full"
                                alt=""
                                />
                            </div>
                            <div class="flex justify-start mb-4">
                                <img
                                src="https://source.unsplash.com/vpOeXr5wmR4/600x600"
                                class="object-cover h-8 w-8 rounded-full"
                                alt=""
                                />
                                <div
                                class="ml-2 py-3 px-4 bg-gray-400 rounded-br-3xl rounded-tr-3xl rounded-tl-xl text-black"
                                >
                                happy holiday guys!
                                </div>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-200 px-4 pt-4 mb-2 sm:mb-0">
                            <div class="relative flex">
                                <input type="text" placeholder="Write your message!" class="w-full focus:outline-none focus:placeholder-gray-400 text-gray-600 placeholder-gray-600 pl-12 bg-gray-200 rounded-md py-3">
                                <div class="absolute right-0 items-center inset-y-0 hidden sm:flex">
                                    <button type="button" class="inline-flex items-center justify-center rounded-lg px-4 py-3 transition duration-500 ease-in-out text-white bg-blue-500 hover:bg-blue-400 focus:outline-none">
                                    <span class="font-bold">Send</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 ml-2 transform rotate-90">
                                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                    </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>