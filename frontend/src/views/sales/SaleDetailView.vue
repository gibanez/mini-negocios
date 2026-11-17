<template>
  <AppShell title="Detalle de venta" back="/ventas">
    <LoadingSpinner v-if="loading" />
    <div v-else-if="sale">
      <AppCard class="sale-info">
        <div class="info-row"><span>Estado</span><strong :class="sale.status === 'cancelled' ? 'text-danger' : 'text-success'">{{ sale.status === 'cancelled' ? 'Anulada' : 'Activa' }}</strong></div>
        <div class="info-row"><span>Fecha</span><strong>{{ sale.createdAt }}</strong></div>
        <div class="info-row"><span>Pago</span><strong>{{ paymentLabel(sale.paymentMethod) }}</strong></div>
        <div v-if="sale.clientName" class="info-row"><span>Cliente</span><strong>{{ sale.clientName }}</strong></div>
        <div v-if="sale.status === 'cancelled'" class="info-row"><span>Motivo</span><strong>{{ sale.cancelReason }}</strong></div>
      </AppCard>

      <h3 class="items-title">Productos</h3>
      <AppCard v-for="item in items" :key="item.id" class="item-card">
        <div class="item-row">
          <span class="item-name">{{ item.productName }}</span>
          <span class="item-qty">× {{ item.quantity / 1000 }}</span>
          <span class="item-sub">${{ formatPrice(item.subtotal) }}</span>
        </div>
        <div class="item-unit">${{ formatPrice(item.unitPrice) }} c/u</div>
      </AppCard>

      <div class="total-box">
        <span>Total</span>
        <span class="total-val">${{ formatPrice(sale.total) }}</span>
      </div>

      <!-- Cancel button (owner only, same day, active) -->
      <BigBtn
        v-if="canCancel"
        variant="danger"
        class="cancel-btn"
        @click="showCancel = true"
      >Anular venta</BigBtn>

      <!-- Cancel form -->
      <div v-if="showCancel" class="cancel-form">
        <BigInput v-model="cancelReason" label="Motivo de anulación" placeholder="Explicá el motivo..." :error="errors.reason" />
        <BigBtn variant="danger" :loading="cancelling" @click="doCancel">Confirmar anulación</BigBtn>
        <BigBtn variant="ghost" @click="showCancel = false">Cancelar</BigBtn>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getSale, cancelSale } from '@/api/sales.js'
import { useAuthStore } from '@/stores/auth.js'
import AppShell from '@/components/layout/AppShell.vue'
import AppCard from '@/components/ui/AppCard.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import BigInput from '@/components/ui/BigInput.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const id = route.params.id
const sale = ref(null)
const items = ref([])
const loading = ref(false)
const showCancel = ref(false)
const cancelReason = ref('')
const cancelling = ref(false)
const errors = ref({})

const today = new Date().toISOString().slice(0, 10)

const canCancel = computed(() => {
  if (!sale.value) return false
  if (sale.value.status === 'cancelled') return false
  if (!auth.isOwner) return false
  const saleDate = sale.value.createdAt?.slice(0, 10)
  return saleDate === today
})

onMounted(async () => {
  loading.value = true
  try {
    const data = await getSale(id)
    sale.value = data
    items.value = data.items || []
  } finally {
    loading.value = false
  }
})

function formatPrice(cents) {
  return (cents / 100).toFixed(2)
}

function paymentLabel(m) {
  const map = { efectivo: 'Efectivo', transferencia: 'Transferencia', debito: 'Débito', credito: 'Crédito', fiado: 'Fiado' }
  return map[m] || m
}

async function doCancel() {
  errors.value = {}
  if (!cancelReason.value.trim()) { errors.value.reason = 'El motivo es obligatorio'; return }
  cancelling.value = true
  try {
    await cancelSale(id, cancelReason.value)
    const data = await getSale(id)
    sale.value = data.sale
    showCancel.value = false
  } catch (e) {
    errors.value.reason = e.message
  } finally {
    cancelling.value = false
  }
}
</script>

<style scoped>
.sale-info { margin-bottom: var(--gap); }
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--color-border); font-size: var(--font-md); }
.info-row:last-child { border-bottom: none; }
.text-danger { color: var(--color-danger); }
.text-success { color: var(--color-success); }
.items-title { font-size: var(--font-lg); margin: 0 0 var(--gap-sm); }
.item-card { margin-bottom: var(--gap-sm); }
.item-row { display: flex; align-items: center; gap: 8px; }
.item-name { flex: 1; font-weight: 600; }
.item-qty { color: var(--color-text-light); font-size: var(--font-sm); }
.item-sub { font-weight: 700; color: var(--color-primary); }
.item-unit { font-size: var(--font-sm); color: var(--color-text-light); margin-top: 4px; }
.total-box { display: flex; justify-content: space-between; padding: var(--gap) 0; font-size: var(--font-lg); font-weight: 700; border-top: 2px solid var(--color-border); margin-top: var(--gap-sm); }
.total-val { font-size: var(--font-xl); color: var(--color-primary); }
.cancel-btn { margin-top: var(--gap); }
.cancel-form { margin-top: var(--gap); display: flex; flex-direction: column; gap: var(--gap); }
</style>
