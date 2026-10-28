<template>
<AuthenticatedLayout>
    <Head title="Sub Categories" />
    <CRow>
        <CCol md="6">
            <h4>Sub Categories</h4>
            <p class="text-muted">Manage your website sub categories to organize content effectively.</p>
        </CCol>
        <CCol md="6">
            <CButton color="primary" class="float-end" @click="showModal = true">Add Sub Category</CButton>
        </CCol>
    </CRow>
    <CCard class="mb-4 border-0 shadow-sm">
        <CCardBody>
            <CTable align="middle" hover responsive>
                <CTableHead>
                    <CTableRow>
                        <CTableHeaderCell scope="col">#</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Name</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Category</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Child Sub Categories</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Created On</CTableHeaderCell>
                        <CTableHeaderCell scope="col"></CTableHeaderCell>
                    </CTableRow>
                </CTableHead>
                <CTableBody>
                    <CTableRow v-if="isEmpty($data.sub_categories.data)">
                        <CTableDataCell colspan="6" class="text-center">
                            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                        </CTableDataCell>
                    </CTableRow>
                    <CTableRow v-for="(sub_category, index) in $data.sub_categories.data" :key="sub_category.id">
                        <CTableHeaderCell scope="row">{{ ($data.sub_categories.from ?? 1) + index }}</CTableHeaderCell>
                        <CTableDataCell class="fw-semibold">{{ sub_category.name }}</CTableDataCell>
                        <CTableDataCell><CBadge color="primary">{{ sub_category.category?.name }}</CBadge></CTableDataCell>
                        <CTableDataCell><CBadge color="info">{{ sub_category.child_sub_categories_count }}</CBadge></CTableDataCell>
                        <CTableDataCell>{{ sub_category.created_at }}</CTableDataCell>
                        <CTableDataCell class="text-end">
                            <CDropdown>
                                <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options" /></CDropdownToggle>
                                <CDropdownMenu>
                                    <CDropdownItem href="#" @click="$edit(sub_category)">Edit</CDropdownItem>
                                    <CDropdownItem href="#" @click="$delete(sub_category)">Delete</CDropdownItem>
                                </CDropdownMenu>
                            </CDropdown>
                        </CTableDataCell>
                    </CTableRow>
                </CTableBody>
            </CTable>
            <CPagination aria-label="Page navigation" size="sm" align="center">
                <CPaginationItem :href="page.url" v-for="(page, index) in pages" :key="index" :active="page.active"><span v-html="page.label"></span></CPaginationItem>
            </CPagination>
        </CCardBody>
    </CCard>
    <AddSubCategory 
        :categories="$props.categories"
        :show="showModal" 
        @update:show="showModal = $event"
        @fetch="$fetch"
    />
</AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { AddSubCategory } from '@/Components/Dashboard';
import { computed, inject, onMounted, reactive, ref } from 'vue';
import { CTableDataCell } from '@coreui/vue';
import { isEmpty, isNull } from 'lodash';

// Modal visibility state
const showModal = ref(false);

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

// In your Vue component
const $props: any = defineProps({
    categories:     Array,
    sub_categories: Object,
    status:         String
});

const $data: any = reactive({
    sub_categories: {},
    loaders: {
        fetch: false
    }
})

const pages = computed( () => !isEmpty($data.sub_categories) ? $data.sub_categories.links.filter( (link: any) => !isNull(link.url)) : [] );

/**
 * Fetch categories from the server.
 * @async
 * @function
 * @name $fetch
 * @returns {Promise<void>}
 */
 const $fetch = async () => {
    try {
        // Set the loading flag
        $data.loaders.fetch = true;

        // Fetch the categories from the server
        const { data: { categories, sub_categories } }: any = await router.get(route('dashboard.sub_categories.fetch'));

        // Set the categories
        $data.categories     = categories;

        // Set the categories
        $data.sub_categories = sub_categories;        
    } catch (error) {
        // Set the loading flag
        $data.loaders.fetch = false;

        // Log the error
        console.error('Error fetching sub categories', error);
    } finally {
        // Set the loading flag
        $data.loaders.fetch = false;
    }
}


const $delete = async (value:any) => {
    // Show a confirmation dialog to the user
    const { isConfirmed } = await $swal.fire({
        icon:  'question', // Icon to display in the dialog
        title: 'Delete Sub Category', // Title of the dialog
        text:  `Are you sure you want to delete ${value.name}?`, // Text content of the dialog
        showCancelButton: true // Whether to show a "Cancel" button
    });

    // If the user does not confirm, exit the function
    if (!isConfirmed) { return; }
            
    // Set the loading flag
    $data.loaders.fetch = true;

    // Fetch the categories from the server
    router.delete(
        route('dashboard.sub_categories.delete',{ sub_category: value.id }),
        {
            onSuccess: () => {
                // Post message
                $toast.success(`${value.name} has been deleted.`);   

                // Set the loading flag
                $data.loaders.fetch = false;

                // Fetch categories
                $fetch();
            },
            onError: (error) => {
                // Set the loading flag
                $data.loaders.fetch = false;
            }
        }
    );
}

const $edit = (value:any) => {

}

onMounted( () => {
    $data.sub_categories = $props.sub_categories
})
</script>
