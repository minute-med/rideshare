<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';


import Icon from 'ol/style/Icon';
import Style from 'ol/style/Style';
import { Point } from 'ol/geom';
import Feature from 'ol/Feature';


import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputAddressAutocomplete from '@/Components/Custom/InputAddressAutocomplete.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import decodePolyline from '@/valhalla'

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

const page = usePage()
const valhallaConfig = computed(() => page.props.app_env.valhalla)

const a = axios.create()
delete a.defaults.headers.post;
delete a.defaults.headers.common;

const center = ref([105.01463774982666, 12.358258174708986]);
const projection = ref('EPSG:4326');
const zoom = ref(7);
const rotation = ref(0);
const vectorsource = ref(null);

const tripRouteCoords = ref([])

const filteredVehicleModels = ref([])
const filteredVehicleCategory = ref([])

const form = useForm({
    instant_booking: false,
    departure_datetime: null,
    price: null,
    departure_coord: {
        lat: null,
        lon: null,
        display_name: null
    },
    arrival_coord: {
        lat: null,
        lon: null,
        display_name: null
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

async function drawRoute() {

    const departureCoordFilled = form.departure_coord.lat !== null && form.departure_coord.lon !== null
    const arrivalCoordFilled = form.arrival_coord.lat !== null && form.arrival_coord.lon !== null
    
    if(departureCoordFilled && arrivalCoordFilled) {
        
        const result = await a.post(`${valhallaConfig.value.base_url}/route`, {
            "locations":[
                { "lon":form.arrival_coord.lon, "lat": form.arrival_coord.lat },
                { "lon":form.departure_coord.lon, "lat":form.departure_coord.lat }
            ],
            "costing":"auto",
            "narrative":false
        },
        {
            headers: {
                'Content-Type': 'text/plain'
            },
        })
        const poly = decodePolyline(result.data.trip.legs[0].shape)
        tripRouteCoords.value.splice(0)
        tripRouteCoords.value.push(poly)
    }

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

watch(() => form.arrival_coord, async () => {
    vectorsource.value.source.forEachFeature(function (feature) {
        if (feature.get('name') === 'arrival') {
            vectorsource.value.source.removeFeature(feature)
        }
    })
    addMarker([form.arrival_coord.lon, form.arrival_coord.lat], 'arrival')
    drawRoute();
})

watch(() => form.departure_coord, () => {
    vectorsource.value.source.forEachFeature(function (feature) {
        if (feature.get('name') === 'departure') {
            vectorsource.value.source.removeFeature(feature)
        }
    })
    addMarker([form.departure_coord.lon, form.departure_coord.lat], 'departure')
    drawRoute();
})

function selectVehicleBrand (evt) {
    const brand_id = evt.target.value
    filteredVehicleModels.value.splice(0)
    props.vehicle_models.forEach((vehicleModel) => {
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

                    <div class="flex flex-row my-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="from" class="block text-sm font-bold mb-2">From</label>
                            <InputAddressAutocomplete v-model="form.departure_coord"></InputAddressAutocomplete>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="to" class="block text-sm font-bold mb-2">To</label>
                            <InputAddressAutocomplete v-model="form.arrival_coord"></InputAddressAutocomplete>
                        </div>
                    </div>
                    <div class="flex flex-row my-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="departure_datetime" class="block text-sm font-bold mb-2">Departure Date & time</label>
                            <VueDatePicker id="departure_datetime" v-model="form.departure_datetime"></VueDatePicker>
                            <p v-if="form.errors.departure_datetime" class="text-red-500 text-xs italic">{{ form.errors.departure_datetime }}</p>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block text-sm font-bold mb-2" for="price">
                                price
                            </label>
                            <input type="number" step=".01" id="price" v-model="form.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                            <p v-if="form.errors['price']" class="text-red-500 text-xs italic">{{ form.errors['price'] }}</p>
                        </div>
                    </div>
                    <div class="flex flex-row mt-6 px-3">
                        <div>
                            <label class="block text-sm font-bold mb-2" for="brand">Brand</label>
                            <select id="brand" name="" @change="selectVehicleBrand">
                                <option v-for="brand in vehicle_brands" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>
                        <div class="ml-2" v-if="filteredVehicleModels.length">
                            <label class="block text-sm font-bold mb-2" for="model">Model</label>
                            <select id="model" v-model="form.vehicle_info.model_id" @change="selectVehicleModel">
                                <option selected>&nbsp;</option>
                                <option v-for="model in filteredVehicleModels" :value="model.id">{{ model.name }}</option>
                            </select>
                            <p v-if="form.errors['vehicle_info.model_id']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.model_id'] }}</p>
                        </div>

                        <div class="ml-2" v-if="filteredVehicleCategory.length">
                            <label class="block text-sm font-bold mb-2" for="category">Category</label>
                            <select id="category" v-model="form.vehicle_info.category_id">
                                <option selected>&nbsp;</option>
                                <option v-for="cat in filteredVehicleCategory" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <p v-if="form.errors['vehicle_info.category_id']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.category_id'] }}</p>
                        </div>
                    
                        <div class="ml-2">
                            <label class="block text-sm font-bold mb-2" for="color">
                                Vehicle Color
                            </label>
                            <input 
                                id="color" 
                                v-model="form.vehicle_info.color" 
                                class="shadow appearance-none border border-black rounded w-full py-2 px-3 mt-1 text-gray-700 leading-tight" 
                                type="color"
                            >
                            <p v-if="form.errors['vehicle_info.color']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.color'] }}</p>
                        </div>
                    </div>
                    <div class="flex flex-row my-3">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block text-sm font-bold mb-2" for="grid-first-name">
                                license plate
                            </label>
                            <input v-model="form.vehicle_info.license_plate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" type="text">
                            <p v-if="form.errors['vehicle_info.license_plate']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.license_plate'] }}</p>
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block text-sm font-bold mb-2" for="grid-first-name">
                                Seats available
                            </label>
                            <input v-model="form.vehicle_info.max_seats" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight" type="number" min="1">
                            <p v-if="form.errors['vehicle_info.max_seats']" class="text-red-500 text-xs italic">{{ form.errors['vehicle_info.max_seats'] }}</p>
                        </div>
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

                    <PrimaryButton type="submit">Submit</PrimaryButton>
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
                        >
                        <ol-source-vector ref="vectorsource">
                            <ol-feature>
                                <ol-geom-multi-line-string
                                    :coordinates="tripRouteCoords"
                                ></ol-geom-multi-line-string>
                                <ol-style>
                                    <ol-style-stroke
                                    color="blue"
                                    :width="5"
                                    ></ol-style-stroke>
                                </ol-style>
                            </ol-feature>
                        </ol-source-vector>
                        </ol-vector-layer>

                    </ol-map>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>