<template>
  <AppShell title="Ventas">
    <!-- Filters -->
    <div class="filters">
      <div class="date-row">
        <div class="field">
          <label class="label">Desde</label>
          <input type="date" v-model="dateFrom" class="date-input" @change="loadSales" />
        </div>
        <div class="field">
          <label class="label">Hasta</label>
          <input type="date" v-model="dateTo" class="date-input" @change="loadSales" />
        </div>
      </div>
      <div class="field">
        <label class="label">Cliente</label>
        <select v-model="filterClientId" class="select" @change="loadSales">
          <option value="">Todos</option>
          <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <button class="clear-btn" @click="clearFilters">Limpiar filtros</button>
    </div>

    <LoadingSpinner v-if="loading" />
    <EmptyState v-else-if="!sales.length" icon="📋" text="No hay ventas con esos filtros" />

    <div v-else>
      <div class="summary-row">
        <span>{{ sales.length }} ventas</span>
        <span class="summary-total">${{ formatPrice(dayTotal) }}</span>
      </div>

      <div class="sales-list">
        <AppCard
          v-for="s in sales" :key="s.id"
          clickable
          class="sale-row"
          @click="router.push('/ventas/' + s.id)"
        >
          <div class="sale-top">
            <span class="sale-time">{{ formatDateTime(s.createdAt) }}</span>
            <span class="sale-status" :class="s.status === 'cancelled' ? 'cancelled' : ''">
              {{ s.status === 'cancelled' ? 'Anulada' : paymentLabel(s.paymentMethod) }}
            </span>
          </div>
          <div class="sale-bottom">
            <span class="sale-client">{{ s.clientName || 'Contado' }}</span>
            <span class="sale-total" :class="s.status === 'cancelled' ? 'cancelled' : ''">${{ formatPrice(s.total) }}</span>
          </div>
        </AppCard>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getSales } from '@/api/sales.js'
import { getClients } from '@/api/clients.js'
import AppShell from '@/components/layout/AppShell.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const router = useRouter()
const sales = ref([])
const clients = ref([])
const loading = ref(false)

const today = new Date().toISOString().slice(0, 10)
const dateFrom = ref(today)
const dateTo = ref(today)
const filterClientId = ref('')

const dayTotal = computed(() =>
  sales.value.filter(s => s.status !== 'cancelled').reduce((sum, s) => sum + s.total, 0)
)

async function loadSales() {
  loading.value = true
  try {
    const data = await getSales({
      dateFrom: dateFrom.value || undefined,
      dateTo:   dateTo.value   || undefined,
      clientId: filterClientId.value || undefined,
    })
    sales.value = Array.isArray(data) ? data : (data.sales || [])
  } finally {
    loading.value = false
  }
}

function clearFilters() {
  dateFrom.value = ''
  dateTo.value = ''
  filterClientId.value = ''
  loadSales()
}

onMounted(async () => {
  const cData = await getClients()
  clients.value = Array.isArray(cData) ? cData : (cData.clients || [])
  await loadSales()
})

function formatPrice(cents) {
  return ((cents || 0) / 100).toFixed(2)
}

function formatDateTime(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit' }) + ' ' + dt.split(' ')[1]?.slice(0, 5)
}

function paymentLabel(m) {
  const map = { efectivo: 'Efectivo', transferencia: 'Transferencia', debito: 'Débito', credito: 'Crédito', fiado: 'Fiado' }
  return map[m] || m
}
</script>

<style scoped>
.filters { display: flex; flex-direction: column; gap: var(--gap-sm); margin-bottom: var(--gap); }
.date-row { display: flex; gap: var(--gap-sm); }
.date-row .field { flex: 1; }
.field { display: flex; flex-direction: column; gap: 4px; }
.label { font-size: var(--font-sm); font-weight: 600; color: var(--color-text-light); }
.date-input { width: 100%; min-height: 44px; padding: 0 8px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); background: var(--color-input-bg); color: var(--color-text); font-size: var(--font-sm); box-sizing: border-box; }
.select { min-height: 44px; font-size: var(--font-sm); }
.clear-btn { align-self: flex-start; font-size: var(--font-sm); color: var(--color-primary); font-weight: 600; background: none; border: none; cursor: pointer; padding: 2px 0; }
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; font-size: var(--font-md); font-weight: 600; border-bottom: 2px solid var(--color-border); margin-bottom: var(--gap); }
.summary-total { font-size: var(--font-xl); color: var(--color-primary); font-weight: 800; }
.sales-list { display: flex; flex-direction: column; gap: var(--gap-sm); }
.sale-top, .sale-bottom { display: flex; justify-content: space-between; align-items: center; }
.sale-top { margin-bottom: 4px; }
.sale-time { font-size: var(--font-sm); color: var(--color-text-light); }
.sale-status { font-size: var(--font-sm); font-weight: 600; color: var(--color-primary); }
.sale-client { font-size: var(--font-md); font-weight: 600; }
.sale-total { font-size: var(--font-lg); font-weight: 700; color: var(--color-primary); }
.cancelled { color: var(--color-danger) !important; text-decoration: line-through; }
</style>
