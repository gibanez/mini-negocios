<template>
  <div>
    <h2 class="page-title">Dashboard</h2>

    <el-row :gutter="16" style="margin-bottom: 24px;">
      <el-col
        v-for="s in statCards"
        :key="s.label"
        :xs="12" :sm="8" :md="4"
        style="margin-bottom: 12px;"
      >
        <el-card shadow="hover" body-style="padding: 16px;">
          <div class="stat-card">
            <el-icon :size="28" :color="s.color"><component :is="s.icon" /></el-icon>
            <div class="stat-num">{{ s.value }}</div>
            <div class="stat-label">{{ s.label }}</div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <el-card shadow="never">
      <template #header>
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <span style="font-weight: 600;">Negocios registrados</span>
          <el-button type="primary" size="small" @click="router.push('/stores')">
            Ver todos
          </el-button>
        </div>
      </template>
      <el-table :data="recentStores" stripe style="width: 100%;" v-loading="loading">
        <el-table-column prop="name" label="Nombre" min-width="160" />
        <el-table-column prop="industry" label="Rubro" min-width="120">
          <template #default="{ row }">{{ row.industry || '—' }}</template>
        </el-table-column>
        <el-table-column prop="status" label="Estado" width="120">
          <template #default="{ row }">
            <el-tag :type="row.status === 'active' ? 'success' : 'danger'" size="small">
              {{ row.status === 'active' ? 'Activo' : 'Suspendido' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="Registro" width="115">
          <template #default="{ row }">{{ row.createdAt?.slice(0, 10) }}</template>
        </el-table-column>
        <el-table-column label="" width="80" align="center">
          <template #default="{ row }">
            <el-button size="small" @click="router.push(`/stores/${row.id}`)">Ver</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { platform } from '@/api/platform.js'

const router  = useRouter()
const stats   = ref(null)
const stores  = ref([])
const loading = ref(true)

const recentStores = computed(() => stores.value.slice(0, 10))

const statCards = computed(() => {
  if (!stats.value) return []
  return [
    { label: 'Total negocios',  value: stats.value.totalStores,     icon: 'Shop',         color: 'var(--el-color-primary)' },
    { label: 'Activos',         value: stats.value.activeStores,    icon: 'CircleCheck',  color: 'var(--el-color-success)' },
    { label: 'Suspendidos',     value: stats.value.suspendedStores, icon: 'CircleClose',  color: 'var(--el-color-danger)'  },
    { label: 'Usuarios',        value: stats.value.totalAdmins,     icon: 'User',         color: 'var(--el-color-warning)' },
    { label: 'Ventas',          value: stats.value.totalSales,      icon: 'ShoppingCart', color: 'var(--el-color-info)'    },
    { label: 'Ingresos totales', value: formatMoney(stats.value.totalRevenue), icon: 'Money', color: 'var(--el-color-success)' },
  ]
})

function formatMoney(cents) {
  if (!cents) return '$0'
  return '$' + (cents / 100).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

onMounted(async () => {
  try {
    const [s, st] = await Promise.all([platform.stats(), platform.listStores()])
    stats.value  = s
    stores.value = st
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page-title { margin: 0 0 20px; font-size: 20px; font-weight: 600; color: var(--el-text-color-primary); }
.stat-card { display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center; }
.stat-num { font-size: 28px; font-weight: 700; line-height: 1; color: var(--el-text-color-primary); }
.stat-label { font-size: 12px; color: var(--el-text-color-secondary); }
</style>
