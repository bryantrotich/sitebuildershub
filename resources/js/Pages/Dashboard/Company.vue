<template>
    <AuthenticatedLayout>
        <Head title="Company | Dashboard" />
        <SettingsTabs description="Company contact information and profile." />
        <CRow>
            <CCol md="12"> 
                <CRow>
                    <CCol md="6" xs="12">
                        <CCard class="mb-4 shadow-sm border-0" id="company">
                            <CCardBody>
                                <CCol md="12">
                                    <CRow>
                                        <CCol md="12">
                                            <CFormInput
                                                type="text"
                                                label="Company Name"
                                                v-model="$data.form.name"
                                                class="mb-2"
                                            />  
                                        </CCol>
                                        <CCol md="12">
                                            <CFormInput
                                                type="email"
                                                label="Email Address"
                                                v-model="$data.form.email"
                                                class="mb-2"
                                            />  
                                        </CCol>
                                        <CCol md="12" class="my-2">
                                            <label class="mb-1">Phone Number</label>
                                            <VueTelInput
                                                v-model="$data.form.phone_number"
                                            />  
                                        </CCol>
                                        <CCol md="12">
                                            <CFormInput
                                                type="text"
                                                label="Address"
                                                v-model="$data.form.address"
                                                class="mb-2"
                                            />  
                                        </CCol>
                                        <CCol md="12">
                                            <CButton color="primary" @click="saveChanges">Save Changes</CButton>
                                        </CCol>
                                    </CRow>       
                                </CCol>   
                            </CCardBody>
                        </CCard>    
                    </CCol>
                </CRow>
            </CCol>
        </CRow>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { SettingsTabs } from '@/Components/Dashboard';
import { computed, reactive, watch } from 'vue';
import { pick } from 'lodash';
import vueDropzone from 'dropzone-vue3'
import { VueTelInput } from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const $data: any = reactive({
    tab: 1,
    dropzoneOptions: {
        url: 'https://httpbin.org/post',
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        headers: { "My-Awesome-Header": "header value" }
    },
    form:   {},
    errors: {}
});

const pageProps: any = computed( () => usePage().props );


const saveChanges = () => {

}

watch(
    () => pageProps.value.company,
    (user) => {
        $data.form = useForm({ 
            ...pick(user,['name','address','email','phone_number','logo','icon']), 
            _token: pageProps.value.csrf_token 
        })
    },
    { immediate: true }
)
</script>