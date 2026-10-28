<template>
    <CCard class="mb-4 shadow-sm border-0" id="payment">
        <CCardBody>
            <CCol md="12">
                <CCardTitle>PesaPal</CCardTitle>
                <p class="small text-body-secondary">
                    These credentials are read from the <code>PESAPAL_*</code> values in the <code>.env</code> file.
                    Update them there to change the payment gateway.
                </p>
                <CCol md="6">
                    <div class="mb-3">
                        <label class="form-label">Environment</label>
                        <div>
                            <CBadge :color="payment.environment == 'live' ? 'success' : 'warning'" class="text-capitalize">{{ payment.environment ?? 'Not set' }}</CBadge>
                            <span v-if="payment.base_url" class="small text-body-secondary ms-2">{{ payment.base_url }}</span>
                        </div>
                    </div>
                    <CFormInput
                        type="text"
                        label="Consumer Key"
                        :model-value="payment.consumer_key ?? 'Not set'"
                        readonly
                        class="mb-3"
                    />
                    <CFormInput
                        type="text"
                        label="Consumer Secret"
                        :model-value="payment.consumer_secret ?? 'Not set'"
                        readonly
                    />

                    <CAlert v-if="has($data.form.errors, 'pesapal')" color="danger" class="mt-3 mb-0 small">{{ $data.form.errors.pesapal }}</CAlert>
                    <CAlert v-if="$data.connected" color="success" class="mt-3 mb-0 small">{{ $data.connected }}</CAlert>
                </CCol>
                <CCol md="6" class="pt-3">
                    <CButton color="primary" :disabled="$data.form.processing" @click="testConnection">
                        <CSpinner v-if="$data.form.processing" size="sm" class="me-1" />Test Connection
                    </CButton>
                </CCol>
            </CCol>
        </CCardBody>
    </CCard>
</template>

<script setup lang="ts">
import { CCardTitle } from '@coreui/vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { has, isEmpty } from 'lodash';
import { computed, reactive } from 'vue';

const pageProps: any = computed( () => usePage().props );

const payment: any = computed( () => pageProps.value.payment ?? {} );

const $data: any = reactive({
    connected: '',
    form:      useForm({ tab: 'payment', action: 'test', _token: pageProps.value.csrf_token }),
});

const testConnection = () => {
    $data.connected = '';

    $data.form.post(
        route('dashboard.system.store'),
        {
            preserveScroll: true,
            onSuccess: ({ props }: any) => {
                if( !isEmpty(props.flash.message) ){
                    $data.connected = props.flash.message;
                }
            },
        }
    );
}
</script>
