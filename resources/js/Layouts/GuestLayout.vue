<template>
  <div class="wrapper min-vh-100 d-flex align-items-center bg-body-tertiary py-5">
    <CContainer>
        <CRow class="justify-content-center">
            <CCol :xl="9" :lg="10" xs="12">
                <CCard class="border-0 shadow-sm overflow-hidden">
                    <CRow class="g-0">
                        <!-- Brand panel -->
                        <CCol md="5" class="brand-panel d-none d-md-flex flex-column justify-content-between p-5 text-white">
                            <div>
                                <div class="brand-logo-card mb-4">
                                    <img v-if="company?.logo" :src="company.logo" :alt="company.name">
                                    <span v-else class="fs-4 fw-bold">{{ company?.name }}</span>
                                </div>
                                <h4 class="fw-semibold text-white">Admin Dashboard</h4>
                                <p class="text-white-50 mb-0">Manage clients, postings, subscriptions and payments in one place.</p>
                            </div>
                            <div class="small text-white-50">&copy; {{ new Date().getFullYear() }} {{ company?.name }}</div>
                        </CCol>

                        <!-- Form panel -->
                        <CCol md="7" class="p-4 p-md-5">
                            <div class="d-md-none text-center mb-4">
                                <img v-if="company?.logo" :src="company.logo" :alt="company.name" style="height: 56px; max-width: 100%; object-fit: contain;">
                                <span v-else class="fs-4 fw-bold">{{ company?.name }}</span>
                            </div>
                            <slot />
                        </CCol>
                    </CRow>
                </CCard>
            </CCol>
        </CRow>
    </CContainer>
  </div>
</template>
<script setup lang="ts">
import '../../scss/app.scss';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// The company details (name, logo, icon) shared with every page.
const company: any = computed(() => usePage().props.company);
</script>
<style scoped>
.brand-panel {
  /* Indigo and gold from the SiteBuild Hub logo. */
  background: linear-gradient(160deg, #3a3f9e 0%, #23265f 100%);
  border-bottom: 4px solid #f5c518;
}
.brand-logo-card {
  display: inline-flex;
  align-items: center;
  padding: 10px 16px;
  border-radius: 12px;
  background-color: #f7f7f8;
  color: #2b2b2b;
}
.brand-logo-card img {
  height: 64px;
  width: auto;
  max-width: 220px;
  object-fit: contain;
}
</style>
