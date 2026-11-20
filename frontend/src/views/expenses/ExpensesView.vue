<template>
  <AppShell title="Gastos">
    <div class="toolbar">
      <div class="filters">
        <div class="date-row">
          <div class="field">
            <label class="label">Desde</label>
            <input type="date" v-model="dateFrom" class="date-input" @change="load" />
          </div>
          <div class="field">
            <label class="label">Hasta</label>
            <input type="date" v-model="dateTo" class="date-input" @change="load" />
          </div>
        </div>
        <div class="field">
          <label class="label">Categoría</label>
          <select v-model="filterCategory" class="select" @change="load">
            <option value="">Todas</option>
            <option v-for="c in CATEGORIES" :key="c.value" :value="c.value">{{ c.label }}</option>
          </select>
        </div>
        <button class="clear-btn" @click="clearFilters">Limpiar filtros</button>
      </div>
      <button class="add-btn" @click="openForm(null)">+ Nuevo gasto</button>
    </div>

    <LoadingSpinner v-if="loading" />
    <EmptyState v-else-if="!expenses.length" icon="💸" text="No hay gastos en ese período" />

    <div v-else>
      <div class="summary-row">
        <span>{{ expenses.length }} gastos</span>
        <span class="summary-total">-${{ formatPrice(totalAmount) }}</span>
      </div>

      <div class="list">
        <AppCard v-for="e in expenses" :key="e.id" clickable class="row" @click="openForm(e)">
          <div class="row-top">
            <span class="row-date">{{ e.date }}</span>
            <span class="row-category">{{ categoryLabel(e.category) }}</span>
          </div>
          <div class="row-bottom">
            <span class="row-recipient">{{ e.recipient }}</span>
            <span class="row-amount">-${{ formatPrice(e.amount) }}</span>
          </div>
          <p v-if="e.description" class="row-desc">{{ e.description }}</p>
        </AppCard>
      </div>
    </div>

    <!-- Form modal -->
    <div v-if="showForm" class="modal-backdrop" @click.self="showForm = false">
      <div class="modal">
        <h2 class="modal-title">{{ editing ? 'Editar gasto' : 'Nuevo gasto' }}</h2>

        <div class="form-field">
          <label class="label">Monto ($)</label>
          <input type="number" v-model.number="form.amountDisplay" min="0" step="0.01" class="input" placeholder="0.00" />
        </div>
        <div class="form-field">
          <label class="label">Categoría</label>
          <select v-model="form.category" class="select">
            <option value="">Seleccionar</option>
            <option v-for="c in CATEGORIES" :key="c.value" :value="c.value">{{ c.label }}</option>
          </select>
        </div>
        <div class="form-field">
          <label class="label">A quién se pagó</label>
          <input type="text" v-model="form.recipient" class="input" placeholder="Proveedor, servicio..." />
        </div>
        <div class="form-field">
          <label class="label">Descripción (opcional)</label>
          <textarea v-model="form.description" class="input textarea" rows="2" placeholder="Detalle del gasto..." />
        </div>
        <div class="form-field">
          <label class="label">Forma de pago</label>
          <select v-model="form.paymentMethod" class="select">
            <option value="">Seleccionar</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
          </select>
        </div>
        <div class="form-field">
          <label class="label">Fecha</label>
          <input type="date" v-model="form.date" class="date-input" />
        </div>

        <p v-if="formError" class="error">{{ formError }}</p>

        <div class="modal-actions">
          <button v-if="editing" class="del-btn" @click="confirmDelete">Eliminar</button>
          <button class="cancel-btn" @click="showForm = false">Cancelar</button>
          <button class="save-btn" :disabled="saving" @click="save">
            {{ saving ? 'Guardando…' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getExpenses, createExpense, updateExpense, deleteExpense } from '@/api/expenses.js'
import AppShell from '@/components/layout/AppShell.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const CATEGORIES = [
  { value: 'proveedor',  label: 'Proveedor' },
  { value: 'servicio',   label: 'Servicio' },
  { value: 'personal',   label: 'Personal' },
  { value: 'alquiler',   label: 'Alquiler' },
  { value: 'otro',       label: 'Otro' },
]

const expenses      = ref([])
const loading       = ref(false)
const showForm      = ref(false)
const saving        = ref(false)
const formError     = ref('')
const editing       = ref(null)

const today    = new Date().toISOString().slice(0, 10)
const dateFrom = ref(today)
const dateTo   = ref(today)
const filterCategory = ref('')

const form = ref(emptyForm())

function emptyForm() {
  return { amountDisplay: '', category: '', recipient: '', description: '', paymentMethod: '', date: today }
}

const totalAmount = computed(() => expenses.value.reduce((s, e) => s + e.amount, 0))

async function load() {
  loading.value = true
  try {
    const data = await getExpenses({
      dateFrom: dateFrom.value || undefined,
      dateTo:   dateTo.value   || undefined,
      category: filterCategory.value || undefined,
    })
    expenses.value = Array.isArray(data) ? data : (data.expenses || [])
  } finally {
    loading.value = false
  }
}

function clearFilters() {
  dateFrom.value = ''
  dateTo.value = ''
  filterCategory.value = ''
  load()
}

function openForm(expense) {
  formError.value = ''
  if (expense) {
    editing.value = expense
    form.value = {
      amountDisplay: expense.amount / 100,
      category:      expense.category,
      recipient:     expense.recipient,
      description:   expense.description || '',
      paymentMethod: expense.paymentMethod,
      date:          expense.date,
    }
  } else {
    editing.value = null
    form.value = emptyForm()
  }
  showForm.value = true
}

async function save() {
  formError.value = ''
  const amount = Math.round((parseFloat(form.value.amountDisplay) || 0) * 100)
  if (amount <= 0)              { formError.value = 'El monto debe ser mayor a cero.'; return }
  if (!form.value.category)     { formError.value = 'Seleccioná una categoría.'; return }
  if (!form.value.recipient.trim()) { formError.value = 'Indicá a quién se pagó.'; return }
  if (!form.value.paymentMethod)    { formError.value = 'Seleccioná una forma de pago.'; return }
  if (!form.value.date)             { formError.value = 'Indicá la fecha.'; return }

  saving.value = true
  try {
    const payload = {
      amount:        amount,
      category:      form.value.category,
      recipient:     form.value.recipient.trim(),
      description:   form.value.description.trim() || null,
      paymentMethod: form.value.paymentMethod,
      date:          form.value.date,
    }
    if (editing.value) {
      await updateExpense(editing.value.id, payload)
    } else {
      await createExpense(payload)
    }
    showForm.value = false
    await load()
  } catch (e) {
    formError.value = 'Error al guardar. Revisá los datos.'
  } finally {
    saving.value = false
  }
}

async function confirmDelete() {
  if (!confirm('¿Eliminar este gasto?')) return
  saving.value = true
  try {
    await deleteExpense(editing.value.id)
    showForm.value = false
    await load()
  } finally {
    saving.value = false
  }
}

function formatPrice(cents) {
  return ((cents || 0) / 100).toFixed(2)
}

function categoryLabel(v) {
  return CATEGORIES.find(c => c.value === v)?.label || v
}

onMounted(load)
</script>

<style scoped>
.toolbar { display: flex; flex-direction: column; gap: var(--gap-sm); margin-bottom: var(--gap); }
.filters { display: flex; flex-direction: column; gap: var(--gap-sm); }
.date-row { display: flex; gap: var(--gap-sm); }
.date-row .field { flex: 1; }
.field { display: flex; flex-direction: column; gap: 4px; }
.label { font-size: var(--font-sm); font-weight: 600; color: var(--color-text-light); }
.date-input { width: 100%; min-height: 44px; padding: 0 8px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); background: var(--color-input-bg); color: var(--color-text); font-size: var(--font-sm); box-sizing: border-box; }
.select { min-height: 44px; font-size: var(--font-sm); }
.clear-btn { align-self: flex-start; font-size: var(--font-sm); color: var(--color-primary); font-weight: 600; background: none; border: none; cursor: pointer; padding: 2px 0; }
.add-btn { align-self: flex-end; background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius-md); padding: 10px 18px; font-size: var(--font-sm); font-weight: 700; cursor: pointer; }
.summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; font-size: var(--font-md); font-weight: 600; border-bottom: 2px solid var(--color-border); margin-bottom: var(--gap); }
.summary-total { font-size: var(--font-xl); color: var(--color-danger); font-weight: 800; }
.list { display: flex; flex-direction: column; gap: var(--gap-sm); }
.row-top, .row-bottom { display: flex; justify-content: space-between; align-items: center; }
.row-top { margin-bottom: 4px; }
.row-date { font-size: var(--font-sm); color: var(--color-text-light); }
.row-category { font-size: var(--font-sm); font-weight: 600; color: var(--color-primary); }
.row-recipient { font-size: var(--font-md); font-weight: 600; }
.row-amount { font-size: var(--font-lg); font-weight: 700; color: var(--color-danger); }
.row-desc { font-size: var(--font-sm); color: var(--color-text-light); margin: 4px 0 0; }

/* Modal */
.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: flex; align-items: flex-end; justify-content: center; z-index: 100; }
.modal { background: var(--color-surface); border-radius: var(--radius-lg) var(--radius-lg) 0 0; padding: var(--gap); width: 100%; max-width: 480px; display: flex; flex-direction: column; gap: var(--gap-sm); max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: var(--font-lg); font-weight: 700; margin: 0 0 4px; }
.form-field { display: flex; flex-direction: column; gap: 4px; }
.input { min-height: 44px; padding: 8px 10px; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); background: var(--color-input-bg); color: var(--color-text); font-size: var(--font-sm); box-sizing: border-box; width: 100%; }
.textarea { min-height: 72px; resize: vertical; }
.error { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
.modal-actions { display: flex; gap: var(--gap-sm); justify-content: flex-end; padding-top: 4px; }
.cancel-btn { background: none; border: 1.5px solid var(--color-border); border-radius: var(--radius-md); padding: 10px 16px; font-size: var(--font-sm); cursor: pointer; color: var(--color-text); }
.save-btn { background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius-md); padding: 10px 20px; font-size: var(--font-sm); font-weight: 700; cursor: pointer; }
.save-btn:disabled { opacity: .6; }
.del-btn { background: none; border: 1.5px solid var(--color-danger); color: var(--color-danger); border-radius: var(--radius-md); padding: 10px 14px; font-size: var(--font-sm); font-weight: 600; cursor: pointer; margin-right: auto; }
</style>
