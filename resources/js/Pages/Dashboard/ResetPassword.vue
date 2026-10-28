<template>
    <GuestLayout>
        <Head title="Reset Password" />
        <h3 class="mb-1">Choose a new password</h3>
        <p class="text-body-secondary mb-4">Your new password must be at least 8 characters.</p>

        <CForm @submit.prevent="submit" novalidate>
            <div class="mb-3">
                <CFormLabel for="email">Email Address</CFormLabel>
                <CFormInput
                    id="email"
                    type="email"
                    autocomplete="email"
                    v-model="form.email"
                    :invalid="has(form.errors,'email')"
                    :feedback-invalid="form.errors.email"
                />
            </div>
            <div class="mb-3">
                <CFormLabel for="password">New Password</CFormLabel>
                <CInputGroup>
                    <CFormInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        autofocus
                        v-model="form.password"
                        :invalid="has(form.errors,'password')"
                    />
                    <CButton type="button" color="secondary" variant="outline" @click="showPassword = !showPassword">
                        {{ showPassword ? 'Hide' : 'Show' }}
                    </CButton>
                </CInputGroup>
                <div v-if="has(form.errors,'password')" class="text-danger small mt-1">{{ form.errors.password }}</div>
            </div>
            <div class="mb-4">
                <CFormLabel for="password_confirmation">Confirm New Password</CFormLabel>
                <CFormInput
                    id="password_confirmation"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    v-model="form.password_confirmation"
                />
            </div>

            <CButton color="primary" type="submit" class="w-100 py-2 mb-3" :disabled="form.processing">
                <CSpinner v-if="form.processing" size="sm" class="me-1" />Reset Password
            </CButton>
            <div class="text-center">
                <Link :href="route('dashboard.login')" class="small text-decoration-none"><CIcon icon="cil-chevron-left" size="sm" /> Back to sign in</Link>
            </div>
        </CForm>
    </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { has } from 'lodash';
import { computed, ref } from 'vue';

const props = defineProps<{
    email?: string;
    token:  string;
}>();

const pageProps: any = computed( () => usePage().props );

const showPassword = ref(false);

const form = useForm({
    _token:                pageProps.value.csrf_token,
    token:                 props.token,
    email:                 props.email ?? '',
    password:              '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('dashboard.password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
