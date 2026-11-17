<template>
  <AppShell title="Ajustar stock" :back="'/catalogo/producto/' + id">
    <form @submit.prevent="save" class="form">
      <p class="current-stock">
        Stock actual: <strong>{{ formatStock(currentStock) }}</strong>
      </p>

      <div class="field">
        <label class="label">Motivo</label>
        <select v-model="reason" class="select">
          <option value="reposicion">Reposición</option>
          <option value="rotura">Rotura / merma</option>
          <option value="recuento">Recuento físico</option>
          <option value="error">Corrección de error</option>
        </select>
      </div>

      <BigInput v-model="delta" label="Cantidad a ajustar (negativo para restar)" type="number" step="0.001" placeholder="Ej: 10 o -3" :error="errors.delta" />
      <BigInput v-model="note" label="Nota (opcional)" placeholder="Ej: Recibí mercadería" />
      <BigInput v-model="responsibleName" label="Responsable (opcional)" placeholder="Tu nombre" />

      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">Guardar ajuste</BigBtn>
    </form>

    <div v-if="history.length" class="history">
      <h3 class="history-title">Historial</h3>
      <AppCard v-for="h in history" :key="h.id" class="history-row">
        <div class="h-top">
          <span class="h-reason">{{ reasonLabel(h.reason) }}</span>
          <span class="h-delta" :class="h.delta >= 0 ? 'positive' : 'negative'">
            {{ h.delta >= 0 ? '+' : '' }}{{ formatStock(h.delta) }}
          </span>
        </div>
        <div class="h-bottom">
          <span class="h-date">{{ h.created_at }}</span>
          <span v-if="h.responsible_name" class="h-who">{{ h.responsible_name }}</span>
        </div>
        <p v-if="h.note" class="h-note">{{ h.note }}</p>
      </AppCard>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getProducts, adjustStock, getStockHistory } from '@/api/catalog.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'

const router = useRouter()
const route = useRoute()
const id = route.params.id

const currentStock = ref(0)
const reason = ref('reposicion')
const delta = ref('')
const note = ref('')
const responsibleName = ref('')
const history = ref([])
const loading = ref(false)
const errors = ref({})

onMounted(async () => {
  const data = await getProducts()
  const p = (data.products || []).find(p => p.id === id)
  if (p) currentStock.value = p.stock || 0

  const hData = await getStockHistory(id)
  history.value = hData.history || []
})

function formatStock(millis) {
  if (millis == null) return '0'
  const val = millis / 1000
  return Number.isInteger(val) ? val.toString() : val.toFixed(3)
}

function reasonLabel(r) {
  const map = { reposicion: 'Reposición', rotura: 'Rotura', recuento: 'Recuento', error: 'Error' }
  return map[r] || r
}

async function save() {
  errors.value = {}
  if (!delta.value) { errors.value.delta = 'Ingresá la cantidad'; return }
  const deltaMillis = Math.round(parseFloat(delta.value) * 1000)
  if (isNaN(deltaMillis)) { errors.value.delta = 'Número inválido'; return }

  loading.value = true
  try {
    await adjustStock(id, {
      delta: deltaMillis,
      reason: reason.value,
      note: note.value || null,
      responsible_name: responsibleName.value || null
    })
    router.push('/catalogo/producto/' + id)
  } catch (e) {
    errors.value.general = e.status === 422 ? 'El stock no puede quedar negativo' : e.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form { display: flex; flex-direction: column; gap: var(--gap); }
.current-stock { font-size: var(--font-md); margin: 0; }
.field { display: flex; flex-direction: column; gap: 6px; }
.label { font-size: var(--font-sm); font-weight: 600; }
.select { min-height: 54px; }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
.history { margin-top: calc(var(--gap) * 2); }
.history-title { font-size: var(--font-lg); margin: 0 0 var(--gap); }
.history-row { margin-bottom: var(--gap-sm); }
.h-top { display: flex; justify-content: space-between; align-items: center; }
.h-reason { font-weight: 600; font-size: var(--font-md); }
.h-delta { font-size: var(--font-lg); font-weight: 700; }
.positive { color: var(--color-success); }
.negative { color: var(--color-danger); }
.h-bottom { display: flex; gap: var(--gap-sm); font-size: var(--font-sm); color: var(--color-text-light); margin-top: 4px; }
.h-note { font-size: var(--font-sm); color: var(--color-text-light); margin: 4px 0 0; }
</style>
