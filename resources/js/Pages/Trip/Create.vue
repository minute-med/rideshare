<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

import { ref, inject } from 'vue';

import axios from 'axios';
import Icon from 'ol/style/Icon';
import Style from 'ol/style/Style';
import { Point } from 'ol/geom';

const props = defineProps({
    vehicle_brands: {
        type: Array,
    },
    vehicle_models: {
        type: Array,
    },
    vehicle_categories: {
        type: Array,
    }
});

const center = ref([105.01463774982666, 12.358258174708986]);
const projection = ref('EPSG:4326');
const zoom = ref(7);
const rotation = ref(0);
const vectorsource = ref(null);

const Feature = inject("ol-feature");

const departureResults = ref([])
const arrivalResults = ref([])

const filteredVehicleModels = ref([])
const filteredVehicleCategory = ref([])

const form = useForm({
    instant_booking: false,
    departure_datetime: null,
    departure_addr: null,
    arrival_addr: null,
    price: null,
    departure_coord: {
        lat: null,
        lon: null
    },
    arrival_coord: {
        lat: null,
        lon: null
    },
    vehicle_info: {
        model_id: null,
        category_id: null,
        color: null,
        license_plate: null,
        max_seats: 1,
    }
})

function submit() {
  form.post(route('trip.store'))
}

function addMarker(coordinate, name) {
    const iconFeature = new Feature({
        geometry: new Point(coordinate),
        name: name
    });

    const iconStyle = new Style({
        image: new Icon({
            width: 16,
            height: 16,
            anchor: [0.5, 46],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            src: 'images/location-pin.png',
        }),
    });
    iconFeature.setStyle(iconStyle);
    vectorsource.value.source.addFeature(iconFeature);
}

function getResults(term) {
    return axios.get(`http://localhost:8080/search.php?q=${term}`)
}

async function updateDepartureResults (evt) {
    departureResults.value.splice(0)
    const term = evt.target.value
    const response = await getResults(term)
    response.data.forEach(res => departureResults.value.push(res));
}

async function updateArrivalResults (evt) {
    arrivalResults.value.splice(0)
    const term = evt.target.value
    const response = await getResults(term)
    response.data.forEach(res => arrivalResults.value.push(res));
}

function selectArrival(addr) {
    arrivalResults.value.splice(0)
    form.arrival_coord.lat = addr.lat
    form.arrival_coord.lon = addr.lon
    form.arrival_addr = addr.display_name
    vectorsource.value.source.forEachFeature(function (feature) {
        if (feature.get('name') === 'arrival') {
            vectorsource.value.source.removeFeature(feature)
        }
    })
    addMarker([addr.lon, addr.lat], 'arrival')
}

function selectDeparture(addr) {
    departureResults.value.splice(0)
    form.departure_coord.lat = addr.lat
    form.departure_coord.lon = addr.lon
    form.departure_addr = addr.display_name
    
    vectorsource.value.source.forEachFeature(function (feature) {
        if (feature.get('name') === 'departure') {
            vectorsource.value.source.removeFeature(feature)
        }
    })
    addMarker([addr.lon, addr.lat], 'departure')
}

function selectVehicleBrand (evt) {
    const brand_id = evt.target.value
    filteredVehicleModels.value.splice(0)
    props.vehicle_models.forEach((vehicleModel) => {
        // console.log(vehicleModel.vehicle_brand_id)
        if(vehicleModel.vehicle_brand_id === parseInt(brand_id)) {
            filteredVehicleModels.value.push(Object.assign({}, vehicleModel))
        }
    })
}

function selectVehicleModel (evt) {
    console.log(evt.target.value)
    const vehicleModel = props.vehicle_models.find((model) => model.id === parseInt(evt.target.value))
    filteredVehicleCategory.value.splice(0)
    vehicleModel.categories.forEach((cat) => filteredVehicleCategory.value.push(cat))
}

</script>

<template>
    <Head title="Create Trip" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Trip</h2>
        </template>
        <div class="flex mb-4">
            <div class="w-1/2 h-12 m-1">
                <form @submit.prevent="submit">
                            <div class="w-full px-3">
                                <label for="departure_datetime" class="block text-sm font-bold mb-2">Departure Date & time</label>
                                <VueDatePicker id="departure_datetime" v-model="form.departure_datetime"></VueDatePicker>
                                <p v-if="form.errors.departure_datetime" class="text-red-500 text-xs italic">{{ form.errors.departure_datetime }}</p>
                            </div>
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label for="from" class="block text-sm font-bold mb-2">From :</label>
                                <input 
                                type="text"
                                id="from"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                @keyup="updateDepartureResults"
                                v-model="form.departure_addr"
                                />
                                <ul v-if="departureResults.length > 0">
                                    <li v-for="addr in departureResults" @click="selectDeparture(addr)">{{ addr.display_name }}</li>
                                </ul>
                                <p v-if="form.errors['departure_coord.lat']" class="text-red-500 text-xs italic">{{ form.errors['departure_coord.lat'] }}</p>
                                <p v-if="form.errors['departure_coord.lon']" class="text-red-500 text-xs italic">{{ form.errors['departure_coord.lon'] }}</p>
                            </div>
                            <div class="w-full md:w-1/2 px-3">
                                <label for="to" class="block text-sm font-bold mb-2">To :</label>
                                <input 
                                id="to" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight"
                                @keyup="updateArrivalResults"
                                v-model="form.arrival_addr"
                                />
                                <ul v-if="arrivalResults.length > 0">
                                    <li v-for="addr in arrivalResults" @click="selectArrival(addr)">{{ addr.display_name }}</li>
                                </ul>
                                <p v-if="form.errors['arrival_coord.lat']" class="text-red-500 text-xs italic">{{ form.errors['arrival_coord.lat'] }}</p>
                                <p v-if="form.errors['arrival_coord.lon']" class="text-red-500 text-xs italic">{{ form.errors['arrival_coord.lon'] }}</p>
                            </div>
                            <div class="w-full md:w-1/2 py-3 px-3">
                                <label class="block text-sm font-bold mb-2" for="price">
                                    price
                                </label>
                                <input type="number" step=".01" id="price" v-model="form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                                <p v-if="form.errors['price']" class="text-red-500 text-xs italic">{{ form.errors['price'] }}</p>
                            </div>
                            <div class="py-3 px-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                    type="checkbox" 
                                    v-model="form.instant_booking" 
                                    class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Instant Booking</span>
                                </label>
                            </div>

                            <div class="px-3">
                                <select name="" @change="selectVehicleBrand">
                                    <option v-for="brand in vehicle_brands" :value="brand.id">{{ brand.name }}</option>
                                </select>

                            </div>
                            <div class="px-3" v-if="filteredVehicleModels.length">
                                <select v-model="form.vehicle_info.model_id" @change="selectVehicleModel">
                                    <option selected>&nbsp;</option>
                                    <option v-for="model in filteredVehicleModels" :value="model.id">{{ model.name }}</option>
                                </select>
                                <p v-if="form.errors['vehicle_info.model_id']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.model_id'] }}</p>
                            </div>

                            <div class="px-3" v-if="filteredVehicleCategory.length">
                                <select v-model="form.vehicle_info.category_id">
                                    <option selected>&nbsp;</option>
                                    <option v-for="cat in filteredVehicleCategory" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <p v-if="form.errors['vehicle_info.category_id']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.category_id'] }}</p>
                            </div>
                        
                            <div class="px-3">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                                    Vehicle Color
                                </label>
                                <input v-model="form.vehicle_info.color" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" type="color">
                                <p v-if="form.errors['vehicle_info.color']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.color'] }}</p>
                            </div>

                        <div class="px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                                license plate
                            </label>
                            <input v-model="form.vehicle_info.license_plate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" type="text">
                            <p v-if="form.errors['vehicle_info.license_plate']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.license_plate'] }}</p>
                        </div>
                        
                        <div>
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                                Seats available
                            </label>
                            <input v-model="form.vehicle_info.max_seats" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" type="number" min="1">
                            <p v-if="form.errors['vehicle_info.max_seats']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.max_seats'] }}</p>
                        </div>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white">Submit</button>
                </form>
            </div>
            <div class="w-1/2 h-12">
                <div>
                    <ol-map
                    :loadTilesWhileAnimating="true"
                    :loadTilesWhileInteracting="true"
                    style="height: 400px"
                    >
                        <ol-view
                        ref="view"
                        :center="center"
                        :rotation="rotation"
                        :zoom="zoom"
                        :projection="projection"
                        />

                        <ol-tile-layer>
                        <ol-source-osm />
                        </ol-tile-layer>

                        <ol-vector-layer
                        :updateWhileAnimating="true"
                        :updateWhileInteracting="true"
                        title="MARKERS"
                        id="MARKERS"
                        preview="https://raw.githubusercontent.com/MelihAltintas/vue3-openlayers/main/src/assets/star.png"
                        >
                        <ol-source-vector ref="vectorsource">
                        </ol-source-vector>
                        </ol-vector-layer>

                    </ol-map>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>