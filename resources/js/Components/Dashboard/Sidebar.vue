<template>
<CSidebar position="fixed" colorScheme="dark">
  <CSidebarHeader class="border-bottom">
    <CSidebarBrand :href="route('dashboard.overview')" class="w-100 d-flex justify-content-center text-decoration-none">
      <img v-if="company?.logo" :src="company.logo" :alt="company.name" class="sidebar-logo" width="100%">
      <span v-else class="fw-semibold text-white">{{ company?.name }}</span>
    </CSidebarBrand>
  </CSidebarHeader>
  <CSidebarNav>
    <CNavItem :href="route('dashboard.overview')">
      <CIcon customClassName="nav-icon" icon="cil-speedometer"/> Overview
    </CNavItem>
    <CNavTitle>Data Management</CNavTitle>
    <CNavItem :href="route('dashboard.clients')">
      <CIcon  customClassName="nav-icon" icon="cil-group"/> Clients
    </CNavItem>
    <CNavGroup>
      <template #togglerContent>
        <CIcon  customClassName="nav-icon" icon="cil-list"/> Posting Categorization
      </template>
      <CNavItem :href="route('dashboard.categories')">
        <CIcon  customClassName="nav-icon" /> Categories
      </CNavItem>
      <CNavItem :href="route('dashboard.sub_categories')">
        <CIcon  customClassName="nav-icon" /> Sub Categories
      </CNavItem>
      <CNavItem :href="route('dashboard.child_sub_categories')">
        <CIcon  customClassName="nav-icon" /> Child Sub Categories
      </CNavItem>      
    </CNavGroup>    
    <CNavItem :href="route('dashboard.postings')">
      <CIcon  customClassName="nav-icon" icon="cil-applications"/> Postings
    </CNavItem>
    <CNavItem :href="route('dashboard.staff')">
      <CIcon  customClassName="nav-icon" icon="cil-user"/> Staff
    </CNavItem>
    <CNavItem :href="route('dashboard.subscriptions')">
      <CIcon  customClassName="nav-icon" icon="cil-list"/> Subscriptions
    </CNavItem>
    <CNavItem :href="route('dashboard.transactions')">
      <CIcon  customClassName="nav-icon" icon="cil-money"/> Transactions
    </CNavItem> 
    <CNavItem :href="route('dashboard.placements')">
      <CIcon  customClassName="nav-icon" icon="cil-bookmark"/> Advert Placements
    </CNavItem>          
    <CNavTitle>Settings</CNavTitle>
    <CNavItem :href="route('dashboard.profile')">
      <CIcon  customClassName="nav-icon" icon="cil-user"/> Profile
    </CNavItem>
    <CNavItem :href="route('dashboard.company')">
      <CIcon  customClassName="nav-icon" icon="cil-building"/> Company
    </CNavItem>
    <CNavItem :href="route('dashboard.system')">
      <CIcon  customClassName="nav-icon" icon="cil-cog"/> System
    </CNavItem>
  </CSidebarNav>
  <CSidebarFooter class="border-top">
    <CSidebarToggler/>
  </CSidebarFooter>
</CSidebar>
</template>
<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

// The company details (name, logo, icon) shared with every page.
const company = computed(() => usePage().props.company)

// import { RouterLink } from 'vue-router'

// import { logo } from '@/assets/brand/logo'
// import { sygnet } from '@/assets/brand/sygnet'
// import { AppSidebarNav } from '@/components/AppSidebarNav.js'
// import { useSidebarStore } from '@/stores/sidebar.js'

// const sidebar = useSidebarStore()
</script>
<style scoped>
.sidebar-logo {
  height: 40px;
  width: auto;
  max-width: 100%;
  object-fit: contain;
}
</style>
