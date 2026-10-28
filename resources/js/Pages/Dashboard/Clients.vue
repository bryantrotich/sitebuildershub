<template>
<AuthenticatedLayout>
    <Head title="Clients" />
    <CCol xs="12">
        <h4>Clients</h4>
        <p class="text-muted">View and manage client details and information</p>
    </CCol>

    <CCard v-if="isEmpty($props.clients.data)" class="border-0 shadow-sm">
        <CCardBody class="text-center">
            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
        </CCardBody>
    </CCard>

    <CRow :xs="{ gutter: 4 }">
        <CCol v-for="client in $props.clients.data" :key="client.id" :md="6" :xl="4">
            <CCard class="h-100 border-0 shadow-sm">
                <CCardBody>
                    <div class="d-flex align-items-start mb-3">
                        <CAvatar v-if="client.pictureUrl" :src="client.pictureUrl" size="lg" />
                        <CAvatar v-else color="primary" text-color="white" size="lg">{{ initials(client) }}</CAvatar>
                        <div class="ms-3 text-truncate">
                            <div class="fw-semibold text-truncate">{{ client.name }}</div>
                            <div class="small text-body-secondary text-truncate">{{ client.business_profile?.name ?? 'No business profile' }}</div>
                        </div>
                        <CDropdown class="ms-auto">
                            <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options" /></CDropdownToggle>
                            <CDropdownMenu>
                                <CDropdownItem href="#">Edit</CDropdownItem>
                                <CDropdownItem href="#">Delete</CDropdownItem>
                            </CDropdownMenu>
                        </CDropdown>
                    </div>

                    <div class="small mb-1 text-truncate"><CIcon icon="cil-envelope-closed" class="me-2 text-body-secondary" />{{ client.email }}</div>
                    <div class="small mb-3"><CIcon icon="cil-speech" class="me-2 text-body-secondary" />{{ client.phone_number ?? '—' }}</div>

                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <CBadge color="success" v-if="!isNull(client.email_verified_at)">Verified</CBadge>
                        <CBadge color="warning" v-else>Unverified</CBadge>
                        <CBadge color="info" v-if="client.activeSubscription">{{ client.activeSubscription.name }}</CBadge>
                        <CBadge color="secondary" v-else>No subscription</CBadge>
                        <CBadge color="danger" v-if="client.unpaid_invoices_count > 0">{{ client.unpaid_invoices_count }} unpaid {{ client.unpaid_invoices_count == 1 ? 'invoice' : 'invoices' }}</CBadge>
                    </div>

                    <CRow class="text-center g-0 border rounded">
                        <CCol class="py-2 border-end">
                            <div class="fw-semibold">{{ number(client.postings_count) }}</div>
                            <div class="small text-body-secondary">Postings</div>
                        </CCol>
                        <CCol class="py-2">
                            <div class="fw-semibold">{{ number(client.projects_count) }}</div>
                            <div class="small text-body-secondary">Projects</div>
                        </CCol>
                    </CRow>
                </CCardBody>
                <CCardFooter class="small text-body-secondary bg-transparent">
                    <CIcon icon="cil-calendar" class="me-2" />Joined {{ client.created_at }}
                </CCardFooter>
            </CCard>
        </CCol>
    </CRow>

    <CCol md="12" class="mt-4">
        <CPagination aria-label="Page navigation example" size="sm" align="center">
            <CPaginationItem :href="page.url" v-for="(page, index) in pages" :key="index" :active="page.active"><span v-html="page.label"></span></CPaginationItem>
        </CPagination>
    </CCol>
</AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { isEmpty, isNull } from 'lodash';

// In your Vue component
const $props: any = defineProps({
  clients: Object,
  status: String
});

const number   = (value: number) => Number(value ?? 0).toLocaleString();
const initials = (client: any) => `${client.first_name?.[0] ?? ''}${client.last_name?.[0] ?? ''}`.toUpperCase();

const pages = computed( () => !isEmpty($props.clients) ? $props.clients.links.filter( (link: any) => !isNull(link.url)) : [] );
</script>
