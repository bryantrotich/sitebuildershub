<template>
  <CDropdown placement="bottom-end" variant="nav-item">
    <CDropdownToggle class="py-0 pe-0 d-flex align-items-center" :caret="false">
      <span class="d-none d-md-block text-end me-2 lh-sm">
        <span class="d-block fw-semibold small">{{ user?.name }}</span>
        <span class="d-block text-body-secondary small">{{ user?.email }}</span>
      </span>
      <CAvatar v-if="user?.pictureUrl" :src="user.pictureUrl" size="md" />
      <CAvatar v-else color="primary" text-color="white" size="md">{{ initials }}</CAvatar>
    </CDropdownToggle>
    <CDropdownMenu class="pt-0">
      <CDropdownHeader
        component="h6"
        class="bg-body-secondary text-body-secondary fw-semibold mb-2 rounded-top"
      >
        Account
      </CDropdownHeader>
      <CDropdownItem :href="route('dashboard.profile')"> <CIcon icon="cil-user" /> Profile </CDropdownItem>
      <CDropdownItem :href="route('dashboard.company')"> <CIcon icon="cil-building" /> Company </CDropdownItem>
      <CDropdownItem :href="route('dashboard.system')"> <CIcon icon="cil-cog" /> System </CDropdownItem>
      <CDropdownDivider />
      <CDropdownItem @click="logout"> <CIcon icon="cil-lock-locked" /> Logout </CDropdownItem>
    </CDropdownMenu>
  </CDropdown>
</template>
<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const user: any = computed(() => usePage().props.auth.user)

const initials = computed(() => `${user.value?.first_name?.[0] ?? ''}${user.value?.last_name?.[0] ?? ''}`.toUpperCase())

const logout = async () => {
  try{
    const data = router.post(route('dashboard.logout'));
    console.log(data);
  } catch(error) {
    console.log(error);
  }
}
</script>
