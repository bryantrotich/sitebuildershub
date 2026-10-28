<template>
    <GuestLayout>
        <Head title="Sign In" />
        <h3 class="mb-1">Welcome back</h3>
        <p class="text-body-secondary mb-4">Sign in to the admin dashboard.</p>

        <CAlert v-if="status" color="success" class="small">{{ status }}</CAlert>

        <CForm @submit.prevent="submit" novalidate>
            <div class="mb-3">
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

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-baseline">
                    <CFormLabel for="password">Password</CFormLabel>
                    <Link :href="route('dashboard.password.request')" class="small text-decoration-none">Forgot password?</Link>
                </div>
                <CInputGroup>
                    <CInputGroupText><CIcon icon="cil-lock-locked" /></CInputGroupText>
                    <CFormInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Password"
                        autocomplete="current-password"
                        v-model="form.password"
                        :invalid="has(form.errors,'password')"
                    />
                    <CButton type="button" color="secondary" variant="outline" @click="showPassword = !showPassword">
                        {{ showPassword ? 'Hide' : 'Show' }}
                    </CButton>
                </CInputGroup>
                <div v-if="has(form.errors,'password')" class="text-danger small mt-1">{{ form.errors.password }}</div>
            </div>

            <CFormCheck id="remember" label="Remember me" v-model="form.remember" class="mb-4" />

            <CButton color="primary" type="submit" class="w-100 py-2" :disabled="form.processing">
                <CSpinner v-if="form.processing" size="sm" class="me-1" />Sign In
            </CButton>
        </CForm>
    </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { has } from 'lodash';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
}>();

const pageProps: any = computed( () => usePage().props );

const showPassword = ref(false);

/**
 * The login form data.
 *
 * @prop {String} email - The user's email address.
 * @prop {String} password - The user's password.
 * @prop {Boolean} remember - Whether to remember the user.
 */
const form = useForm({
    _token:   pageProps.value.csrf_token,
    email:    String(),
    password: String(),
    remember: false,
});

/**
 * Submits the login form and clears the password afterwards.
 */
const submit = () => {
    form.post(route('dashboard.login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
