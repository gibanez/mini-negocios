<template>
  <div class="admin-dash">
    <header class="admin-header">
      <h1>Panel de Plataforma</h1>
      <span class="badge-platform">Admin</span>
    </header>

    <div class="stats-grid" v-if="stats">
      <div class="stat-card">
        <span class="stat-num">{{ stats.totalStores }}</span>
        <span class="stat-label">Negocios</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">{{ stats.activeStores }}</span>
        <span class="stat-label">Activos</span>
      </div>
      <div class="stat-card danger" v-if="stats.suspendedStores > 0">
        <span class="stat-num">{{ stats.suspendedStores }}</span>
        <span class="stat-label">Suspendidos</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">{{ stats.totalAdmins }}</span>
        <span class="stat-label">Usuarios</span>
      </div>
      <div class="stat-card">
        <span class="stat-num">{{ stats.totalSales }}</span>
        <span class="stat-label">Ventas</span>
      </div>
    </div>

    <div class="search-row">
      <input class="search-input" v-model="search" placeholder="Buscar negocio..." />
    </div>

    <div class="stores-list" v-if="!loading">
      <div
        v-for="store in filtered"
        :key="store.id"
        class="store-row"
        @click="router.push(`/admin/stores/${store.id}`)"
      >
        <div class="store-info">
          <span class="store-name">{{ store.name }}</span>
          <span class="store-industry">{{ store.industry || 'Sin rubro' }}</span>
        </div>
        <span :class="['store-badge', store.status]">{{ store.status === 'active' ? 'Activo' : 'Suspendido' }}</span>
      </div>
      <p v-if="filtered.length === 0" class="empty">No hay negocios.</p>
    </div>
    <div v-else class="loading">Cargando...</div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { platform } from '@/api/platform.js'

const router = useRouter()
const stats = ref(null)
const stores = ref([])
const loading = ref(true)
const search = ref('')

const filtered = computed(() => {
  if (!search.value) return stores.value
  const q = search.value.toLowerCase()
  return stores.value.filter(s => s.name.toLowerCase().includes(q))
})

onMounted(async () => {
  try {
    const [s, st] = await Promise.all([platform.stats(), platform.listStores()])
    stats.value = s
    stores.value = st
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.admin-dash { padding: 16px; max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px; }
.admin-header { display: flex; align-items: center; gap: 12px; }
.admin-header h1 { font-size: var(--font-lg); font-weight: 700; margin: 0; color: var(--color-text); }
.badge-platform { background: #7c3aed; color: #fff; padding: 2px 10px; border-radius: 999px; font-size: var(--font-sm); }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
.stat-card { background: var(--color-card); border-radius: var(--radius); padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 2px; }
.stat-card.danger { background: var(--color-danger); color: #fff; }
.stat-num { font-size: 28px; font-weight: 700; line-height: 1; }
.stat-label { font-size: var(--font-sm); color: var(--color-text-light); }
.stat-card.danger .stat-label { color: rgba(255,255,255,0.8); }
.search-row { display: flex; }
.search-input { flex: 1; padding: 10px 14px; border: 2px solid var(--color-border); border-radius: var(--radius); font-size: var(--font-base); background: var(--color-input-bg); color: var(--color-text); }
.stores-list { display: flex; flex-direction: column; gap: 8px; }
.store-row { background: var(--color-card); border-radius: var(--radius); padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: opacity .15s; }
.store-row:active { opacity: .7; }
.store-info { display: flex; flex-direction: column; gap: 2px; }
.store-name { font-size: var(--font-base); font-weight: 600; color: var(--color-text); }
.store-industry { font-size: var(--font-sm); color: var(--color-text-light); }
.store-badge { padding: 2px 10px; border-radius: 999px; font-size: var(--font-sm); font-weight: 600; }
.store-badge.active { background: #dcfce7; color: #16a34a; }
.store-badge.suspended { background: #fee2e2; color: #dc2626; }
.empty { text-align: center; color: var(--color-text-light); padding: 32px 0; }
.loading { text-align: center; color: var(--color-text-light); padding: 48px 0; }
</style>
