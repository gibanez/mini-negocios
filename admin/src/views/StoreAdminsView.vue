<template>
  <div>
    <div class="page-header">
      <el-button :icon="ArrowLeft" circle @click="router.back()" />
      <div>
        <h2 class="page-title">Usuarios del negocio</h2>
        <p v-if="storeName" class="page-sub">{{ storeName }}</p>
      </div>
      <el-button type="primary" :icon="Plus" @click="createDialog.visible = true">
        Agregar usuario
      </el-button>
    </div>

    <el-card shadow="never">
      <el-table :data="admins" stripe style="width: 100%;" v-loading="loading">
        <el-table-column prop="name" label="Nombre" min-width="150" />
        <el-table-column prop="email" label="Correo" min-width="210" />
        <el-table-column prop="role" label="Rol" width="130">
          <template #default="{ row }">
            <el-tag :type="roleType(row.role)" size="small">{{ roleLabel(row.role) }}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="Estado" width="110">
          <template #default="{ row }">
            <el-tag :type="row.status === 'active' ? 'success' : 'info'" size="small">
              {{ row.status === 'active' ? 'Activo' : 'Inactivo' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="Acciones" width="140" align="center">
          <template #default="{ row }">
            <el-button-group>
              <el-tooltip content="Ingresar como este usuario">
                <el-button
                  size="small"
                  type="primary"
                  :icon="View"
                  :loading="impersonating === row.id"
                  :disabled="row.status !== 'active'"
                  @click="doImpersonate(row)"
                />
              </el-tooltip>
              <el-popconfirm
                v-if="row.status === 'active'"
                title="¿Desactivar este usuario?"
                confirm-button-text="Sí, desactivar"
                cancel-button-text="Cancelar"
                confirm-button-type="danger"
                @confirm="doDeactivate(row.id)"
              >
                <template #reference>
                  <el-button size="small" type="danger" :icon="Delete" circle />
                </template>
              </el-popconfirm>
            </el-button-group>
          </template>
        </el-table-column>
      </el-table>

      <div class="table-footer">
        {{ admins.length }} usuario{{ admins.length !== 1 ? 's' : '' }}
      </div>
    </el-card>

    <el-dialog v-model="createDialog.visible" title="Nuevo usuario" width="440px" @close="resetCreate">
      <el-form :model="createDialog.form" label-position="top">
        <el-form-item label="Nombre completo">
          <el-input v-model="createDialog.form.name" placeholder="Juan García" clearable />
        </el-form-item>
        <el-form-item label="Correo electrónico">
          <el-input v-model="createDialog.form.email" type="email" placeholder="juan@correo.com" clearable />
        </el-form-item>
        <el-form-item label="Contraseña">
          <el-input
            v-model="createDialog.form.password"
            type="password"
            show-password
            placeholder="Mínimo 8 caracteres"
          />
        </el-form-item>
        <el-form-item label="Rol">
          <el-radio-group v-model="createDialog.form.role">
            <el-radio value="owner">Dueño</el-radio>
            <el-radio value="vendor">Vendedor</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-alert
          v-if="createDialog.error"
          :title="createDialog.error"
          type="error"
          show-icon
          :closable="false"
        />
      </el-form>
      <template #footer>
        <el-button @click="createDialog.visible = false">Cancelar</el-button>
        <el-button type="primary" :loading="createDialog.loading" @click="doCreate">
          Crear usuario
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { ArrowLeft, Plus, Delete, View } from '@element-plus/icons-vue'
import { platform } from '@/api/platform.js'

const router    = useRouter()
const route     = useRoute()
const admins    = ref([])
const loading   = ref(true)
const storeName = ref('')

const impersonating = ref('')

async function doImpersonate(row) {
  impersonating.value = row.id
  try {
    const data = await platform.impersonate(row.id)
    localStorage.setItem('token', data.token)
    localStorage.setItem('user',  JSON.stringify(data.user))
    window.open('/', '_blank')
  } catch (e) {
    ElMessage.error('No se pudo impersonar el usuario.')
  } finally {
    impersonating.value = ''
  }
}

const createDialog = reactive({
  visible: false,
  loading: false,
  error: '',
  form: { name: '', email: '', password: '', role: 'vendor' }
})

function roleLabel(role) {
  if (role === 'owner')          return 'Dueño'
  if (role === 'vendor')         return 'Vendedor'
  if (role === 'platform_admin') return 'Admin PLA'
  return role
}

function roleType(role) {
  if (role === 'owner')  return 'primary'
  if (role === 'vendor') return 'warning'
  return 'info'
}

function resetCreate() {
  createDialog.error = ''
  createDialog.form  = { name: '', email: '', password: '', role: 'vendor' }
}

async function doDeactivate(adminId) {
  try {
    await platform.deactivateAdmin(adminId)
    admins.value = await platform.listAdmins(route.params.id)
    ElMessage.success('Usuario desactivado.')
  } catch (e) {
    ElMessage.error('Error al desactivar.')
  }
}

async function doCreate() {
  createDialog.error = ''
  const { name, email, password, role } = createDialog.form
  if (!name.trim() || !email.trim() || password.length < 8) {
    createDialog.error = 'Completá todos los campos. La contraseña debe tener al menos 8 caracteres.'
    return
  }
  createDialog.loading = true
  try {
    await platform.createAdmin(route.params.id, {
      name: name.trim(), email: email.trim(), password, role
    })
    admins.value = await platform.listAdmins(route.params.id)
    createDialog.visible = false
    ElMessage.success('Usuario creado correctamente.')
  } catch (e) {
    createDialog.error = e.status === 409
      ? 'Ese correo ya está en uso.'
      : (e.data?.error || 'Error al crear el usuario.')
  } finally {
    createDialog.loading = false
  }
}

onMounted(async () => {
  try {
    const [store, a] = await Promise.all([
      platform.getStore(route.params.id),
      platform.listAdmins(route.params.id),
    ])
    storeName.value = store.name
    admins.value    = a
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.page-title { margin: 0; font-size: 20px; font-weight: 600; color: var(--el-text-color-primary); }
.page-sub { margin: 2px 0 0; font-size: 13px; color: var(--el-text-color-secondary); }
.table-footer { margin-top: 12px; font-size: 13px; color: var(--el-text-color-secondary); text-align: right; }
</style>
