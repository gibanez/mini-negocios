<template>
  <el-container class="admin-shell" style="height: 100vh;">
    <el-aside :width="collapsed ? '64px' : '220px'" class="admin-aside">
      <div class="sidebar-logo">
        <span v-if="!collapsed" class="logo-text">🏪 Despensa</span>
        <span v-else style="font-size:22px;">🏪</span>
      </div>
      <el-menu
        :default-active="$route.path"
        :collapse="collapsed"
        router
        style="border-right: none; height: calc(100vh - 60px); overflow-y: auto;"
      >
        <el-menu-item index="/dashboard">
          <el-icon><DataAnalysis /></el-icon>
          <template #title>Dashboard</template>
        </el-menu-item>
        <el-menu-item index="/stores">
          <el-icon><Shop /></el-icon>
          <template #title>Negocios</template>
        </el-menu-item>
      </el-menu>
    </el-aside>

    <el-container>
      <el-header class="admin-header">
        <div style="display:flex; align-items:center; gap:12px;">
          <el-button
            :icon="collapsed ? Expand : Fold"
            circle
            @click="collapsed = !collapsed"
          />
          <el-breadcrumb separator="/">
            <el-breadcrumb-item :to="{ path: '/dashboard' }">Admin</el-breadcrumb-item>
            <el-breadcrumb-item v-if="pageTitle">{{ pageTitle }}</el-breadcrumb-item>
          </el-breadcrumb>
        </div>
        <div style="display:flex; align-items:center; gap:16px;">
          <el-switch
            v-model="isDark"
            @change="toggleDark"
            :active-icon="Moon"
            :inactive-icon="Sunny"
            inline-prompt
          />
          <el-dropdown @command="handleCmd">
            <span class="user-btn">
              <el-avatar size="small" :style="{ background: 'var(--el-color-primary)' }">
                {{ auth.user?.name?.charAt(0)?.toUpperCase() || 'A' }}
              </el-avatar>
              <span class="user-name">{{ auth.user?.name }}</span>
              <el-icon><ArrowDown /></el-icon>
            </span>
            <template #dropdown>
              <el-dropdown-menu>
                <el-dropdown-item command="logout">Cerrar sesión</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </el-header>

      <el-main style="overflow-y: auto;">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Moon, Sunny, Expand, Fold, ArrowDown } from '@element-plus/icons-vue'
import { useAdminStore } from '@/stores/auth.js'

const auth      = useAdminStore()
const router    = useRouter()
const route     = useRoute()
const collapsed = ref(false)
const isDark    = ref(auth.darkMode)

const titles = {
  '/dashboard': null,
  '/stores': 'Negocios',
}

const pageTitle = computed(() => {
  if (route.path === '/dashboard') return null
  if (route.path.includes('/users')) return 'Usuarios'
  if (route.path.startsWith('/stores/')) return 'Detalle de negocio'
  return titles[route.path] || null
})

function toggleDark() {
  auth.toggleDark()
}

watch(() => auth.darkMode, (val) => {
  isDark.value = val
})

function handleCmd(cmd) {
  if (cmd === 'logout') {
    auth.logout()
    router.push('/login')
  }
}
</script>

<style scoped>
.admin-shell {
  background: var(--el-bg-color-page);
}
.admin-aside {
  transition: width .2s;
  overflow: hidden;
  border-right: 1px solid var(--el-border-color);
}
.sidebar-logo {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  border-bottom: 1px solid var(--el-border-color);
  white-space: nowrap;
  overflow: hidden;
  color: var(--el-color-primary);
}
.logo-text { font-size: 15px; }
.admin-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--el-border-color);
  background: var(--el-bg-color);
}
.user-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  color: var(--el-text-color-primary);
}
.user-name {
  font-size: 14px;
}
</style>
