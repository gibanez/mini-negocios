<template>
  <AppShell :title="isEdit ? 'Editar producto' : 'Nuevo producto'" back="/catalogo">
    <form @submit.prevent="save" class="form">
      <BigInput v-model="form.name" label="Nombre" placeholder="Ej: Arroz 1kg" :error="errors.name" />

      <div class="field">
        <label class="label">Categoría</label>
        <select v-model="form.category_id" class="select">
          <option value="">Sin categoría</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>

      <BigInput v-model="priceDisplay" label="Precio ($)" type="number" placeholder="0.00" step="0.01" :error="errors.price" />
      <BigInput v-model="stockDisplay" label="Stock inicial" type="number" placeholder="0" step="0.001" :error="errors.stock" />
      <BigInput v-model="form.unit" label="Unidad (opcional)" placeholder="Ej: kg, litro, unidad" />

      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">{{ isEdit ? 'Guardar cambios' : 'Crear producto' }}</BigBtn>
      <BigBtn v-if="isEdit" variant="ghost" type="button" @click="router.push('/catalogo/producto/' + id + '/stock')">
        Ajustar stock
      </BigBtn>
      <BigBtn v-if="isEdit" variant="danger" type="button" @click="showConfirm = true">Desactivar</BigBtn>
    </form>

    <ConfirmDialog
      v-model="showConfirm"
      message="¿Desactivar este producto?"
      confirm-label="Desactivar"
      @confirm="deactivate"
    />
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getCatalog, createProduct, updateProduct, deactivateProduct } from '@/api/catalog.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const router = useRouter()
const route = useRoute()

const id = route.params.id
const isEdit = computed(() => !!id)

const form = ref({ name: '', category_id: '', unit: '' })
const priceDisplay = ref('')
const stockDisplay = ref('')
const categories = ref([])
const loading = ref(false)
const errors = ref({})
const showConfirm = ref(false)

onMounted(async () => {
  const catalog = await getCatalog()
  categories.value = catalog
    .filter(c => c.status === 'active')
    .map(c => ({ id: c.id, name: c.name }))

  if (isEdit.value) {
    for (const cat of catalog) {
      const p = (cat.products || []).find(p => p.id === id)
      if (p) {
        form.value.name = p.name
        form.value.category_id = cat.id
        form.value.unit = p.unit || ''
        if (p.price != null) priceDisplay.value = (p.price / 100).toFixed(2)
        if (p.stock != null) stockDisplay.value = (p.stock / 1000).toString()
        break
      }
    }
  }
})

async function save() {
  errors.value = {}
  if (!form.value.name.trim()) { errors.value.name = 'Requerido'; return }

  const payload = {
    name: form.value.name,
    categoryId: form.value.category_id || null,
    unit: form.value.unit || null,
    price: priceDisplay.value ? Math.round(parseFloat(priceDisplay.value) * 100) : null,
    stock: stockDisplay.value ? Math.round(parseFloat(stockDisplay.value) * 1000) : 0
  }

  loading.value = true
  try {
    if (isEdit.value) {
      await updateProduct(id, payload)
    } else {
      await createProduct(payload)
    }
    router.push('/catalogo')
  } catch (e) {
    errors.value.general = e.message
  } finally {
    loading.value = false
  }
}

async function deactivate() {
  try {
    await deactivateProduct(id)
    router.push('/catalogo')
  } catch (e) {
    errors.value.general = e.message
  }
}
</script>

<style scoped>
.form { display: flex; flex-direction: column; gap: var(--gap); }
.field { display: flex; flex-direction: column; gap: 6px; }
.label { font-size: var(--font-sm); font-weight: 600; }
.select { min-height: 54px; }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
</style>
