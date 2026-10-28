<template>
<AuthenticatedLayout>
    <Head title="Child Sub Categories" />
    <CRow>
        <CCol md="6">
            <h4>Child Sub Categories</h4>
            <p class="text-muted">Manage your website child sub categories to organize content effectively.</p>
        </CCol>
        <CCol md="6">
            <CButton color="primary" class="float-end" @click="showModal = true">Add Child Sub Category</CButton>
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
                        <CTableHeaderCell scope="col">Sub Category</CTableHeaderCell>
                        <CTableHeaderCell scope="col">Created On</CTableHeaderCell>
                        <CTableHeaderCell scope="col"></CTableHeaderCell>
                    </CTableRow>
                </CTableHead>
                <CTableBody>
                    <CTableRow v-if="isEmpty($props.child_sub_categories.data)">
                        <CTableDataCell colspan="6" class="text-center">
                            <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                        </CTableDataCell>
                    </CTableRow>
                    <CTableRow v-for="(sub_category, index) in $props.child_sub_categories.data" :key="sub_category.id">
                        <CTableHeaderCell scope="row">{{ ($props.child_sub_categories.from ?? 1) + index }}</CTableHeaderCell>
                        <CTableDataCell class="fw-semibold">{{ sub_category.name }}</CTableDataCell>
                        <CTableDataCell><CBadge color="primary">{{ sub_category.category?.name }}</CBadge></CTableDataCell>
                        <CTableDataCell><CBadge color="info">{{ sub_category.sub_category?.name }}</CBadge></CTableDataCell>
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
    <AddChildSubCategory 
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
import { AddChildSubCategory } from '@/Components/Dashboard';
import { computed, onMounted, reactive, ref } from 'vue';
import { isEmpty, isNull } from 'lodash';

// Modal visibility state
const showModal = ref(false);

// In your Vue component
const $props: any = defineProps({
    categories:           Array,
    child_sub_categories: Object,
    status:               String
});

const $data = reactive({
    child_sub_categories: Object(),
    loaders: {
        fetch: false
    }
});

const pages = computed( () => !isEmpty($props.child_sub_categories) ? $props.child_sub_categories.links.filter( (link: any) => !isNull(link.url)) : [] );

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
        const { data }: any = await router.get(route('dashboard.childsubcategories'));

        // Set the categories
        $data.child_sub_categories = data;
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

const $delete = (value:any) => {

}

const $edit = (value:any) => {

}

onMounted( () => {
    $data.categories = $props.categories
})
</script>
