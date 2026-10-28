<template>
    <CCol :md="6" :xl="4">
        <CCard class="h-100 border-0 shadow-sm overflow-hidden">
            <CCardImage v-if="!isEmpty($props.data.link_images)" orientation="top" :src="$props.data.link_images[0]" style="height: 180px; object-fit: cover;" />
            <div v-else class="d-flex align-items-center justify-content-center bg-body-secondary" style="height: 180px;">
                <CAvatar color="primary" size="xl" class="text-white">{{ $props.data.title.charAt(0) }}</CAvatar>
            </div>
            <CCardBody class="d-flex flex-column">
                <div class="d-flex align-items-start mb-2">
                    <h5 class="mb-0 text-truncate">{{ $props.data.title }}</h5>
                    <CDropdown class="ms-auto">
                        <CDropdownToggle color="link" :caret="false" class="p-0"><CIcon icon="cil-options"/></CDropdownToggle>
                        <CDropdownMenu>
                            <CDropdownItem :href="route('dashboard.postings.show', $props.data.id)">View</CDropdownItem>
                        </CDropdownMenu>
                    </CDropdown>
                </div>
                <div class="small text-body-secondary mb-2">
                    <CIcon icon="cil-user" class="me-1" />{{ $props.data.user?.name }}
                    <span class="mx-1">·</span>
                    <CIcon icon="cil-location-pin" class="me-1" />{{ $props.data.town }}, {{ $props.data.county }}
                </div>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    <CBadge color="success" v-if="$props.data.show">Visible</CBadge>
                    <CBadge color="secondary" v-else>Hidden</CBadge>
                    <CBadge color="info" v-for="category in $props.data.categories" :key="category.id">{{ category.name }}</CBadge>
                </div>
                <CRow class="mt-auto text-center g-0 border rounded">
                    <CCol class="py-2 border-end">
                        <div class="fw-semibold">{{ $props.data.views_total ?? 0 }}</div>
                        <div class="small text-body-secondary">Views</div>
                    </CCol>
                    <CCol class="py-2 border-end">
                        <div class="fw-semibold">{{ $props.data.comments_total ?? 0 }}</div>
                        <div class="small text-body-secondary">Comments</div>
                    </CCol>
                    <CCol class="py-2">
                        <div class="fw-semibold small">{{ $props.data.created_at }}</div>
                        <div class="small text-body-secondary">Posted</div>
                    </CCol>
                </CRow>
            </CCardBody>
        </CCard>
    </CCol>
</template>
<script lang="ts" setup>
import { isEmpty } from 'lodash';

// In your Vue component
const $props: any = defineProps({
    data: Object,
});
</script>
