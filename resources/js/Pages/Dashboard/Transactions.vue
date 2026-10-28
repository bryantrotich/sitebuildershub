<template>
  <AuthenticatedLayout>
    <Head title="Transactions" />
    <CRow>
        <CCol md="6">
            <h4>Transactions</h4>
            <p class="text-muted">View transactions made by users for subscriptions and adverts.</p>
        </CCol>
    </CRow>
    <CCard class="mb-4 border-0 shadow-sm">
        <CCardBody>
            <CTable align="middle" hover responsive>
                <CTableHead>
                    <CTableRow>
                        <CTableHeaderCell scope="col">#</CTableHeaderCell>
                        <CTableHeaderCell scope="col">User</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Reference</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Payment Method</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Amount</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Status</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Paid On</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Created On</CTableHeaderCell>
                    </CTableRow>
                </CTableHead>
                <CTableBody>
                    <CTableRow v-if="isEmpty($props.transactions.data)">
                        <CTableDataCell colspan="8" class="text-center">
                            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                        </CTableDataCell>
                    </CTableRow>
                    <CTableRow v-for="(transaction, index) in $props.transactions.data" :key="transaction.id">
                        <CTableHeaderCell scope="row">{{ ($props.transactions.from ?? 1) + index }}</CTableHeaderCell>
                        <CTableDataCell>
                            <CAvatar color="primary" text-color="white" size="sm" class="me-2">{{ transaction.user?.name?.[0] ?? '?' }}</CAvatar>{{ transaction.user?.name ?? '—' }}
                        </CTableDataCell>
                        <CTableDataCell>{{ transaction.confirmation_code ?? '—' }}</CTableDataCell>
                        <CTableDataCell>{{ transaction.payment_method ?? '—' }}</CTableDataCell>
                        <CTableDataCell class="fw-semibold">{{ transaction.currency }} {{ transaction.amount }}</CTableDataCell>
                        <CTableDataCell>
                            <CBadge color="success" v-if="!isNull(transaction.complete)">Complete</CBadge>
                            <CBadge color="warning" v-else>Pending</CBadge>
                        </CTableDataCell>
                        <CTableDataCell>{{ transaction.paid_at ?? '—' }}</CTableDataCell>
                        <CTableDataCell>{{ transaction.created_at }}</CTableDataCell>
                    </CTableRow>
                </CTableBody>
            </CTable>
            <CPagination aria-label="Page navigation" size="sm" align="center">
                <CPaginationItem :href="page.url" v-for="(page, index) in pages" :key="index" :active="page.active"><span v-html="page.label"></span></CPaginationItem>
            </CPagination>
        </CCardBody>
    </CCard>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { isEmpty, isNull } from 'lodash';
import { computed, inject } from 'vue';

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

// In your Vue component
const $props: any = defineProps({
    transactions: Object,
});

const pages = computed( () => !isEmpty($props.transactions) ?$props.transactions.links.filter( (link: any) => !isNull(link.url)) : [] );


</script>
  