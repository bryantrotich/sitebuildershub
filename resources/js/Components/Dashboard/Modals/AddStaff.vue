<template>
    <!-- Staff Creation Modal -->
    <CModal
        :visible="showModal"
        @close="closeModal"
        alignment="center"
        size="lg"
    >
        <CModalHeader>
            <CModalTitle>Add Staff Member</CModalTitle>
        </CModalHeader>
        <form @submit.prevent="submitForm" novalidate>
            <CModalBody>
                <p class="small text-body-secondary">
                    They will receive an email with a link to set their own password.
                </p>
                <CRow>
                    <CCol md="6" class="mb-3">
                        <CFormInput
                            id="first_name"
                            v-model="form.first_name"
                            label="First Name"
                            :invalid="!!form.errors.first_name"
                            :feedback-invalid="form.errors.first_name"
                        />
                    </CCol>
                    <CCol md="6" class="mb-3">
                        <CFormInput
                            id="last_name"
                            v-model="form.last_name"
                            label="Last Name"
                            :invalid="!!form.errors.last_name"
                            :feedback-invalid="form.errors.last_name"
                        />
                    </CCol>
                    <CCol md="6" class="mb-3">
                        <CFormInput
                            id="staff_email"
                            type="email"
                            v-model="form.email"
                            label="Email Address"
                            :invalid="!!form.errors.email"
                            :feedback-invalid="form.errors.email"
                        />
                    </CCol>
                    <CCol md="6" class="mb-3">
                        <CFormInput
                            id="staff_phone"
                            type="tel"
                            v-model="form.phone_number"
                            label="Phone Number (optional)"
                            :invalid="!!form.errors.phone_number"
                            :feedback-invalid="form.errors.phone_number"
                        />
                    </CCol>
                    <CCol md="12">
                        <label class="form-label">Role</label>
                        <div class="d-flex flex-column gap-2">
                            <label
                                v-for="role in $props.roles"
                                :key="role.id"
                                :class="['border rounded p-3 d-flex align-items-start gap-2 role-option', { 'border-primary': form.role_id == role.id }]"
                            >
                                <CFormCheck type="radio" name="role_id" :value="role.id" v-model="form.role_id" class="mt-0" />
                                <span>
                                    <span class="fw-semibold text-capitalize d-block">{{ role.name }}</span>
                                    <span class="small text-body-secondary">{{ descriptions[role.state] }}</span>
                                </span>
                            </label>
                        </div>
                        <div v-if="form.errors.role_id" class="text-danger small mt-1">{{ form.errors.role_id }}</div>
                    </CCol>
                </CRow>
            </CModalBody>
            <CModalFooter>
                <CButton color="secondary" @click="closeModal">
                    Close
                </CButton>
                <CButton color="primary" type="submit" :disabled="form.processing">
                    <CSpinner v-if="form.processing" variant="light" size="sm"/>
                    Add Staff
                </CButton>
            </CModalFooter>
        </form>
    </CModal>
</template>
<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, inject } from 'vue';

const $toast: any = inject("$toast");

// Props
const $props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    roles: {
        type: Array as () => any[],
        default: () => [],
    },
});

// Initialize emits
const $emit = defineEmits(['update:show']);

const showModal = computed({
    get: () => $props.show,
    set: (value:any) => $emit('update:show', value),
});

// What each role state can do, shown under the role name.
const descriptions: Record<number, string> = {
    2: 'Full access, including adding staff and changing settings.',
    1: 'Can manage clients, postings and other data.',
};

// Form for creating a new staff member
const form = useForm({
    _token:       (usePage().props as any).csrf_token,
    first_name:   '',
    last_name:    '',
    email:        '',
    phone_number: '',
    role_id:      '',
});

// Close modal and reset form
const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

// Submit form to create the staff member
const submitForm = () => {
    form.post(route('dashboard.staff.store'), {
        preserveScroll: true,
        onSuccess: ({ props }: any) => {
            $toast.success(props.flash.message);
            closeModal();
        },
    });
};
</script>
<style scoped>
.role-option {
    cursor: pointer;
}
</style>
