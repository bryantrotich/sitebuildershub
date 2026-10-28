<template>
<AuthenticatedLayout>
    <Head title="Staff" />
    <CRow>
        <CCol md="6">
            <h4>Staff</h4>
            <p class="text-muted">View the administrators and staff members who manage the platform.</p>
        </CCol>
    </CRow>
    <CCard class="mb-4 border-0 shadow-sm">
        <CCardBody>
            <CTable align="middle" hover responsive>
                <CTableHead>
                    <CTableRow>
                        <CTableHeaderCell scope="col">#</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Name</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Email</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Phone Number</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Role</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Verified</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Joined On</CTableHeaderCell>
                    </CTableRow>
                </CTableHead>
                <CTableBody>
                    <CTableRow v-if="isEmpty($props.staff.data)">
                        <CTableDataCell colspan="7" class="text-center">
                            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                        </CTableDataCell>
                    </CTableRow>
                    <CTableRow v-for="(member, index) in $props.staff.data" :key="member.id">
                        <CTableHeaderCell scope="row">{{ ($props.staff.from ?? 1) + index }}</CTableHeaderCell>
                        <CTableDataCell>
                            <CAvatar v-if="member.pictureUrl" :src="member.pictureUrl" size="sm" class="me-2" />
                            <CAvatar v-else color="primary" text-color="white" size="sm" class="me-2">{{ member.first_name?.[0] }}</CAvatar>
                            {{ member.name }}
                        </CTableDataCell>
                        <CTableDataCell>{{ member.email }}</CTableDataCell>
                        <CTableDataCell>{{ member.phone_number ?? '—' }}</CTableDataCell>
                        <CTableDataCell><CBadge :color="member.role?.state == 2 ? 'danger' : 'info'" class="text-capitalize">{{ member.role?.name }}</CBadge></CTableDataCell>
                        <CTableDataCell>
                            <CBadge color="success" v-if="!isNull(member.email_verified_at)">Verified</CBadge>
                            <CBadge color="warning" v-else>Unverified</CBadge>
                        </CTableDataCell>
                        <CTableDataCell>{{ member.created_at }}</CTableDataCell>
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
import { computed } from 'vue';

// In your Vue component
const $props: any = defineProps({
    staff: Object,
    status: String
});

const pages = computed( () => !isEmpty($props.staff) ? $props.staff.links.filter( (link: any) => !isNull(link.url)) : [] );
</script>
