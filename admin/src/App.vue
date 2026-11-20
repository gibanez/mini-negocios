<template>
  <router-view />
</template>

<script setup>
import { watch } from 'vue'
import { useAdminStore } from '@/stores/auth.js'

const admin = useAdminStore()

function applyDark(val) {
  document.documentElement.classList.toggle('dark', val)
}

applyDark(admin.darkMode)
watch(() => admin.darkMode, applyDark)
</script>
