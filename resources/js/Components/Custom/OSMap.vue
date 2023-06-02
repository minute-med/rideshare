<script setup>
import { ref } from 'vue'

defineProps({
    markers: {
        type: Array,
        default: []
    },
    routeCoord: {
        type: Array,
        default: []
    },
    center: {
        type: Array,
        default: [105.01463774982666, 12.358258174708986]
    },
    projection: {
        type: String,
        default: 'EPSG:4326'
    },
    zoom: {
        type: Number,
        default: 7
    },
    rotation: {
        type: Number,
        default: 0
    },
})

const vectorsource = ref(null)

</script>

<template>
    <div>
        <div>
            <ol-map
            :loadTilesWhileAnimating="true"
            :loadTilesWhileInteracting="true"
            style="height: 400px;width: 100%;"
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
                >
                <ol-source-vector ref="vectorsource">
                    <ol-feature v-if="routeCoord.length > 0">
                        <ol-geom-multi-line-string
                            :coordinates="routeCoord"
                        ></ol-geom-multi-line-string>
                        <ol-style>
                            <ol-style-stroke
                            color="blue"
                            :width="5"
                            ></ol-style-stroke>
                        </ol-style>
                    </ol-feature>
                    
                    <slot></slot>

                </ol-source-vector>
                </ol-vector-layer>
            </ol-map>
        </div>
    </div>
</template>