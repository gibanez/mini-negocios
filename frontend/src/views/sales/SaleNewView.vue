<template>
  <AppShell title="Nueva venta" :show-nav="false">
    <!-- Search bar -->
    <div class="search-wrap">
      <BigInput v-model="search" placeholder="Buscar producto..." type="search" @input="doSearch" />
    </div>

    <!-- Search results -->
    <div v-if="searchResults.length" class="results">
      <AppCard
        v-for="p in searchResults" :key="p.id"
        :clickable="p.stock > 0"
        class="result-row"
        :class="{ 'result-disabled': p.stock <= 0 }"
        @click="p.stock > 0 && addToCart(p)"
      >
        <span class="r-name">{{ p.name }}</span>
        <span v-if="p.stock <= 0" class="r-no-stock">Sin stock</span>
        <span v-else class="r-price">${{ formatPrice(p.price) }}</span>
      </AppCard>
    </div>

    <!-- Cart -->
    <div class="cart-section">
      <h3 class="section-title">Carrito</h3>
      <EmptyState v-if="!cart.length" icon="🛒" text="Agregá productos buscando arriba" />
      <div v-else>
        <AppCard v-for="item in cart" :key="item.product_id" class="cart-item">
          <div class="item-info">
            <span class="item-name">{{ item.name }}</span>
            <span class="item-price">${{ formatPrice(item.unit_price) }}</span>
          </div>
          <div class="item-qty">
            <button class="qty-btn" @click="changeQty(item, -1000)">−</button>
            <span class="qty-val">{{ item.quantity / 1000 }}</span>
            <button class="qty-btn" @click="changeQty(item, 1000)">+</button>
            <button class="remove-btn" @click="removeFromCart(item)">✕</button>
          </div>
          <div class="item-subtotal">${{ formatPrice(item.subtotal) }}</div>
        </AppCard>

        <div class="total-row">
          <span>Total</span>
          <span class="total-amount">${{ formatPrice(total) }}</span>
        </div>

        <!-- Payment method -->
        <div class="field">
          <label class="label">Forma de pago</label>
          <select v-model="paymentMethod" class="select">
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
            <option value="debito">Débito</option>
            <option value="credito">Crédito</option>
            <option value="fiado">Fiado</option>
          </select>
        </div>

        <!-- Client selector (only if fiado) -->
        <div v-if="paymentMethod === 'fiado'" class="field">
          <div class="label-row">
            <label class="label">Cliente</label>
            <button class="add-client-btn" @click="showNewClient = true">+ Nuevo</button>
          </div>
          <select v-model="clientId" class="select">
            <option value="">— Elegí un cliente —</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
        <BigBtn @click="confirm" :loading="loading">Confirmar venta</BigBtn>
      </div>
    </div>

    <ConfirmDialog
      v-model="showConfirm"
      :message="`Confirmar venta por $${formatPrice(total)}?`"
      confirm-label="Confirmar"
      confirm-variant="primary"
      @confirm="submit"
    />

    <!-- Modal nuevo cliente -->
    <div v-if="showNewClient" class="modal-overlay" @click.self="showNewClient = false">
      <div class="modal">
        <h3 class="modal-title">Nuevo cliente</h3>
        <div class="field">
          <label class="label">Nombre *</label>
          <input v-model="newClient.name" class="input" placeholder="Nombre del cliente" />
        </div>
        <div class="field">
          <label class="label">Teléfono</label>
          <input v-model="newClient.phone" class="input" placeholder="Opcional" />
        </div>
        <p v-if="newClientError" class="error-msg">{{ newClientError }}</p>
        <div class="modal-actions">
          <button class="btn-ghost" @click="showNewClient = false">Cancelar</button>
          <button class="btn-primary" @click="saveNewClient" :disabled="savingClient">Guardar</button>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getProducts } from '@/api/catalog.js'
import { getClients, createClient } from '@/api/clients.js'
import { createSale } from '@/api/sales.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import AppCard from '@/components/ui/AppCard.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const router = useRouter()

const search = ref('')
const searchResults = ref([])
const cart = ref([])
const clients = ref([])
const clientId = ref('')
const paymentMethod = ref('efectivo')
const showNewClient = ref(false)
const newClient = ref({ name: '', phone: '' })
const newClientError = ref('')
const savingClient = ref(false)
const loading = ref(false)
const errors = ref({})
const showConfirm = ref(false)
const allProducts = ref([])

const total = computed(() => cart.value.reduce((sum, i) => sum + i.subtotal, 0))

let debounce = null
function doSearch() {
  clearTimeout(debounce)
  debounce = setTimeout(() => {
    if (!search.value.trim()) { searchResults.value = []; return }
    const q = search.value.toLowerCase()
    const matched = allProducts.value.filter(p => p.status === 'active' && p.name.toLowerCase().includes(q))
    matched.sort((a, b) => {
      const aOk = a.stock > 0 && a.price > 0 ? 0 : 1
      const bOk = b.stock > 0 && b.price > 0 ? 0 : 1
      return aOk - bOk
    })
    searchResults.value = matched.slice(0, 8)
  }, 200)
}

onMounted(async () => {
  const pData = await getProducts()
  allProducts.value = Array.isArray(pData) ? pData : (pData.products || [])
  const cData = await getClients()
  const cList = Array.isArray(cData) ? cData : (cData.clients || [])
  clients.value = cList.filter(c => c.status === 'active')
})

function addToCart(product) {
  const existing = cart.value.find(i => i.product_id === product.id)
  if (existing) {
    existing.quantity += 1000
    existing.subtotal = Math.round(existing.quantity * existing.unit_price / 1000)
  } else {
    cart.value.push({
      product_id: product.id,
      name: product.name,
      unit_price: product.price || 0,
      quantity: 1000,
      subtotal: product.price || 0
    })
  }
  search.value = ''
  searchResults.value = []
}

function changeQty(item, delta) {
  const newQty = item.quantity + delta
  if (newQty <= 0) { removeFromCart(item); return }
  item.quantity = newQty
  item.subtotal = Math.round(item.quantity * item.unit_price / 1000)
}

function removeFromCart(item) {
  cart.value = cart.value.filter(i => i.product_id !== item.product_id)
}

function formatPrice(cents) {
  if (cents == null) return '0.00'
  return (cents / 100).toFixed(2)
}

function confirm() {
  errors.value = {}
  if (!cart.value.length) return
  showConfirm.value = true
}

async function saveNewClient() {
  newClientError.value = ''
  if (!newClient.value.name.trim()) { newClientError.value = 'El nombre es requerido'; return }
  savingClient.value = true
  try {
    const c = await createClient({ name: newClient.value.name.trim(), phone: newClient.value.phone.trim() || null })
    clients.value.push(c)
    clientId.value = c.id
    showNewClient.value = false
    newClient.value = { name: '', phone: '' }
  } catch (e) {
    newClientError.value = e.message
  } finally {
    savingClient.value = false
  }
}

async function submit() {
  loading.value = true
  try {
    await createSale({
      clientId: paymentMethod.value === 'fiado' ? (clientId.value || null) : null,
      paymentMethod: paymentMethod.value,
      items: cart.value.map(i => ({
        productId: i.product_id,
        quantity: i.quantity,
        unitPrice: i.unit_price
      }))
    })
    router.push('/ventas')
  } catch (e) {
    errors.value.general = e.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.search-wrap { margin-bottom: var(--gap); }
.results { margin-bottom: var(--gap); display: flex; flex-direction: column; gap: var(--gap-sm); }
.result-row { display: flex; justify-content: space-between; align-items: center; padding: 12px var(--gap); }
.r-name { font-size: var(--font-md); font-weight: 600; }
.r-price { color: var(--color-primary); font-weight: 700; }
.r-no-stock { color: var(--color-danger); font-size: var(--font-sm); font-weight: 600; }
.result-disabled { opacity: 0.5; cursor: not-allowed; }
.cart-section {}
.section-title { font-size: var(--font-lg); margin: 0 0 var(--gap); }
.cart-item { margin-bottom: var(--gap-sm); }
.item-info { display: flex; justify-content: space-between; margin-bottom: 8px; }
.item-name { font-weight: 600; }
.item-price { color: var(--color-text-light); font-size: var(--font-sm); }
.item-qty { display: flex; align-items: center; gap: 12px; }
.qty-btn { width: 40px; height: 40px; border-radius: 50%; background: var(--color-primary-light); color: var(--color-primary); font-size: 22px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
.qty-val { font-size: var(--font-lg); font-weight: 700; min-width: 30px; text-align: center; }
.remove-btn { color: var(--color-danger); font-size: 18px; padding: 4px 8px; }
.item-subtotal { text-align: right; font-size: var(--font-md); font-weight: 700; color: var(--color-primary); margin-top: 4px; }
.total-row { display: flex; justify-content: space-between; align-items: center; padding: var(--gap) 0; border-top: 2px solid var(--color-border); margin-bottom: var(--gap); font-size: var(--font-lg); font-weight: 600; }
.total-amount { font-size: var(--font-xl); color: var(--color-primary); font-weight: 800; }
.field { display: flex; flex-direction: column; gap: 6px; margin-bottom: var(--gap); }
.label { font-size: var(--font-sm); font-weight: 600; }
.select { min-height: 54px; }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0 0 var(--gap); }

.label-row { display: flex; justify-content: space-between; align-items: center; }
.add-client-btn {
  font-size: var(--font-sm); font-weight: 700; color: var(--color-primary);
  background: none; border: none; cursor: pointer; padding: 2px 0;
}

.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.5);
  display: flex; align-items: flex-end; justify-content: center;
  z-index: 100; padding: var(--gap);
}
.modal {
  background: var(--color-surface); border-radius: var(--radius-lg);
  padding: var(--gap); width: 100%; max-width: 500px;
  display: flex; flex-direction: column; gap: var(--gap);
}
.modal-title { font-size: var(--font-lg); font-weight: 700; margin: 0; }
.input {
  width: 100%; min-height: 54px; padding: 0 var(--gap);
  border: 1.5px solid var(--color-border); border-radius: var(--radius-md);
  background: var(--color-bg); color: var(--color-text);
  font-size: var(--font-base); box-sizing: border-box;
}
.modal-actions { display: flex; gap: var(--gap); }
.btn-ghost {
  flex: 1; min-height: 54px; border-radius: var(--radius-md);
  border: 1.5px solid var(--color-border); background: none;
  color: var(--color-text); font-size: var(--font-base); font-weight: 600; cursor: pointer;
}
.btn-primary {
  flex: 1; min-height: 54px; border-radius: var(--radius-md);
  border: none; background: var(--color-primary);
  color: #fff; font-size: var(--font-base); font-weight: 700; cursor: pointer;
}
.btn-primary:disabled { opacity: 0.6; }
</style>
