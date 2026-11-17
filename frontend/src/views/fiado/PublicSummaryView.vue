<template>
  <div class="public-page" :data-theme="theme">
    <LoadingSpinner v-if="loading" />
    <div v-else-if="error" class="error-state">
      <p class="error-icon">😕</p>
      <p class="error-text">{{ error }}</p>
    </div>
    <div v-else>
      <div class="store-header">
        <div class="store-logo">🏪</div>
        <h1 class="store-name">{{ summary.storeName }}</h1>
        <p class="client-name">Cuenta de {{ summary.clientName }}</p>
      </div>

      <div class="balance-box" :class="summary.balance > 0 ? 'owed' : 'clear'">
        <p class="balance-label">Saldo adeudado</p>
        <p class="balance-amount">${{ formatPrice(summary.balance) }}</p>
      </div>

      <!-- Total debt -->
      <div class="debt-box">
        <span class="debt-label">Total deuda</span>
        <span class="debt-amount">${{ formatPrice(totalDebt) }}</span>
      </div>

      <!-- Payments -->
      <div class="movements">
        <h2 class="section-title">Pagos realizados</h2>
        <p v-if="!activePayments.length" class="empty-pay">Sin pagos registrados aún</p>
        <div v-for="m in activePayments" :key="m.id" class="movement">
          <div class="mov-row">
            <div class="mov-left">
              <span class="mov-type">{{ payMethodLabel(m.paymentMethod) }}</span>
              <span class="mov-date">{{ formatDate(m.createdAt) }}</span>
            </div>
            <span class="mov-amount payment">−${{ formatPrice(m.amount) }}</span>
          </div>
          <p v-if="m.notes" class="mov-note">{{ m.notes }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getPublicSummary } from '@/api/fiado.js'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const route = useRoute()
const token = route.params.token

const loading = ref(true)
const error = ref(null)
const summary = ref({})
const theme = ref('verde')

onMounted(async () => {
  try {
    const data = await getPublicSummary(token)
    summary.value = data
    theme.value = data.theme || 'verde'
  } catch (e) {
    error.value = 'No se encontró la cuenta. El link puede haber vencido.'
  } finally {
    loading.value = false
  }
})

function formatPrice(cents) {
  return ((cents || 0) / 100).toFixed(2)
}

function formatDate(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' })
}

const totalDebt = computed(() => {
  const movs = summary.value.movements || []
  return movs
    .filter(m => m.type !== 'payment' && m.status !== 'cancelled')
    .reduce((sum, m) => sum + (m.amount || 0), 0)
})

const activePayments = computed(() => {
  const movs = summary.value.movements || []
  return movs.filter(m => m.type === 'payment' && m.status !== 'cancelled')
})

function payMethodLabel(method) {
  const map = { efectivo: 'Efectivo', transferencia: 'Transferencia', debito: 'Débito', credito: 'Crédito' }
  return map[method] || method || '—'
}
</script>

<style>
/* Import base styles for public page */
@import '../../styles/base.css';
@import '../../styles/themes.css';
</style>

<style scoped>
.public-page {
  min-height: 100vh;
  background: var(--color-bg);
  color: var(--color-text);
  padding: var(--gap);
  max-width: 500px;
  margin: 0 auto;
}
.store-header { text-align: center; padding: var(--gap) 0; }
.store-logo { font-size: 56px; }
.store-name { font-size: var(--font-xl); margin: 8px 0 4px; color: var(--color-primary); }
.client-name { color: var(--color-text-light); font-size: var(--font-md); margin: 0; }
.balance-box { text-align: center; padding: var(--gap); border-radius: var(--radius-lg); margin: var(--gap) 0; }
.balance-box.owed { background: #fce4e4; }
.balance-box.clear { background: var(--color-primary-light); }
.balance-label { margin: 0; font-size: var(--font-sm); color: var(--color-text-light); }
.balance-amount { margin: 0; font-size: var(--font-2xl); font-weight: 800; }
.owed .balance-amount { color: var(--color-danger); }
.clear .balance-amount { color: var(--color-success); }
.debt-box { display: flex; justify-content: space-between; align-items: center; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--gap); margin: var(--gap) 0; }
.debt-label { font-size: var(--font-base); font-weight: 600; }
.debt-amount { font-size: var(--font-xl); font-weight: 800; color: var(--color-danger); }
.empty-pay { color: var(--color-text-light); font-size: var(--font-sm); text-align: center; padding: var(--gap) 0; }
.section-title { font-size: var(--font-lg); margin: 0 0 var(--gap); }
.movement {
  background: var(--color-surface);
  border-radius: var(--radius-md);
  padding: var(--gap);
  margin-bottom: var(--gap-sm);
  border: 1px solid var(--color-border);
}
.movement.cancelled { opacity: 0.5; }
.mov-row { display: flex; justify-content: space-between; align-items: flex-start; }
.mov-left { display: flex; flex-direction: column; gap: 2px; }
.mov-type { font-weight: 600; }
.mov-amount { font-size: var(--font-lg); font-weight: 700; }
.payment { color: var(--color-success); }
.purchase { color: var(--color-danger); }
.mov-detail { display: flex; justify-content: space-between; margin-top: 4px; }
.mov-date { font-size: var(--font-sm); color: var(--color-text-light); }
.mov-ann { font-size: var(--font-sm); color: var(--color-danger); }
.mov-note { font-size: var(--font-sm); color: var(--color-text-light); margin: 4px 0 0; }
.items-list { margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--color-border); }
.item-row { display: flex; justify-content: space-between; gap: 8px; font-size: var(--font-sm); padding: 4px 0; }
.error-state { text-align: center; padding: 48px var(--gap); }
.error-icon { font-size: 56px; margin: 0; }
.error-text { font-size: var(--font-lg); color: var(--color-text-light); }
</style>
