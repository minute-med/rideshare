<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ChatBox from '@/Components/Custom/ChatBox.vue';

const page = usePage()
const user = computed(() => page.props.auth.user)

defineProps({
    bookings: Array
});

const activeTrip = ref({
    driver: {},
    passengers: [],
    messages: [],
})

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
            <div class="col-span-5 pr-4">
                <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li 
                                v-for="trip in bookings" 
                                :key="trip.id" 
                                @click="reloadChat(trip)"
                                class="py-3 sm:py-4"
                                :active="activeTrip && activeTrip.id === trip.id"
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
            <div class="col-span-7">
                <div v-if="activeTrip !== null">
                    <ChatBox 
                        v-if="activeTrip.passengers.length"
                        :tripId="activeTrip.id"
                        :userList="[activeTrip.driver]"
                        :messages="activeTrip.messages"
                        :sender="user"
                    ></ChatBox>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>