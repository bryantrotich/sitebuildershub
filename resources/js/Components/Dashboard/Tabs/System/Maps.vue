<template>
    <CCard class="mb-4 shadow-sm border-0" id="maps">
        <CCardBody>
            <CCol md="12">
                <CCardTitle>Google Maps</CCardTitle>
                <p class="small text-body-secondary">
                    Used for location search and maps when clients create postings and add stores.
                    The key needs the Maps JavaScript API, Places API (New) and Geocoding API enabled.
                </p>
                <CCol md="6">
                    <CInputGroup class="mb-1">
                        <CFormInput
                            :type="$data.showKey ? 'text' : 'password'"
                            placeholder="AIza..."
                            aria-label="API Key"
                            autocomplete="off"
                            v-model="$data.form.google_maps_api_key"
                            :invalid="has($data.form.errors, 'google_maps_api_key')"
                        />
                        <CButton type="button" color="secondary" variant="outline" @click="$data.showKey = !$data.showKey">
                            {{ $data.showKey ? 'Hide' : 'Show' }}
                        </CButton>
                    </CInputGroup>
                    <div class="small text-body-secondary mb-1">API Key</div>
                    <p v-if="has($data.form.errors, 'google_maps_api_key')" class="text-danger small">{{ $data.form.errors.google_maps_api_key }}</p>
                    <p v-else-if="!pageProps.maps?.google_maps_api_key && pageProps.maps?.env_api_key" class="small text-body-secondary">
                        No key saved here yet, so the key from the <code>.env</code> file is being used.
                    </p>

                    <CFormInput
                        type="text"
                        label="Map ID (optional)"
                        autocomplete="off"
                        v-model="$data.form.google_maps_id"
                        :invalid="has($data.form.errors, 'google_maps_id')"
                        :feedback-invalid="$data.form.errors.google_maps_id"
                        class="mt-3"
                    />
                    <div class="small text-body-secondary">Enables Google's newer map markers. Leave empty to use the classic marker.</div>
                </CCol>
                <CCol md="4" class="pt-3">
                    <CButton color="primary" :disabled="$data.form.processing" @click="saveChanges">Save Changes</CButton>
                </CCol>
            </CCol>
        </CCardBody>
    </CCard>
</template>

<script setup lang="ts">
import { CCardTitle } from '@coreui/vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { has, isEmpty, pick } from 'lodash';
import { computed, inject, reactive, watch } from 'vue';

const $toast: any = inject('$toast');

const $data: any = reactive({
    form:    {},
    showKey: false,
});

const pageProps: any = computed( () => usePage().props );

const saveChanges = () => {
    $data.form.post(
        route('dashboard.system.store'),
        {
            preserveScroll: true,
            onSuccess: ({ props }: any) => {
                if( !isEmpty(props.flash.message) ){
                    $toast.success(props.flash.message);
                }
            },
        }
    );
}

watch(
    () => pageProps.value.maps,
    (maps) => {
        $data.form = useForm({
            ...pick(maps,['google_maps_api_key','google_maps_id']),
            tab:    'maps',
            _token: pageProps.value.csrf_token
        })
    },
    { immediate: true }
)
</script>
