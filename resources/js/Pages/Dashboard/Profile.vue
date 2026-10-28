<template>
    <AuthenticatedLayout>
        <Head title="Profile | Dashboard" />
        <SettingsTabs description="Your account details and avatar." />
        <CRow>
            <CCol md="12">
                <CRow>
                    <CCol lg="8" xs="12">
                        <CCard class="mb-4 shadow-sm border-0" id="profile">
                            <CCardBody>
                                <div class="d-flex align-items-center mb-4">
                                    <CAvatar v-if="avatar" :src="avatar" size="xl" class="flex-shrink-0" style="width: 96px; height: 96px;" />
                                    <CAvatar v-else color="primary" text-color="white" size="xl" class="flex-shrink-0" style="width: 96px; height: 96px;">{{ initials }}</CAvatar>
                                    <div class="ms-4">
                                        <h6 class="mb-1">Profile Picture</h6>
                                        <div class="small text-body-secondary mb-2">JPG, PNG or WebP, up to 2MB. It is cropped to a square.</div>
                                        <div class="d-flex gap-2">
                                            <CButton color="primary" variant="outline" size="sm" @click="pictureInput.click()">
                                                {{ avatar ? 'Change Picture' : 'Upload Picture' }}
                                            </CButton>
                                            <CButton v-if="$data.form.picture" color="secondary" variant="ghost" size="sm" @click="clearPicture">Undo</CButton>
                                        </div>
                                        <input ref="pictureInput" type="file" accept="image/png,image/jpeg,image/webp" class="d-none" @change="selectPicture" />
                                        <p v-if="$data.form.errors.picture" class="text-danger small mb-0 mt-1">{{ $data.form.errors.picture }}</p>
                                        <p v-else-if="$data.form.picture" class="text-warning small mb-0 mt-1">New picture selected; save to apply.</p>
                                    </div>
                                </div>
                                <CRow>
                                    <CCol md="6">
                                        <CFormInput
                                            type="text"
                                            label="First Name"
                                            v-model="$data.form.first_name"
                                            :invalid="has($data.form.errors, 'first_name')"
                                            :feedback-invalid="$data.form.errors.first_name"
                                            class="mb-3"
                                        />
                                    </CCol>
                                    <CCol md="6">
                                        <CFormInput
                                            type="text"
                                            label="Last Name"
                                            v-model="$data.form.last_name"
                                            :invalid="has($data.form.errors, 'last_name')"
                                            :feedback-invalid="$data.form.errors.last_name"
                                            class="mb-3"
                                        />
                                    </CCol>
                                    <CCol md="6">
                                        <CFormInput
                                            type="email"
                                            label="Email Address"
                                            v-model="$data.form.email"
                                            :invalid="has($data.form.errors, 'email')"
                                            :feedback-invalid="$data.form.errors.email"
                                            class="mb-3"
                                        />
                                    </CCol>
                                    <CCol md="6" class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <VueTelInput v-model="$data.form.phone_number" />
                                        <p v-if="$data.form.errors.phone_number" class="text-danger small mb-0">{{ $data.form.errors.phone_number }}</p>
                                    </CCol>
                                    <CCol md="12">
                                        <CButton color="primary" :disabled="$data.form.processing" @click="saveChanges">
                                            <CSpinner v-if="$data.form.processing" size="sm" class="me-1" />Save Changes
                                        </CButton>
                                    </CCol>
                                </CRow>
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
import { SettingsTabs } from '@/Components/Dashboard';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, inject, reactive, ref, watch } from 'vue';
import { has, isEmpty, pick } from 'lodash';
import { VueTelInput } from 'vue3-tel-input'
import 'vue3-tel-input/dist/vue3-tel-input.css'

const $toast: any = inject('$toast');

const pictureInput = ref();

const $data: any = reactive({
    form:    {},
    preview: null,
});

const pageProps: any = computed( () => usePage().props );

const user: any = computed( () => pageProps.value.auth.user );

// A newly chosen picture previews locally; otherwise show the saved one.
const avatar = computed( () => $data.preview ?? user.value?.pictureUrl );

const initials = computed( () => `${user.value?.first_name?.[0] ?? ''}${user.value?.last_name?.[0] ?? ''}`.toUpperCase() );

const selectPicture = ({ target }: any) => {
    const file = target.files?.[0];

    if( !file ){ return; }

    if( $data.preview ){ URL.revokeObjectURL($data.preview); }

    $data.form.picture = file;
    $data.preview      = URL.createObjectURL(file);
    $data.form.clearErrors('picture');

    // Allow picking the same file again after an undo.
    target.value = '';
}

const clearPicture = () => {
    if( $data.preview ){ URL.revokeObjectURL($data.preview); }

    $data.form.picture = null;
    $data.preview      = null;
}

const saveChanges = () => {
    $data.form.post(
        route('dashboard.profile.store'),
        {
            forceFormData:  true,
            preserveScroll: true,
            onSuccess: ({ props }: any) => {
                clearPicture();

                if( !isEmpty(props.flash.message) ){
                    $toast.success(props.flash.message);
                }
            },
        }
    );
}

watch(
    () => user.value,
    (user) => {
        $data.form = useForm({ ...pick(user,['first_name','last_name','email','phone_number']), picture: null, _token: pageProps.value.csrf_token })
    },
    { immediate: true }
)
</script>
