<template>
<AuthenticatedLayout>
    <Head title="Categories" />
    <CRow>
        <CCol md="6">
            <h4>Categories</h4>
            <p class="text-muted">Manage your website categories to organize content effectively.</p>
        </CCol>
        <CCol md="6">
            <CButton color="primary" class="float-end" @click="showModal = true">Add Category</CButton>
        </CCol>
    </CRow>
    <CCard class="mb-4 border-0 shadow-sm">
        <CCardBody>
            <CTable align="middle" hover responsive>
                <CTableHead>
                    <CTableRow>
                        <CTableHeaderCell scope="col">#</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Name</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Sub Categories</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Child Sub Categories</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Created On</CTableHeaderCell>
                        <CTableHeaderCell scope="col"></CTableHeaderCell>
                    </CTableRow>
                </CTableHead>
                <CTableBody>
                    <CTableRow v-if="isEmpty($data.categories.data)">
                        <CTableDataCell colspan="6" class="text-center">
                            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                        </CTableDataCell>
                    </CTableRow>
                    <CTableRow v-for="(category, index) in $data.categories.data" :key="category.id">
                        <CTableHeaderCell scope="row">{{ ($data.categories.from ?? 1) + index }}</CTableHeaderCell>
                        <CTableDataCell class="fw-semibold">{{ category.name }}</CTableDataCell>
                        <CTableDataCell><CBadge color="primary">{{ category.sub_categories_count }}</CBadge></CTableDataCell>
                        <CTableDataCell><CBadge color="info">{{ category.child_sub_categories_count }}</CBadge></CTableDataCell>
                        <CTableDataCell>{{ category.created_at }}</CTableDataCell>
                        <CTableDataCell class="text-end">
                            <CDropdown>
                                <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options" /></CDropdownToggle>
                                <CDropdownMenu>
                                    <CDropdownItem href="#" @click="$edit(category)">Edit</CDropdownItem>
                                    <CDropdownItem href="#" @click="$delete(category)">Delete</CDropdownItem>
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
    <AddCategory 
        :show="showModal" 
        @update:show="showModal = $event"
        @fetch="$fetch"
    />
</AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { AddCategory } from '@/Components/Dashboard';
import { computed, onMounted, reactive, ref } from 'vue';
import { CTableDataCell, CTableFoot } from '@coreui/vue';
import { isEmpty, isNull } from 'lodash';
import { inject } from 'vue';

// Modal visibility state
const showModal = ref(false);

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

// In your Vue component
const $props = defineProps({
    categories: Object,
    status: String
});

const pages = computed( () => !isEmpty($data.categories) ?$data.categories.links.filter( (link: any) => !isNull(link.url)) : [] );

const $data = reactive({
    categories: Object(),
    loaders: {
        fetch: false
    }
})

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
        const { data: { categories } }: any = await router.get(route('dashboard.categories.fetch'));

        // Set the categories
        $data.categories = categories;
    } catch (error) {
        // Set the loading flag
        $data.loaders.fetch = false;

        // Log the error
        console.error('Error fetching categories', error);
    } finally {
        // Set the loading flag
        $data.loaders.fetch = false;
    }
}

const $delete = async (value:any) => {
    // Show a confirmation dialog to the user
    const { isConfirmed } = await $swal.fire({
        icon: 'question', // Icon to display in the dialog
        title: 'Delete Category', // Title of the dialog
        text: 'Are you sure you want to delete this category?', // Text content of the dialog
        showCancelButton: true // Whether to show a "Cancel" button
    });

    // If the user does not confirm, exit the function
    if (!isConfirmed) { return; }
            
    // Set the loading flag
    $data.loaders.fetch = true;

    // Fetch the categories from the server
    router.delete(
        route('dashboard.categories.delete',{ category: value.id }),
        {
            onSuccess: () => {
                // Post message
                $toast.success('Category has been deleted.');   

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

const $edit = async (value:any) => {
    try {
        // Set the loading flag
        $data.loaders.fetch = true;

        // Fetch the categories from the server
        const { data }: any = await router.put(route('dashboard.categories.show', value.id));

        // Set the categories
        $data.categories = data;
    } catch (error) {
        // Set the loading flag
        $data.loaders.fetch = false;

        // Log the error
        console.error('Error fetching categories', error);
    } finally {
        // Set the loading flag
        $data.loaders.fetch = false;
    }
}

onMounted( () => {
    $data.categories = $props.categories
})
</script>
