<template>
    <GuestLayout>
        <Head title="Forgot Password" />
        <h3 class="mb-1">Forgot your password?</h3>
        <p class="text-body-secondary mb-4">Enter your staff email address and we will send you a link to choose a new password.</p>

        <CAlert v-if="status" color="success" class="small">{{ status }}</CAlert>

        <CForm @submit.prevent="submit" novalidate>
            <div class="mb-4">
                <CFormLabel for="email">Email Address</CFormLabel>
                <CInputGroup>
                    <CInputGroupText><CIcon icon="cil-envelope-closed" /></CInputGroupText>
                    <CFormInput
                        id="email"
                        type="email"
                        placeholder="you@example.com"
                        autocomplete="email"
                        autofocus
                        v-model="form.email"
                        :invalid="has(form.errors,'email')"
                    />
                </CInputGroup>
                <div v-if="has(form.errors,'email')" class="text-danger small mt-1">{{ form.errors.email }}</div>
            </div>

            <CButton color="primary" type="submit" class="w-100 py-2 mb-3" :disabled="form.processing">
                <CSpinner v-if="form.processing" size="sm" class="me-1" />Email Reset Link
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
import { computed } from 'vue';

defineProps<{
    status?: string;
}>();

const pageProps: any = computed( () => usePage().props );

const form = useForm({
    _token: pageProps.value.csrf_token,
    email:  String(),
});

const submit = () => {
    form.post(route('dashboard.password.email'), {
        preserveScroll: true,
    });
};
</script>
