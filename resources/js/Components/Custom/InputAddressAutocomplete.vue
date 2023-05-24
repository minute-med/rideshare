<script setup>

import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });


const addressResults = ref([])

function getResults(term) {
    return axios.get(`http://localhost:8080/search.php?q=${term}`)
}

function updateModelValue (addr) {
    emits('update:modelValue', { lat: addr.lat, lon: addr.lon })
    addressResults.value.splice(0)
    input.value.value = addr.display_name
}

async function updateResults (evt) {
    const term = evt.target.value
    const response = await getResults(term)
    addressResults.value.splice(0)
    // clear modelValue now ?
    response.data.forEach(res => addressResults.value.push(res));
}

function focusout (evt) {
    const ModelUpdated = evt.explicitOriginalTarget.parentElement.classList.contains('list-element')
    if(!ModelUpdated) {
        addressResults.value.splice(0)
    }
}
</script>

<template>
    <!-- {{ modelValue }} -->
    <input 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        @keyup="updateResults"
        @focusout="focusout"
        ref="input"
        />
        <div class="z-10 bg-white rounded-lg shadow w-60 dark:bg-gray-700">
          <ul v-if="addressResults.length > 0" class="h-48 py-2 overflow-y-auto text-gray-700 dark:text-gray-200" aria-labelledby="dropdownUsersButton">
            <li 
            v-for="addr in addressResults" 
            @click="updateModelValue(addr)"
            class="list-element block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
            >{{ addr.display_name }}</li>
          </ul>
        </div>









</template>