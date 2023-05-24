<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { ref } from 'vue';
import InputAddressAutocomplete from '@/Components/Custom/InputAddressAutocomplete.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    search_results: {
        type: Array,
    }
});

const form = useForm({
    departure_datetime: null,
    departure_coord: {
        lat: null,
        lon: null
    },
    arrival_coord: {
        lat: null,
        lon: null
    },
    passengers: 1
})


const departureAddress = ref('')
const arrivalAddress = ref('')

const departureResults = ref([])
const arrivalResults = ref([])

function submit() {
  form.post(route('search.submit'))
}
</script>

<template>

<AuthenticatedLayout>
    <Head title="Dashboard" />
    <div>
        <form name="search" class="w-full max-w-6xl" @submit.prevent="submit">
            <div class="w-full flex flex-wrap -mx-3 mb-2">
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <label for="from" class="block text-sm font-bold mb-2">From :</label>
                    <InputAddressAutocomplete v-model="form.departure_coord"></InputAddressAutocomplete>
                    <p v-if="form.errors['departure_coord.lat']" class="text-red-500 text-xs italic">{{ form.errors['departure_coord.lat'] }}</p>
                    <p v-if="form.errors['departure_coord.lon']" class="text-red-500 text-xs italic">{{ form.errors['departure_coord.lon'] }}</p>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <label for="to" class="block text-sm font-bold mb-2">To :</label>
                    <InputAddressAutocomplete v-model="form.arrival_coord"></InputAddressAutocomplete>
                    <p v-if="form.errors['arrival_coord.lat']" class="text-red-500 text-xs italic">{{ form.errors['arrival_coord.lat'] }}</p>
                    <p v-if="form.errors['arrival_coord.lon']" class="text-red-500 text-xs italic">{{ form.errors['arrival_coord.lon'] }}</p>
                </div>
                <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                    <label for="departure_datetime" class="block text-sm font-bold mb-2">When</label>
                    <VueDatePicker 
                        v-model="form.departure_datetime"
                        model-type="yyyy-MM-dd HH:mm:ss"
                        format="dd/MM/yyyy HH:mm"
                        :clearable="true"
                    ></VueDatePicker>
                    <p v-if="form.errors.departure_datetime" class="text-red-500 text-xs italic">{{ form.errors.departure_datetime }}</p>
                </div>
                <div class="w-full md:w-1/4 px-3 ">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white">Submit</button>
                </div>
            </div>
            <div>
                <ul role="list" class="divide-y divide-gray-100">
                    <li v-for="trip in search_results">
                        {{ trip.departure_datetime }}
                        <p>{{ trip.departure_addr.split(',')[0] }} To {{ trip.arrival_addr.split(',')[0] }}</p>
                        <p>seats available: {{ trip.vehicle_info.max_seats  - trip.passengers.length }}</p>
                        <Link 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            method="get"
                            as="anchor"
                            :href="route('trip.show', { trip: trip.id})"
                        >View</Link>  
                    </li>
                </ul>
            </div>
        </form>
    </div>
</AuthenticatedLayout>

</template>