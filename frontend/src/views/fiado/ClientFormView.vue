<template>
  <AppShell :title="isEdit ? 'Editar cliente' : 'Nuevo cliente'" back="/clientes">
    <form @submit.prevent="save" class="form">
      <BigInput v-model="form.name" label="Nombre completo" placeholder="María García" :error="errors.name" />
      <BigInput v-model="form.nickname" label="Apodo (opcional)" placeholder="Ej: La Mari" />
      <BigInput v-model="form.phone" label="Teléfono (opcional)" type="tel" placeholder="+54 9 11 1234-5678" />
      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">{{ isEdit ? 'Guardar' : 'Agregar cliente' }}</BigBtn>
      <BigBtn v-if="isEdit" variant="danger" type="button" @click="showConfirm = true">Desactivar cliente</BigBtn>
    </form>

    <ConfirmDialog
      v-model="showConfirm"
      message="¿Desactivar este cliente?"
      confirm-label="Desactivar"
      @confirm="deactivate"
    />
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getClient, createClient, updateClient, deactivateClient } from '@/api/clients.js'
import AppShell from '@/components/layout/AppShell.vue'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue'

const router = useRouter()
const route = useRoute()

const id = route.params.id
const isEdit = computed(() => !!id)
const form = ref({ name: '', nickname: '', phone: '' })
const loading = ref(false)
const errors = ref({})
const showConfirm = ref(false)

onMounted(async () => {
  if (isEdit.value) {
    try {
      const c = await getClient(id)
      form.value.name     = c.name     || ''
      form.value.nickname = c.nickname || ''
      form.value.phone    = c.phone    || ''
    } catch (e) {
      errors.value.general = 'No se pudo cargar el cliente'
    }
  }
})

async function save() {
  errors.value = {}
  if (!form.value.name.trim()) { errors.value.name = 'Requerido'; return }
  loading.value = true
  try {
    const payload = {
      name: form.value.name,
      nickname: form.value.nickname || null,
      phone: form.value.phone || null
    }
    if (isEdit.value) {
      await updateClient(id, payload)
    } else {
      await createClient(payload)
    }
    router.push('/clientes')
  } catch (e) {
    errors.value.general = e.message
  } finally {
    loading.value = false
  }
}

async function deactivate() {
  try {
    await deactivateClient(id)
    router.push('/clientes')
  } catch (e) {
    errors.value.general = e.message
  }
}
</script>

<style scoped>
.form { display: flex; flex-direction: column; gap: var(--gap); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
</style>
