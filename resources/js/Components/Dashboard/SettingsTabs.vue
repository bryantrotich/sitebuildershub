<template>
    <CCol xs="12">
        <h4>Settings</h4>
        <p class="text-muted">{{ $props.description }}</p>
    </CCol>
    <CNav variant="tabs" class="mb-4">
        <CNavItem v-for="tab in tabs" :key="tab.route">
            <Link :href="route(tab.route)" :class="['nav-link', { active: route().current(tab.route) }]">
                <CIcon :icon="tab.icon" class="me-2" />{{ tab.label }}
            </Link>
        </CNavItem>
    </CNav>
</template>
<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';

// In your Vue component
const $props: any = defineProps({
    description: String,
});

const tabs = [
    { label: 'Profile', route: 'dashboard.profile', icon: 'cil-user' },
    { label: 'Company', route: 'dashboard.company', icon: 'cil-building' },
    { label: 'System',  route: 'dashboard.system',  icon: 'cil-cog' },
];
</script>
