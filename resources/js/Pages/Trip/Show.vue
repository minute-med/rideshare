<template>
<AuthenticatedLayout>
  <div class="mx-8 my-8 flex">
        <div class="w-1/2">
          <div>
            from {{ trip.departure_addr.split(',')[0] }} to {{ trip.arrival_addr.split(',')[0] }}        
          </div>
          <div >
              <p class="inline-flex">departure: {{ trip.departure_datetime }} &nbsp;</p>
              <p class="inline-flex">available seats : {{ trip.vehicle_info.max_seats - trip.passengers.length }}</p>
          </div>

          vehicle: {{ trip.vehicle_info.model.brand.name }} {{ trip.vehicle_info.model.name }} {{ trip.vehicle_info.category.name }}
          <div class=" inline-flex" :style="{width:'20px', backgroundColor: trip.vehicle_info.color }">&nbsp</div>
        
          <div>driver: {{ trip.driver.name }}</div>
          <div>
            passengers:
            <ul>
              <li v-for="(passenger, index) in trip.passengers">- {{ passenger.name }}</li>
            </ul>
          </div>
          <PrimaryButton 
            v-if="!disabled && trip.instant_booking && trip.driver_id !== user.id"
          >Book</PrimaryButton>
          <PrimaryButton v-else-if="!disabled" @click="open = true">Request booking</PrimaryButton>
        </div>
        <div class="w-1/2">
          <OSMap :routeCoord="tripRoute"></OSMap>
        </div>
  </div>

    <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="open = false">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <ChatBubbleBottomCenterTextIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Request Booking</DialogTitle>
                    <div class="mt-2">
                        <form @submit.prevent="submit">
                            <div>
                                <label for="seats">seats</label>
                                <input v-model="form.seats" type="number" min="1" :max="trip.vehicle_info.max_seats">
                            </div>
                            <div>
                                <label for="message">Message</label>
                                <textarea v-model="form.bookingMessage"></textarea>
                            </div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" :disabled="disabled" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" @click="submit">Request booking</button>
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="open = false" ref="cancelButtonRef">Cancel</button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>


</AuthenticatedLayout>

</template>
<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ChatBubbleBottomCenterTextIcon } from '@heroicons/vue/24/outline'

import OSMap from '@/Components/Custom/OSMap.vue';
import decodePolyLine from '@/valhalla';

const open = ref(false)
const tripRoute = ref([])

const page = usePage()
const user = computed(() => page.props.auth.user)

const props = defineProps({
    trip: {
        type: Object,
    }
});

let found = props.trip.passengers.find(p => p.id === user.value.id) !== undefined
const disabled = ref(found)

const form = useForm({
    bookingMessage: '',
    seats: 1
})

function submit() {
  form.post(route('trip.book',{trip: props.trip.id}))
  open.value = false
}

onMounted(async () => {

  const data = {
    "locations":[
        { "lon":props.trip.arrival_coord.lon, "lat": props.trip.arrival_coord.lat },
        { "lon":props.trip.departure_coord.lon, "lat":props.trip.departure_coord.lat }
    ],
    "costing":"auto",
    "narrative":false
  };

    const response = await fetch('http://localhost:8002/route', {
    method: "POST",
    headers: {
      "Content-Type": "text/plain",
    },
    body: JSON.stringify(data),
  });

  response.json().then(dat => {
    const poly = decodePolyLine(dat.trip.legs[0].shape)
    tripRoute.value.push(poly)
  })

})


</script>