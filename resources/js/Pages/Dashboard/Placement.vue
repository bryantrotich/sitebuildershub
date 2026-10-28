<template>
<AuthenticatedLayout>
    <Head title="Advert Placement" />
    <CRow>
        <CCol md="6">
            <h4>Advert Placements</h4>
            <p class="text-muted">This section specifies areas where adverts can be placed and how much those sections cost.</p>
        </CCol>
        <CCol md="6">
            <CButton color="primary" class="float-end" @click="showModal = true">Create placement</CButton>
        </CCol>                    
    </CRow>    

    <CCard v-if="isEmpty($props.placements.data)" class="mb-4 border-0 shadow-sm">
        <CCardBody class="text-center">
            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
        </CCardBody>
    </CCard>

    <CRow :xs="{ gutter: 4 }" class="mb-4">
        <CCol v-for="placement in $props.placements.data" :key="placement.id" :md="6" :xl="4">
            <CCard class="h-100 border-0 shadow-sm">
                <CCardBody class="d-flex flex-column">
                    <div class="d-flex align-items-start">
                        <div>
                            <h5 class="mb-1">{{ placement.name }}</h5>
                            <CBadge color="info" class="text-capitalize">{{ placement.section.replace('-', ' ') }}</CBadge>
                        </div>
                        <CDropdown class="ms-auto">
                            <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options" /></CDropdownToggle>
                            <CDropdownMenu>
                                <CDropdownItem href="#" @click="$edit(placement)">Edit</CDropdownItem>
                                <CDropdownItem href="#" @click="$delete(placement)">Delete</CDropdownItem>
                            </CDropdownMenu>
                        </CDropdown>
                    </div>

                    <div class="fs-3 fw-semibold my-3">{{ placement.currency_price }}</div>

                    <CRow class="mt-auto text-center g-0 border rounded">
                        <CCol class="py-2 border-end">
                            <div class="fw-semibold">{{ placement.promotions_count }}</div>
                            <div class="small text-body-secondary">Adverts</div>
                        </CCol>
                        <CCol class="py-2">
                            <div class="fw-semibold">{{ moment(placement.created_at).format('MMM D, YYYY') }}</div>
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

    <AddPlacement 
        :show="showModal" 
        :flash="$props.flash"                  
        @update:show="showModal = $event"
    />
    <EditPlacement 
        :show="editModal" 
        :placement="$data.placement"
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
import { AddPlacement, EditPlacement } from '@/Components/Dashboard';
import moment from 'moment';

// Modal visibility state
const showModal = ref(false);
const editModal = ref(false);

// In your Vue component
const $props: any = defineProps({
    flash:      Object,    
    placements: Object,
    data:       Object
});

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

const $data: any = reactive({
  placement: {},
  loaders: {
    fetch: false
  },
  modal: false,
});

const pages = computed( () => !isEmpty($props.placements) ? $props.placements.links.filter( (link: any) => !isNull(link.url)) : [] );

/**
 * Fetch categories from the server.
 * @async
 * @function
 * @name $fetch
 * @returns {Promise<void>}
 */
const $edit = async (placement: any) => {
    $data.placement = cloneDeep(placement);
    editModal.value    = true;
}

const $delete = async (value:any) => {
    // Show a confirmation dialog to the user
    const { isConfirmed } = await $swal.fire({
        icon:  'question', // Icon to display in the dialog
        title: 'Delete placement', // Title of the dialog
        text:  `Are you sure you want to delete ${value.name}?`, // Text content of the dialog
        showCancelButton: true // Whether to show a "Cancel" button
    });

    // If the user does not confirm, exit the function
    if (!isConfirmed) { return; }
            
    // Set the loading flag
    $data.loaders.fetch = true;

    // Fetch the categories from the server
    router.delete(
        route('dashboard.placements.delete',{ placement: value.id }),
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
  $data.placements = $props.placements
})
</script>
