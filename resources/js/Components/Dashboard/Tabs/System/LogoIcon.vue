<template>
    <CCard class="mb-4 shadow-sm border-0" id="logo-icon">
        <CCardBody>
            <CCardTitle>App Logo &amp; Icon</CCardTitle>
            <p class="small text-body-secondary">
                The logo shows in the header and footer of the website. The icon is the small square version of the brand.
                JPG, PNG or WebP; logo up to 2MB, icon up to 1MB.
            </p>
            <CRow :xs="{ gutter: 4 }">
                <CCol v-for="image in images" :key="image.field" md="6">
                    <h6>{{ image.label }}</h6>
                    <div class="border rounded d-flex align-items-center justify-content-center bg-body-tertiary mb-2 p-3" :style="`height: ${image.height}px;`">
                        <img v-if="preview(image.field)" :src="preview(image.field)" :alt="image.label" class="mw-100 mh-100" style="object-fit: contain;">
                        <span v-else class="small text-body-secondary"><CIcon icon="cil-ban" class="me-1" />No {{ image.field }} uploaded</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <CFormInput
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            :invalid="has($data.form.errors, image.field)"
                            :feedback-invalid="$data.form.errors[image.field]"
                            @change="select(image.field, $event)"
                        />
                        <CButton v-if="$data.form[image.field]" color="secondary" variant="ghost" size="sm" @click="clear(image.field)">Undo</CButton>
                    </div>
                    <div v-if="$data.form[image.field]" class="small text-warning mt-1">New {{ image.field }} selected; save to apply.</div>
                </CCol>
            </CRow>
            <div class="pt-3">
                <CButton color="primary" :disabled="$data.form.processing || (!$data.form.logo && !$data.form.icon)" @click="saveChanges">
                    <CSpinner v-if="$data.form.processing" size="sm" class="me-1" />Save Changes
                </CButton>
            </div>
        </CCardBody>
    </CCard>
</template>

<script setup lang="ts">
import { CCardTitle } from '@coreui/vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { has, isEmpty } from 'lodash';
import { computed, inject, reactive } from 'vue';

const $toast: any = inject('$toast');

const images = [
    { field: 'logo', label: 'Logo', height: 140 },
    { field: 'icon', label: 'Icon', height: 140 },
];

const pageProps: any = computed( () => usePage().props );

const $data: any = reactive({
    form:     useForm({ logo: null, icon: null, tab: 'images', _token: pageProps.value.csrf_token }),
    previews: { logo: null, icon: null },
});

// A newly chosen file previews locally; otherwise show the saved image.
const preview = (field: string) => $data.previews[field] ?? pageProps.value.images?.[field];

const select = (field: string, { target }: any) => {
    const file = target.files?.[0] ?? null;

    if( $data.previews[field] ){ URL.revokeObjectURL($data.previews[field]); }

    $data.form[field]     = file;
    $data.previews[field] = file ? URL.createObjectURL(file) : null;
    $data.form.clearErrors(field);
}

const clear = (field: string) => {
    if( $data.previews[field] ){ URL.revokeObjectURL($data.previews[field]); }

    $data.form[field]     = null;
    $data.previews[field] = null;
}

const saveChanges = () => {
    $data.form.post(
        route('dashboard.system.store'),
        {
            forceFormData:  true,
            preserveScroll: true,
            onSuccess: ({ props }: any) => {
                clear('logo');
                clear('icon');

                // The favicon is rendered server-side, so swap it in place for this open page.
                const favicon: any = document.getElementById('favicon');

                if( favicon && props.images?.icon ){
                    favicon.href = props.images.icon;
                }

                if( !isEmpty(props.flash.message) ){
                    $toast.success(props.flash.message);
                }
            },
        }
    );
}
</script>
