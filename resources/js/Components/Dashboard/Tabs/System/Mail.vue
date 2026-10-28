<template>
    <CCard class="mb-4 shadow-sm border-0" id="mail">
        <CCardBody>
            <CCol md="12">
                <CCardTitle>SMTP Settings</CCardTitle>
                <CCol md="6">
                    <CFormInput
                        type="text"
                        label="Mail Host"
                        v-model="$data.form.host"
                        class="mb-2"
                    />  
                    <CFormInput
                        type="text"
                        label="Mail Username"
                        v-model="$data.form.username"
                        class="mb-2"
                    />  
                    <CFormInput
                        type="password"
                        label="Mail Password"
                        v-model="$data.form.password"
                        class="mb-2"
                    />  
                    <CFormInput
                        type="number"
                        label="Mail Port"
                        v-model="$data.form.port"
                    />       
                </CCol> 
                <CCol md="4" class="pt-2">
                    <CButton color="primary" @click="saveChanges">Save Changes</CButton>         
                </CCol>         
            </CCol>  
        </CCardBody>
    </CCard>       
</template>

<script setup lang="ts">
import { CCardText, CCardTitle } from '@coreui/vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { pick } from 'lodash';
import { computed, reactive, watch } from 'vue';

const $data: any = reactive({
    form:   {},
    errors: {}
});

const pageProps: any = computed( () => usePage().props );


const saveChanges = () => {

}

watch(
    () => pageProps.value.mail,
    (mail) => {
        $data.form = useForm({ 
            ...pick(mail,['host','port','username','password']), 
            _token: pageProps.value.csrf_token 
        })
    },
    { immediate: true }
)
</script>