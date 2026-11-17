<template>
  <AppShell :title="clientName" back="/clientes">
    <LoadingSpinner v-if="loading" />
    <div v-else>
      <!-- Balance -->
      <AppCard class="balance-card">
        <p class="balance-label">Saldo adeudado</p>
        <p class="balance-amount" :class="balance > 0 ? 'owed' : 'clear'">${{ formatPrice(balance) }}</p>
        <p class="share-hint">
          <a class="share-btn" :href="whatsappUrl" target="_blank">📲 Enviar por WhatsApp</a>
        </p>
      </AppCard>

      <!-- Add debt -->
      <AppCard class="action-card">
        <h3 class="action-title">Cargar deuda</h3>
        <BigInput v-model="debtAmount" type="number" placeholder="Monto ($)" step="0.01" />
        <BigInput v-model="debtNote" placeholder="Nota (opcional)" />
        <BigBtn @click="submitDebt" :loading="submittingDebt">Agregar deuda</BigBtn>
      </AppCard>

      <!-- Add payment -->
      <AppCard class="action-card">
        <h3 class="action-title">Registrar pago</h3>
        <BigInput v-model="payAmount" type="number" placeholder="Monto ($)" step="0.01" />
        <BigInput v-model="payNote" placeholder="Nota (opcional)" />
        <BigBtn variant="secondary" @click="submitPayment" :loading="submittingPay">Registrar pago</BigBtn>
      </AppCard>

      <!-- Debt total -->
      <AppCard class="debt-card">
        <div class="debt-row">
          <span class="debt-label">Total deuda</span>
          <span class="debt-amount">${{ formatPrice(totalDebt) }}</span>
        </div>
      </AppCard>

      <!-- Payments list -->
      <h3 class="movements-title">Pagos realizados</h3>
      <EmptyState v-if="!activePayments.length" icon="💳" text="Sin pagos registrados aún" />
      <div v-else class="movements-list">
        <AppCard v-for="m in activePayments" :key="m.id" class="movement-row">
          <div class="mov-top">
            <div class="mov-left">
              <span class="mov-method">{{ payMethodLabel(m.paymentMethod) }}</span>
              <span class="mov-date">{{ formatDate(m.createdAt) }}</span>
            </div>
            <span class="mov-amount payment">−${{ formatPrice(m.amount) }}</span>
          </div>
          <div class="mov-bottom" v-if="m.notes || auth.isOwner">
            <span v-if="m.notes" class="mov-note">{{ m.notes }}</span>
            <button
              v-if="auth.isOwner"
              class="cancel-mov-btn"
              @click="startCancelPayment(m)"
            >Anular</button>
          </div>
        </AppCard>
      </div>
    </div>

    <!-- Cancel payment dialog -->
    <div v-if="cancellingMovement" class="cancel-overlay">
      <div class="cancel-panel">
        <p class="cancel-title">Anular pago de ${{ formatPrice(cancellingMovement.amount) }}</p>
        <BigInput v-model="cancelReason" label="Motivo (obligatorio)" placeholder="Explicá el motivo..." :error="cancelError" />
        <div class="cancel-btns">
          <BigBtn variant="ghost" @click="cancellingMovement = null">Volver</BigBtn>
          <BigBtn variant="danger" :loading="cancellingPay" @click="submitCancelPayment">Anular</BigBtn>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getFiadoAccount, addDebt, addPayment, cancelPayment, renewToken } from '@/api/fiado.js'
import { getClient } from '@/api/clients.js'
import { useAuthStore } from '@/stores/auth.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const route = useRoute()
const auth = useAuthStore()
const clientId = route.params.clientId

const loading = ref(false)
const clientName = ref('Fiado')
const balance = ref(0)
const movements = ref([])
const publicToken = ref('')

const debtAmount = ref('')
const debtNote = ref('')
const payAmount = ref('')
const payNote = ref('')
const submittingDebt = ref(false)
const submittingPay = ref(false)

const cancellingMovement = ref(null)
const cancelReason = ref('')
const cancelError = ref('')
const cancellingPay = ref(false)

async function load() {
  loading.value = true
  try {
    const [clientData, accountData] = await Promise.all([
      getClient(clientId),
      getFiadoAccount(clientId)
    ])
    clientName.value = clientData.name || 'Fiado'
    balance.value    = accountData.balance   || 0
    movements.value  = accountData.movements || []
    publicToken.value = accountData.publicToken || ''
  } finally {
    loading.value = false
  }
}

onMounted(load)

function formatPrice(cents) {
  return ((cents || 0) / 100).toFixed(2)
}

function formatDate(dt) {
  if (!dt) return ''
  const d = new Date(dt)
  return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' })
}

const activeMovements = computed(() => movements.value.filter(m => m.status !== 'cancelled'))
const activePayments  = computed(() => activeMovements.value.filter(m => m.type === 'payment'))
const totalDebt = computed(() => {
  return activeMovements.value
    .filter(m => m.type !== 'payment')
    .reduce((sum, m) => sum + (m.amount || 0), 0)
})

function payMethodLabel(method) {
  const map = { efectivo: 'Efectivo', transferencia: 'Transferencia', debito: 'Débito', credito: 'Crédito' }
  return map[method] || method || '—'
}

const whatsappUrl = computed(() => {
  const url = `${window.location.origin}/public/fiado/${publicToken.value}`
  const msg = `Hola! Te comparto el resumen de tu cuenta corriente:\n${url}`
  return `https://wa.me/?text=${encodeURIComponent(msg)}`
})

async function submitDebt() {
  if (!debtAmount.value) return
  submittingDebt.value = true
  try {
    await addDebt(clientId, {
      amount: Math.round(parseFloat(debtAmount.value) * 100),
      notes: debtNote.value || null
    })
    debtAmount.value = ''
    debtNote.value = ''
    await load()
  } finally {
    submittingDebt.value = false
  }
}

async function submitPayment() {
  if (!payAmount.value) return
  submittingPay.value = true
  try {
    await addPayment(clientId, {
      amount: Math.round(parseFloat(payAmount.value) * 100),
      paymentMethod: 'efectivo',
      notes: payNote.value || null
    })
    payAmount.value = ''
    payNote.value = ''
    await load()
  } finally {
    submittingPay.value = false
  }
}

function startCancelPayment(movement) {
  cancellingMovement.value = movement
  cancelReason.value = ''
  cancelError.value = ''
}

async function submitCancelPayment() {
  cancelError.value = ''
  if (!cancelReason.value.trim()) { cancelError.value = 'El motivo es obligatorio'; return }
  cancellingPay.value = true
  try {
    await cancelPayment(clientId, cancellingMovement.value.id, cancelReason.value)
    cancellingMovement.value = null
    await load()
  } catch (e) {
    cancelError.value = e.message
  } finally {
    cancellingPay.value = false
  }
}
</script>

<style scoped>
.balance-card { text-align: center; margin-bottom: var(--gap); }
.balance-label { margin: 0 0 4px; color: var(--color-text-light); font-size: var(--font-sm); }
.balance-amount { margin: 0 0 var(--gap); font-size: var(--font-2xl); font-weight: 800; }
.owed { color: var(--color-danger); }
.clear { color: var(--color-success); }
.share-hint { margin: 0; font-size: var(--font-sm); color: var(--color-text-light); }
.share-btn { color: var(--color-primary); font-weight: 600; }
.action-card { margin-bottom: var(--gap); display: flex; flex-direction: column; gap: var(--gap-sm); }
.action-title { margin: 0 0 var(--gap-sm); font-size: var(--font-md); font-weight: 700; }
.debt-card { margin-bottom: var(--gap); }
.debt-row { display: flex; justify-content: space-between; align-items: center; }
.debt-label { font-size: var(--font-base); font-weight: 600; color: var(--color-text); }
.debt-amount { font-size: var(--font-xl); font-weight: 800; color: var(--color-danger); }
.movements-title { font-size: var(--font-lg); margin: var(--gap) 0 var(--gap-sm); }
.movements-list { display: flex; flex-direction: column; gap: var(--gap-sm); }
.movement-row {}
.mov-top { display: flex; justify-content: space-between; align-items: flex-start; }
.mov-left { display: flex; flex-direction: column; gap: 2px; }
.mov-method { font-weight: 600; font-size: var(--font-base); }
.mov-amount { font-size: var(--font-lg); font-weight: 700; }
.payment { color: var(--color-success); }
.mov-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; }
.mov-date { font-size: var(--font-sm); color: var(--color-text-light); }
.cancel-mov-btn { font-size: var(--font-sm); color: var(--color-danger); font-weight: 600; background: none; border: none; cursor: pointer; padding: 0; }
.mov-note { font-size: var(--font-sm); color: var(--color-text-light); flex: 1; }
.cancel-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: flex-end; z-index: 50; }
.cancel-panel { width: 100%; background: var(--color-surface); border-radius: var(--radius-lg) var(--radius-lg) 0 0; padding: var(--gap); display: flex; flex-direction: column; gap: var(--gap); }
.cancel-title { font-size: var(--font-lg); font-weight: 700; margin: 0; }
.cancel-btns { display: flex; gap: var(--gap); }
.cancel-btns > * { flex: 1; }
</style>
