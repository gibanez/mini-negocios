<template>
  <AppShell title="Inicio">
    <!-- Stats del día -->
    <section class="stats-row">
      <div class="stat-card">
        <span class="stat-val">{{ todaySales }}</span>
        <span class="stat-lbl">Ventas hoy</span>
      </div>
      <div class="stat-card">
        <span class="stat-val">{{ formatMoney(todayRevenue) }}</span>
        <span class="stat-lbl">Recaudado hoy</span>
      </div>
      <div class="stat-card">
        <span class="stat-val">{{ totalSalesCount }}</span>
        <span class="stat-lbl">Total ventas</span>
      </div>
    </section>

    <!-- Acción rápida -->
    <section class="quick-actions">
      <BigBtn @click="router.push('/vender')">💰 Nueva venta</BigBtn>
    </section>

    <!-- Búsqueda rápida de producto -->
    <section class="search-section">
      <h2 class="section-title">Buscar producto</h2>
      <BigInput
        v-model="searchQuery"
        placeholder="Escribí el nombre..."
        @input="onSearch"
      />
      <div v-if="searchResults.length" class="search-results">
        <div
          v-for="p in searchResults"
          :key="p.id"
          class="product-row"
          @click="router.push(`/catalogo/producto/${p.id}`)"
        >
          <div class="product-info">
            <span class="product-name">{{ p.name }}</span>
            <span class="product-unit">{{ p.unit }}</span>
          </div>
          <div class="product-right">
            <span class="product-price">{{ p.price ? formatMoney(p.price) : 'Sin precio' }}</span>
            <span class="product-stock" :class="{ low: p.stock <= 5 }">Stock: {{ p.stock }}</span>
          </div>
        </div>
      </div>
      <p v-else-if="searchQuery && !searching" class="no-results">Sin resultados.</p>
    </section>

    <!-- Ventas recientes -->
    <section class="recent-section">
      <h2 class="section-title">Ventas recientes</h2>
      <LoadingSpinner v-if="loadingSales" />
      <div v-else-if="recentSales.length" class="sales-list">
        <AppCard
          v-for="sale in recentSales"
          :key="sale.id"
          clickable
          @click="router.push(`/ventas/${sale.id}`)"
        >
          <div class="sale-row">
            <div class="sale-info">
              <span class="sale-amount">{{ formatMoney(sale.total) }}</span>
              <span class="sale-meta">{{ formatTime(sale.createdAt) }} · {{ payLabel(sale.paymentMethod) }}</span>
            </div>
            <span :class="['sale-badge', sale.status]">{{ sale.status === 'completed' ? 'Cobrada' : 'Cancelada' }}</span>
          </div>
        </AppCard>
      </div>
      <EmptyState v-else icon="📋" text="No hay ventas aún." />
    </section>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '@/components/layout/AppShell.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import BigInput from '@/components/ui/BigInput.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import { api } from '@/api/client.js'

const router = useRouter()

const allSales      = ref([])
const recentSales   = ref([])
const loadingSales  = ref(true)
const searchQuery   = ref('')
const searchResults = ref([])
const searching     = ref(false)

let searchTimer = null

const today = new Date().toISOString().slice(0, 10)

const todaySales = computed(() =>
  allSales.value.filter(s => s.status === 'completed' && s.createdAt?.slice(0, 10) === today).length
)
const todayRevenue = computed(() =>
  allSales.value
    .filter(s => s.status === 'completed' && s.createdAt?.slice(0, 10) === today)
    .reduce((sum, s) => sum + (s.total || 0), 0)
)
const totalSalesCount = computed(() =>
  allSales.value.filter(s => s.status === 'completed').length
)

function formatMoney(cents) {
  if (!cents) return '$0'
  return '$' + Math.round(cents / 100).toLocaleString('es-AR')
}

function formatTime(dt) {
  if (!dt) return ''
  return dt.slice(11, 16)
}

function payLabel(method) {
  const map = { cash: 'Efectivo', card: 'Tarjeta', transfer: 'Transferencia', credit: 'Fiado' }
  return map[method] || method || ''
}

function onSearch() {
  clearTimeout(searchTimer)
  if (!searchQuery.value.trim()) { searchResults.value = []; return }
  searching.value = true
  searchTimer = setTimeout(async () => {
    try {
      const data = await api.get('/catalog/search?q=' + encodeURIComponent(searchQuery.value))
      searchResults.value = (data.products || data || []).slice(0, 8)
    } catch (e) {
      searchResults.value = []
    } finally {
      searching.value = false
    }
  }, 300)
}

onMounted(async () => {
  try {
    const data = await api.get('/sales')
    allSales.value    = data.sales || data || []
    recentSales.value = allSales.value.slice(0, 5)
  } catch (e) {
    allSales.value = []
  } finally {
    loadingSales.value = false
  }
})
</script>

<style scoped>
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: var(--gap);
}
.stat-card {
  background: var(--color-card);
  border-radius: var(--radius);
  padding: 14px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}
.stat-val {
  font-size: 22px;
  font-weight: 700;
  color: var(--color-primary);
  line-height: 1;
}
.stat-lbl {
  font-size: 11px;
  color: var(--color-text-light);
  text-align: center;
  line-height: 1.2;
}
.quick-actions { margin-bottom: var(--gap); }
.search-section { margin-bottom: var(--gap); display: flex; flex-direction: column; gap: 10px; }
.section-title { font-size: var(--font-base); font-weight: 700; margin: 0 0 8px; color: var(--color-text); }
.search-results {
  display: flex;
  flex-direction: column;
  gap: 2px;
  background: var(--color-card);
  border-radius: var(--radius);
  overflow: hidden;
}
.product-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px;
  cursor: pointer;
  border-bottom: 1px solid var(--color-border);
  transition: background .1s;
}
.product-row:last-child { border-bottom: none; }
.product-row:active { background: var(--color-bg); }
.product-info { display: flex; flex-direction: column; gap: 2px; }
.product-name { font-size: var(--font-base); font-weight: 600; color: var(--color-text); }
.product-unit { font-size: var(--font-sm); color: var(--color-text-light); }
.product-right { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
.product-price { font-size: var(--font-base); font-weight: 700; color: var(--color-primary); }
.product-stock { font-size: var(--font-sm); color: var(--color-text-light); }
.product-stock.low { color: var(--color-danger); font-weight: 600; }
.no-results { text-align: center; color: var(--color-text-light); font-size: var(--font-sm); }
.recent-section { display: flex; flex-direction: column; gap: 8px; }
.sale-row { display: flex; align-items: center; justify-content: space-between; }
.sale-info { display: flex; flex-direction: column; gap: 2px; }
.sale-amount { font-size: var(--font-lg); font-weight: 700; color: var(--color-text); }
.sale-meta { font-size: var(--font-sm); color: var(--color-text-light); }
.sale-badge { padding: 2px 10px; border-radius: 999px; font-size: var(--font-sm); font-weight: 600; }
.sale-badge.completed { background: #dcfce7; color: #16a34a; }
.sale-badge.cancelled { background: #fee2e2; color: #dc2626; }
</style>
