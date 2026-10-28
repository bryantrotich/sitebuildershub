<template>
    <AuthenticatedLayout>
        <Head title="System | Dashboard" />
        <SettingsTabs description="App logo and icon, mail, Google Maps and payment gateway settings." />
        <CTabs :active-item-key="'logo-icon'">
            <CTabList variant="underline" class="mb-3">
                <CTab item-key="logo-icon"><CIcon icon="cil-applications" class="me-2" />App Logo &amp; Icon</CTab>
                <CTab item-key="mail"><CIcon icon="cil-envelope-closed" class="me-2" />Mail Settings</CTab>
                <CTab item-key="maps"><CIcon icon="cil-location-pin" class="me-2" />Google Maps</CTab>
                <CTab item-key="payment"><CIcon icon="cil-money" class="me-2" />Payment Gateway</CTab>
            </CTabList>
            <CTabContent>
                <CTabPanel item-key="logo-icon">
                    <CRow>
                        <CCol md="10"><LogoIconTab /></CCol>
                    </CRow>
                </CTabPanel>
                <CTabPanel item-key="mail">
                    <CRow>
                        <CCol md="10"><MailTab /></CCol>
                    </CRow>
                </CTabPanel>
                <CTabPanel item-key="maps">
                    <CRow>
                        <CCol md="10"><MapsTab /></CCol>
                    </CRow>
                </CTabPanel>
                <CTabPanel item-key="payment">
                    <CRow>
                        <CCol md="10"><PaymentTab /></CCol>
                    </CRow>
                </CTabPanel>
            </CTabContent>
        </CTabs>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LogoIconTab, MailTab, MapsTab, PaymentTab, SettingsTabs } from '@/Components/Dashboard';
</script>
