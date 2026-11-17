<template>
  <div>
    <div class="page-header">
      <el-button :icon="ArrowLeft" circle @click="router.push('/stores')" />
      <h2 v-if="store" class="page-title">{{ store.name }}</h2>
      <el-skeleton v-else style="width:200px;" :rows="1" animated />
      <el-tag v-if="store" :type="store.status === 'active' ? 'success' : 'danger'" size="large">
        {{ store.status === 'active' ? 'Activo' : 'Suspendido' }}
      </el-tag>
    </div>

    <div v-if="store">
      <el-row :gutter="20">
        <el-col :md="15" :xs="24">
          <el-card shadow="never" style="margin-bottom: 16px;">
            <template #header>
              <span style="font-weight: 600;">Datos del negocio</span>
            </template>
            <el-form :model="form" label-position="top" @submit.prevent="save">
              <el-form-item label="Nombre del negocio">
                <el-input v-model="form.name" clearable />
              </el-form-item>
              <el-form-item label="Rubro">
                <el-input :model-value="store.industry || 'Sin definir'" disabled />
              </el-form-item>
              <el-form-item label="Tema de color">
                <el-input :model-value="store.theme" disabled />
              </el-form-item>
              <el-form-item label="Onboarding">
                <el-tag :type="store.onboardingCompleted ? 'success' : 'warning'">
                  {{ store.onboardingCompleted ? 'Completado' : 'Pendiente' }}
                </el-tag>
              </el-form-item>
              <el-form-item label="Fecha de registro">
                <span style="color: var(--el-text-color-regular);">{{ store.createdAt?.slice(0, 10) }}</span>
              </el-form-item>
              <el-button type="primary" native-type="submit" :loading="saving">
                Guardar cambios
              </el-button>
            </el-form>
          </el-card>
        </el-col>

        <el-col :md="9" :xs="24">
          <el-card shadow="never" style="margin-bottom: 16px;">
            <template #header><span style="font-weight: 600;">Acciones</span></template>
            <div style="display: flex; flex-direction: column; gap: 10px;">
              <el-button
                :icon="User"
                @click="router.push(`/stores/${store.id}/users`)"
                style="justify-content: flex-start;"
              >
                Gestionar usuarios
              </el-button>
              <el-divider style="margin: 4px 0;" />
              <el-button
                v-if="store.status === 'active'"
                type="danger"
                :icon="Lock"
                @click="showSuspend = true"
                style="justify-content: flex-start;"
              >
                Suspender negocio
              </el-button>
              <el-button
                v-else
                type="success"
                :icon="Unlock"
                @click="doActivate"
                style="justify-content: flex-start;"
              >
                Activar negocio
              </el-button>
            </div>
          </el-card>

          <el-card shadow="never">
            <template #header><span style="font-weight: 600;">Info técnica</span></template>
            <el-descriptions :column="1" size="small" border>
              <el-descriptions-item label="ID">
                <span style="font-family: monospace; font-size: 11px; word-break: break-all;">{{ store.id }}</span>
              </el-descriptions-item>
            </el-descriptions>
          </el-card>
        </el-col>
      </el-row>
    </div>

    <el-skeleton v-else :rows="8" animated />

    <el-dialog v-model="showSuspend" title="Suspender negocio" width="420px">
      <p style="margin: 0 0 12px; color: var(--el-text-color-secondary);">
        Esta acción impedirá que los usuarios del negocio puedan iniciar sesión.
      </p>
      <el-input
        v-model="suspendReason"
        type="textarea"
        :rows="3"
        placeholder="Motivo de la suspensión..."
      />
      <template #footer>
        <el-button @click="showSuspend = false">Cancelar</el-button>
        <el-button type="danger" :loading="suspending" @click="doSuspend">Confirmar suspensión</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { ArrowLeft, User, Lock, Unlock } from '@element-plus/icons-vue'
import { platform } from '@/api/platform.js'

const router = useRouter()
const route  = useRoute()
const store  = ref(null)
const form   = reactive({ name: '' })
const saving = ref(false)

const showSuspend   = ref(false)
const suspendReason = ref('')
const suspending    = ref(false)

async function save() {
  if (!form.name.trim()) return
  saving.value = true
  try {
    store.value = await platform.updateStore(route.params.id, { name: form.name.trim() })
    form.name   = store.value.name
    ElMessage.success('Nombre actualizado correctamente.')
  } catch (e) {
    ElMessage.error(e.data?.error || 'Error al guardar.')
  } finally {
    saving.value = false
  }
}

async function doSuspend() {
  if (!suspendReason.value.trim()) { ElMessage.warning('Ingresá un motivo.'); return }
  suspending.value = true
  try {
    await platform.suspendStore(route.params.id, suspendReason.value.trim())
    store.value         = await platform.getStore(route.params.id)
    showSuspend.value   = false
    suspendReason.value = ''
    ElMessage.success('Negocio suspendido.')
  } catch (e) {
    ElMessage.error(e.data?.error || 'Error al suspender.')
  } finally {
    suspending.value = false
  }
}

async function doActivate() {
  try {
    await platform.activateStore(route.params.id)
    store.value = await platform.getStore(route.params.id)
    ElMessage.success('Negocio activado.')
  } catch (e) {
    ElMessage.error('Error al activar.')
  }
}

onMounted(async () => {
  store.value = await platform.getStore(route.params.id)
  form.name   = store.value.name
})
</script>

<style scoped>
.page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.page-title { margin: 0; font-size: 20px; font-weight: 600; color: var(--el-text-color-primary); }
</style>
