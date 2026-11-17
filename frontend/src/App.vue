<template>
  <router-view />
  <BottomNav v-if="showNav" />
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import BottomNav from '@/components/layout/BottomNav.vue'

const route = useRoute()

const NO_NAV = ['/login', '/register', '/onboarding', '/public']

const showNav = computed(() => {
  const p = route.path
  if (!p) return false
  if (p.startsWith('/admin')) return false
  return !NO_NAV.some(prefix => p.startsWith(prefix))
})
</script>
