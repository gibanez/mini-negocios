<template>
  <AppShell :title="isEdit ? 'Editar categoría' : 'Nueva categoría'" back="/catalogo">
    <form @submit.prevent="save" class="form">
      <BigInput v-model="name" label="Nombre" placeholder="Ej: Bebidas" :error="errors.name" />
      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">{{ isEdit ? 'Guardar cambios' : 'Crear categoría' }}</BigBtn>
      <BigBtn v-if="isEdit" variant="danger" type="button" @click="showConfirm = true">Desactivar categoría</BigBtn>
    </form>

    <ConfirmDialog
      v-model="showConfirm"
      message="¿Desactivar esta categoría? Los productos asociados quedarán sin categoría."
      confirm-label="Desactivar"
      @confirm="deactivate"
    />
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getCategories, createCategory, updateCategory, deactivateCategory } from '@/api/catalog.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const router = useRouter()
const route = useRoute()

const id = route.params.id
const isEdit = computed(() => !!id)

const name = ref('')
const loading = ref(false)
const errors = ref({})
const showConfirm = ref(false)

onMounted(async () => {
  if (isEdit.value) {
    const data = await getCategories()
    const cat = (data.categories || []).find(c => c.id === id)
    if (cat) name.value = cat.name
  }
})

async function save() {
  errors.value = {}
  if (!name.value.trim()) { errors.value.name = 'Requerido'; return }
  loading.value = true
  try {
    if (isEdit.value) {
      await updateCategory(id, { name: name.value })
    } else {
      await createCategory({ name: name.value })
    }
    router.push('/catalogo')
  } catch (e) {
    errors.value.general = e.status === 409 ? 'Ya existe una categoría con ese nombre' : e.message
  } finally {
    loading.value = false
  }
}

async function deactivate() {
  try {
    await deactivateCategory(id)
    router.push('/catalogo')
  } catch (e) {
    errors.value.general = e.message
  }
}
</script>

<style scoped>
.form { display: flex; flex-direction: column; gap: var(--gap); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
</style>
