<template>
  <div>
    <h2 class="page-title">Negocios</h2>

    <el-card shadow="never">
      <div class="toolbar">
        <el-input
          v-model="search"
          placeholder="Buscar por nombre o rubro..."
          clearable
          style="width: 300px;"
          :prefix-icon="Search"
        />
        <el-select v-model="statusFilter" placeholder="Estado" clearable style="width: 150px;">
          <el-option label="Todos"       value="" />
          <el-option label="Activos"     value="active" />
          <el-option label="Suspendidos" value="suspended" />
        </el-select>
      </div>

      <el-table :data="filtered" stripe style="width: 100%;" v-loading="loading">
        <el-table-column prop="name" label="Nombre" min-width="160" sortable />
        <el-table-column prop="industry" label="Rubro" min-width="130">
          <template #default="{ row }">{{ row.industry || '—' }}</template>
        </el-table-column>
        <el-table-column prop="status" label="Estado" width="120">
          <template #default="{ row }">
            <el-tag :type="row.status === 'active' ? 'success' : 'danger'" size="small">
              {{ row.status === 'active' ? 'Activo' : 'Suspendido' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="onboardingCompleted" label="Onboarding" width="120" align="center">
          <template #default="{ row }">
            <el-icon v-if="row.onboardingCompleted" color="var(--el-color-success)"><CircleCheck /></el-icon>
            <el-icon v-else color="var(--el-color-info)"><Clock /></el-icon>
          </template>
        </el-table-column>
        <el-table-column prop="createdAt" label="Registro" width="115">
          <template #default="{ row }">{{ row.createdAt?.slice(0, 10) }}</template>
        </el-table-column>
        <el-table-column label="Acciones" width="160" align="center">
          <template #default="{ row }">
            <el-button-group>
              <el-tooltip content="Ver detalle">
                <el-button size="small" :icon="View" @click="router.push(`/stores/${row.id}`)" />
              </el-tooltip>
              <el-tooltip content="Usuarios">
                <el-button size="small" :icon="User" @click="router.push(`/stores/${row.id}/users`)" />
              </el-tooltip>
              <el-tooltip :content="row.status === 'active' ? 'Suspender' : 'Activar'">
                <el-button
                  size="small"
                  :type="row.status === 'active' ? 'danger' : 'success'"
                  :icon="row.status === 'active' ? Lock : Unlock"
                  @click="toggleStatus(row)"
                />
              </el-tooltip>
            </el-button-group>
          </template>
        </el-table-column>
      </el-table>

      <div class="table-footer">
        {{ filtered.length }} negocio{{ filtered.length !== 1 ? 's' : '' }}
      </div>
    </el-card>

    <el-dialog v-model="suspendDialog.visible" title="Suspender negocio" width="420px">
      <p style="margin: 0 0 12px; color: var(--el-text-color-secondary);">
        Negocio: <strong>{{ suspendDialog.storeName }}</strong>
      </p>
      <el-input
        v-model="suspendDialog.reason"
        type="textarea"
        :rows="3"
        placeholder="Describí brevemente el motivo de la suspensión..."
      />
      <template #footer>
        <el-button @click="suspendDialog.visible = false">Cancelar</el-button>
        <el-button type="danger" :loading="suspendDialog.loading" @click="confirmSuspend">
          Suspender
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { Search, View, User, Lock, Unlock } from '@element-plus/icons-vue'
import { platform } from '@/api/platform.js'

const router       = useRouter()
const stores       = ref([])
const loading      = ref(true)
const search       = ref('')
const statusFilter = ref('')

const suspendDialog = ref({
  visible: false, storeId: '', storeName: '', reason: '', loading: false
})

const filtered = computed(() => {
  return stores.value.filter(s => {
    const q = search.value.toLowerCase()
    const matchSearch = !q || s.name.toLowerCase().includes(q) || (s.industry || '').toLowerCase().includes(q)
    const matchStatus = !statusFilter.value || s.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

function toggleStatus(row) {
  if (row.status === 'active') {
    suspendDialog.value = { visible: true, storeId: row.id, storeName: row.name, reason: '', loading: false }
  } else {
    doActivate(row.id)
  }
}

async function doActivate(id) {
  try {
    await platform.activateStore(id)
    const idx = stores.value.findIndex(s => s.id === id)
    if (idx !== -1) stores.value[idx].status = 'active'
    ElMessage.success('Negocio activado.')
  } catch (e) {
    ElMessage.error('Error al activar.')
  }
}

async function confirmSuspend() {
  if (!suspendDialog.value.reason.trim()) { ElMessage.warning('Ingresá un motivo.'); return }
  suspendDialog.value.loading = true
  try {
    await platform.suspendStore(suspendDialog.value.storeId, suspendDialog.value.reason.trim())
    const idx = stores.value.findIndex(s => s.id === suspendDialog.value.storeId)
    if (idx !== -1) stores.value[idx].status = 'suspended'
    suspendDialog.value.visible = false
    ElMessage.success('Negocio suspendido.')
  } catch (e) {
    ElMessage.error(e.data?.error || 'Error al suspender.')
  } finally {
    suspendDialog.value.loading = false
  }
}

onMounted(async () => {
  try { stores.value = await platform.listStores() }
  finally { loading.value = false }
})
</script>

<style scoped>
.page-title { margin: 0 0 20px; font-size: 20px; font-weight: 600; color: var(--el-text-color-primary); }
.toolbar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.table-footer { margin-top: 12px; font-size: 13px; color: var(--el-text-color-secondary); text-align: right; }
</style>
