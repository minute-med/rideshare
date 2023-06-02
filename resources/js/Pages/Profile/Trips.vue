<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ChatBox from '@/Components/Custom/ChatBox.vue';

defineProps({
    trips: Array
});

const activeTrip = ref(null)

const Bookingform = useForm({
    passenger_id: null
})

function reloadChat (trip) {
    activeTrip.value = trip
}

function approveBooking (trip, passenger) {
    Bookingform.passenger_id = passenger.id;
    Bookingform.post(route('trip.approve_booking', { trip: trip.id }));
    Bookingform.reset();
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

    .message-item-right {
        display: flex;
        justify-content: right;
    }
    .message-item-left {
        display: flex;
        justify-content: right;
        margin-bottom: 1rem;
    }
</style>

<template>
    <Head title="My Trips" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Trips</h2>
        </template>

        <div class="grid grid-cols-12 py-2">
            <div v-if="trips.length === 0" class="col-span-6">
                No upcoming trip
            </div>
            <div v-else class="col-span-5 pr-4">
                <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li 
                                v-for="trip in trips" 
                                :key="trip.id" 
                                @click="reloadChat(trip)"
                                :class="{'py-3':true, 'sm:py-4':true, 'bg-blue-300': activeTrip && activeTrip.id === trip.id}"
                            >
                                <div class="flex items-center space-x-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            From {{ trip.departure_addr.split(',')[0] }} to {{ trip.arrival_addr.split(',')[0] }}
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
                                            <li v-for="passenger in trip.passengers">
                                                <div class="inline-flex object-left">
                                                    {{ passenger.name }}: 
                                                    <span :class="'status-'.concat(passenger.pivot.status.toLowerCase())">{{ passenger.pivot.status }}</span>
                                                    <PrimaryButton v-if="passenger.pivot.status === 'Pending'" @click="approveBooking(trip, passenger)">Approve</PrimaryButton>
                                                    <PrimaryButton>Cancel</PrimaryButton>
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
                <ChatBox 
                    v-if="activeTrip !== null && activeTrip.passengers.length"
                    :tripId="activeTrip.id"
                    :userList="activeTrip.passengers"
                    :messages="activeTrip.messages"
                    :sender="activeTrip.driver"
                ></ChatBox>
            </div>
        </div>
    </AuthenticatedLayout>
</template>