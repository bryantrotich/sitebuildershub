<template>
<AuthenticatedLayout>
    <Head title="Subscriptions" />
    <CRow>
        <CCol md="6">
            <h4>Subscriptions</h4>
            <p class="text-muted">Manage the subscription plans available to clients on the platform.</p>
        </CCol>
        <CCol md="6">
            <CButton color="primary" class="float-end" @click="showModal = true">Create Subscription</CButton>
        </CCol>                    
    </CRow>    

    <CCard v-if="isEmpty($props.subscriptions.data)" class="mb-4 border-0 shadow-sm">
        <CCardBody class="text-center">
            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
        </CCardBody>
    </CCard>

    <CRow :xs="{ gutter: 4 }" class="mb-4">
        <CCol v-for="subscription in $props.subscriptions.data" :key="subscription.id" :md="6" :xl="4">
            <CCard class="h-100 border-0 shadow-sm">
                <CCardBody class="d-flex flex-column">
                    <div class="d-flex align-items-start">
                        <div>
                            <h5 class="mb-1">{{ subscription.name }}</h5>
                            <div class="d-flex gap-1">
                                <CBadge color="success" v-if="subscription.active">Active</CBadge>
                                <CBadge color="warning" v-else>Inactive</CBadge>
                                <CBadge color="primary" v-if="subscription.default">Default</CBadge>
                            </div>
                        </div>
                        <CDropdown class="ms-auto">
                            <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options" /></CDropdownToggle>
                            <CDropdownMenu>
                                <CDropdownItem href="#" @click="$edit(subscription)">Edit</CDropdownItem>
                                <CDropdownItem href="#" @click="$delete(subscription)">Delete</CDropdownItem>
                            </CDropdownMenu>
                        </CDropdown>
                    </div>

                    <div class="fs-3 fw-semibold my-3">{{ subscription.currency_price }}</div>
                    <p class="small text-body-secondary">{{ subscription.description }}</p>

                    <ul class="list-unstyled small mb-3">
                        <li class="mb-1"><CIcon icon="cil-notes" class="me-2 text-primary" />{{ subscription.features?.max_posts ?? 0 }} postings allowed</li>
                        <li class="mb-1" v-for="feature in features" :key="feature.key">
                            <CIcon :icon="subscription.features?.[feature.key] ? 'cil-check' : 'cil-x-circle'" :class="`me-2 ${subscription.features?.[feature.key] ? 'text-success' : 'text-danger'}`" />{{ feature.label }}
                        </li>
                    </ul>

                    <CRow class="mt-auto text-center g-0 border rounded">
                        <CCol class="py-2 border-end">
                            <div class="fw-semibold">{{ subscription.users_count }}</div>
                            <div class="small text-body-secondary">Users</div>
                        </CCol>
                        <CCol class="py-2">
                            <div class="fw-semibold">{{ moment(subscription.created_at).format('MMM D, YYYY') }}</div>
                            <div class="small text-body-secondary">Created</div>
                        </CCol>
                    </CRow>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>

    <CPagination aria-label="Page navigation" size="sm" align="center">
        <CPaginationItem :href="page.url" v-for="(page, index) in pages" :key="index" :active="page.active"><span v-html="page.label"></span></CPaginationItem>
    </CPagination>

    <AddSubscription 
        :show="showModal" 
        :flash="$props.flash"                  
        @update:show="showModal = $event"
    />
    <EditSubscription 
        :show="editModal" 
        :subscription="$data.subscription"
        :flash="$props.flash"
        @update:show="editModal = $event"
    />                
</AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, inject, onMounted, reactive, ref, watch } from 'vue';
import { CTableDataCell } from '@coreui/vue';
import { cloneDeep, has, isEmpty, isNull } from 'lodash';
import { AddSubscription, EditSubscription } from '@/Components/Dashboard';
import moment from 'moment';

// Modal visibility state
const showModal = ref(false);
const editModal = ref(false);

// In your Vue component
const $props: any = defineProps({
    flash:         Object,    
    subscriptions: Object,
    data:          Object
});

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

const $data: any = reactive({
  subscription: {},
  loaders: {
    fetch: false
  },
  modal: false,
});

const features = [
    { key: 'for_businesses',    label: 'For businesses' },
    { key: 'for_professionals', label: 'For professionals' },
    { key: 'can_comment',       label: 'Can comment' },
];

const pages = computed( () => !isEmpty($props.subscriptions) ? $props.subscriptions.links.filter( (link: any) => !isNull(link.url)) : [] );

/**
 * Fetch categories from the server.
 * @async
 * @function
 * @name $fetch
 * @returns {Promise<void>}
 */
const $edit = async (subscription: any) => {
    $data.subscription = cloneDeep(subscription);
    editModal.value    = true;
}

const $delete = async (value:any) => {
    // Show a confirmation dialog to the user
    const { isConfirmed } = await $swal.fire({
        icon:  'question', // Icon to display in the dialog
        title: 'Delete Subscription', // Title of the dialog
        text:  `Are you sure you want to delete ${value.name}?`, // Text content of the dialog
        showCancelButton: true // Whether to show a "Cancel" button
    });

    // If the user does not confirm, exit the function
    if (!isConfirmed) { return; }
            
    // Set the loading flag
    $data.loaders.fetch = true;

    // Fetch the categories from the server
    router.delete(
        route('dashboard.subscriptions.delete',{ subscription: value.id }),
        {
            onSuccess: () => {
                // Post message
                $toast.success($props.flash.message);   
            },
            onError: (error) => {
                // Set the loading flag
                $data.loaders.fetch = false;
            }
        }
    );
}

onMounted( () => {
//   $data.subscriptions = $props.subscription
})
</script>
