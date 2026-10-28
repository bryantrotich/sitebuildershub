<template>
    <div class="location-picker">
        <div class="d-flex">
            <div class="position-relative flex-grow-1">
                <input
                    type="text"
                    :class="`form-control ${$props.error ? 'border-danger' : ''}`"
                    placeholder="Search for your town or city"
                    autocomplete="off"
                    :disabled="!$data.ready"
                    v-model="$data.query"
                    @input="search"
                    @keydown.down.prevent="move(1)"
                    @keydown.up.prevent="move(-1)"
                    @keydown.enter.prevent="choose($data.suggestions[$data.highlighted])"
                    @blur="closeSuggestions"
                />
                <ul v-if="!isEmpty($data.suggestions)" class="list-group position-absolute w-100 shadow-8 suggestions">
                    <li
                        v-for="(suggestion, index) in $data.suggestions"
                        :key="index"
                        :class="['list-group-item list-group-item-action', { active: index == $data.highlighted }]"
                        @mousedown.prevent="choose(suggestion)"
                    >
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ suggestion.placePrediction.text.toString() }}
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-outline-primary ml-2 text-nowrap" :disabled="!$data.ready || $data.locating" @click="useMyLocation">
                <i class="fas fa-crosshairs mr-1"></i>{{ $data.locating ? 'Locating...' : 'Use my location' }}
            </button>
        </div>

        <div ref="mapElement" class="mt-3 rounded-4 border" style="height: 220px;"></div>
        <p class="font-size-3 text-gray mt-1 mb-0">Search, use your current location, or click the map to adjust.</p>

        <div v-if="$props.modelValue?.location" class="row mt-3">
            <div class="col-md-4 mb-2">
                <span class="d-block font-size-3 text-gray">Town / City</span>
                <span class="font-weight-semibold text-black-2">{{ $props.modelValue.town || '—' }}</span>
            </div>
            <div class="col-md-4 mb-2">
                <span class="d-block font-size-3 text-gray">County</span>
                <span class="font-weight-semibold text-black-2">{{ $props.modelValue.county || '—' }}</span>
            </div>
            <div class="col-md-4 mb-2">
                <span class="d-block font-size-3 text-gray">Country</span>
                <span class="font-weight-semibold text-black-2">{{ $props.modelValue.country || '—' }}</span>
            </div>
        </div>

        <p v-if="$data.message" class="text-danger mb-0">{{ $data.message }}</p>
    </div>
</template>
<script lang="ts" setup>
import { loadGoogleMaps, parseAddressComponents, AddressParts } from '@/google/maps';
import { debounce, isEmpty } from 'lodash';
import { onMounted, reactive, ref } from 'vue';

const $props = defineProps<{
    modelValue?: AddressParts,
    apiKey:      string,
    mapId?:      string,
    error?:      string,
}>();

const $emit = defineEmits(['update:modelValue']);

// Default map view: Nairobi, Kenya.
const DEFAULT_CENTER = { lat: -1.286389, lng: 36.817223 };

const mapElement = ref();

const $data: any = reactive({
    highlighted: 0,
    locating:    false,
    message:     '',
    query:       $props.modelValue?.location ?? '',
    ready:       false,
    suggestions: [],
});

let google: any, places: any, geocoder: any, map: any, marker: any, sessionToken: any;

onMounted(async () => {
    try {
        google = await loadGoogleMaps($props.apiKey);
        places = await google.maps.importLibrary('places');

        const { Map }      = await google.maps.importLibrary('maps');
        const { Geocoder } = await google.maps.importLibrary('geocoding');

        geocoder     = new Geocoder();
        sessionToken = new places.AutocompleteSessionToken();

        const center = $props.modelValue?.lat ? { lat: $props.modelValue.lat, lng: $props.modelValue.lng } : DEFAULT_CENTER;

        map = new Map(mapElement.value, {
            center,
            zoom:              $props.modelValue?.lat ? 12 : 6,
            mapId:             $props.mapId || undefined,
            streetViewControl: false,
            mapTypeControl:    false,
        });

        // Advanced markers need a map ID; fall back to the classic marker without one.
        if ($props.mapId) {
            const { AdvancedMarkerElement } = await google.maps.importLibrary('marker');
            marker = new AdvancedMarkerElement({ map, position: $props.modelValue?.lat ? center : null });
        } else {
            marker = new google.maps.Marker({ map, position: $props.modelValue?.lat ? center : null });
        }

        map.addListener('click', ({ latLng }: any) => reverseGeocode({ lat: latLng.lat(), lng: latLng.lng() }));

        $data.ready = true;
    } catch (error) {
        console.error(error);
        $data.message = 'Google Maps could not be loaded. Please refresh the page and try again.';
    }
});

const search = debounce(async () => {
    if ($data.query.trim().length < 2) {
        $data.suggestions = [];
        return;
    }

    try {
        const { suggestions } = await places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
            input: $data.query,
            sessionToken,
        });

        $data.suggestions = suggestions.filter((suggestion: any) => suggestion.placePrediction);
        $data.highlighted = 0;
    } catch (error) {
        console.error(error);
        $data.suggestions = [];
    }
}, 300);

const move = (step: number) => {
    const total = $data.suggestions.length;

    if (total) {
        $data.highlighted = (($data.highlighted + step) % total + total) % total;
    }
};

const closeSuggestions = () => setTimeout(() => $data.suggestions = [], 150);

const choose = async (suggestion: any) => {
    if (!suggestion) { return; }

    $data.suggestions = [];

    try {
        const place = suggestion.placePrediction.toPlace();

        await place.fetchFields({ fields: ['addressComponents', 'formattedAddress', 'location'] });

        // A session ends once a place is fetched; start a new one for the next search.
        sessionToken = new places.AutocompleteSessionToken();

        select(
            place.formattedAddress,
            place.addressComponents ?? [],
            { lat: place.location.lat(), lng: place.location.lng() }
        );
    } catch (error) {
        console.error(error);
        $data.message = 'That place could not be loaded. Please try another search.';
    }
};

const useMyLocation = () => {
    if (!navigator.geolocation) {
        $data.message = 'Your browser does not support location access.';
        return;
    }

    $data.locating = true;
    $data.message  = '';

    navigator.geolocation.getCurrentPosition(
        ({ coords }) => {
            $data.locating = false;
            reverseGeocode({ lat: coords.latitude, lng: coords.longitude });
        },
        () => {
            $data.locating = false;
            $data.message  = 'We could not get your location. Please allow location access or search instead.';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
};

const reverseGeocode = async (position: { lat: number, lng: number }) => {
    try {
        const { results } = await geocoder.geocode({ location: position });

        if (isEmpty(results)) {
            $data.message = 'No address was found for that spot. Please try a different location.';
            return;
        }

        // The first result is often a street address; merge components across results so
        // the town, county and country are all found even when the top result lacks one.
        const components = results.flatMap((result: any) => result.address_components);
        const parts      = parseAddressComponents(components);

        select([parts.town, parts.county, parts.country].filter(Boolean).join(', '), components, position);
    } catch (error) {
        console.error(error);
        $data.message = 'We could not look up that location. Please try again.';
    }
};

const select = (location: string, components: any[], position: { lat: number, lng: number }) => {
    const parts = parseAddressComponents(components);

    $data.query   = location;
    $data.message = parts.town ? '' : 'Please choose a more specific location, such as a town or city.';

    map.setCenter(position);
    map.setZoom(12);
    if (marker.setPosition) {
        marker.setPosition(position);
    } else {
        marker.position = position;
    }

    $emit('update:modelValue', { location, ...parts, ...position });
};
</script>
<style scoped>
.suggestions {
    z-index: 10;
    max-height: 260px;
    overflow-y: auto;
}
.suggestions .list-group-item {
    cursor: pointer;
}
</style>
