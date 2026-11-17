<template>
  <div :class="isDark ? 'dark' : ''">
    <router-view />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAdminStore } from '@/stores/auth.js'
const admin = useAdminStore()
const isDark = computed(() => admin.darkMode)
</script>
