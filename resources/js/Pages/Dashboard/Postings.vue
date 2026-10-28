<template>
  <AuthenticatedLayout>
    <Head title="Postings" />
    <CRow>
      <CCol xs="12">
        <CRow>
            <CCol md="6">
                <h4>Postings</h4>
                <p class="text-muted">Browse and review postings created by clients on the platform.</p>
            </CCol>
        </CRow>
      </CCol>
      <CCol xs="12">
        <CRow :xs="{ gutter: 4 }">
          <Deferred data="postings">
              <template #fallback>
                  <div>Loading...</div>
              </template>
              <template v-if="isEmpty($props.postings.data)">
                <CCol md="12">
                  <CCard class="border-0 shadow-sm">
                    <CCardBody class="text-center">
                      <h6 class="mb-0"><CIcon icon="cil-ban"/> Nothing Found Here. </h6>
                    </CCardBody>
                  </CCard>
                </CCol>
              </template>
              <template v-if="!isEmpty($props.postings.data)" v-for="(posting,index) in $props.postings.data" :key="posting.id">
                  <Posting :data="posting" />
              </template>
          </Deferred>
        </CRow>
      </CCol>
      <CCol md="12" class="mt-4" v-if="!isEmpty($props.postings.data)">
          <CPagination aria-label="Page navigation example" size="sm" align="center">
              <CPaginationItem :href="page.url" v-for="(page, index) in pages" :key="index" :active="page.active"><span v-html="page.label"></span></CPaginationItem>
          </CPagination>         
      </CCol>
    </CRow>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Posting } from '@/Components/Dashboard';
import { Deferred, Head } from '@inertiajs/vue3';
import { isEmpty, isNull } from 'lodash';
import { computed, inject } from 'vue';

const $swal: any  = inject('$swal');  
const $toast: any = inject('$toast');  

// In your Vue component
const $props: any = defineProps({
    postings: Object,
    status: String
});

const pages = computed( () => !isEmpty($props.postings) ?$props.postings.links.filter( (link: any) => !isNull(link.url)) : [] );


</script>
  