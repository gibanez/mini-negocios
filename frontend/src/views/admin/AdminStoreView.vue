<template>
  <div class="store-view">
    <div class="topbar">
      <button class="back-btn" @click="router.back()">← Volver</button>
      <span :class="['store-badge', store?.status]" v-if="store">{{ store.status === 'active' ? 'Activo' : 'Suspendido' }}</span>
    </div>

    <div v-if="store" class="content">
      <section class="card">
        <h2 class="section-title">Negocio</h2>
        <div v-if="!editingName" class="name-row">
          <span class="big-name">{{ store.name }}</span>
          <button class="btn-link" @click="startEditName">Editar</button>
        </div>
        <div v-else class="name-edit-row">
          <input class="edit-input" v-model="newName" @keydown.enter="saveName" />
          <button class="btn-sm" @click="saveName" :disabled="savingName">Guardar</button>
          <button class="btn-sm ghost" @click="editingName = false">Cancelar</button>
        </div>
        <div class="meta-list">
          <div class="meta-row"><span class="meta-key">Rubro:</span><span>{{ store.industry || '—' }}</span></div>
          <div class="meta-row"><span class="meta-key">Creado:</span><span>{{ store.createdAt?.slice(0,10) }}</span></div>
          <div class="meta-row"><span class="meta-key">Onboarding:</span><span>{{ store.onboardingCompleted ? 'Completado' : 'Pendiente' }}</span></div>
        </div>
      </section>

      <section class="card actions-card">
        <button v-if="store.status === 'active'" class="btn-danger" @click="showSuspend = true">Suspender negocio</button>
        <button v-else class="btn-success" @click="doActivate">Activar negocio</button>
      </section>

      <div v-if="showSuspend" class="suspend-box card">
        <p class="suspend-label">Motivo de suspensión:</p>
        <input class="edit-input" v-model="suspendReason" placeholder="Ej: Incumplimiento de términos" />
        <div class="suspend-actions">
          <button class="btn-danger" @click="doSuspend" :disabled="suspending">Confirmar</button>
          <button class="btn-sm ghost" @click="showSuspend = false">Cancelar</button>
        </div>
        <p v-if="actionError" class="error-msg">{{ actionError }}</p>
      </div>

      <section class="card">
        <div class="section-header">
          <h2 class="section-title">Usuarios</h2>
          <button class="btn-sm" @click="showCreateAdmin = true">+ Agregar</button>
        </div>
        <div class="admins-list">
          <div v-for="admin in admins" :key="admin.id" class="admin-row">
            <div class="admin-info">
              <span class="admin-name">{{ admin.name }}</span>
              <span class="admin-email">{{ admin.email }}</span>
            </div>
            <div class="admin-right">
              <span :class="['role-badge', admin.role]">{{ roleLabel(admin.role) }}</span>
              <button
                v-if="admin.status === 'active'"
                class="btn-icon-danger"
                @click="doDeactivateAdmin(admin.id)"
                title="Desactivar"
              >✕</button>
              <span v-else class="inactive-label">Inactivo</span>
            </div>
          </div>
          <p v-if="admins.length === 0" class="empty">Sin usuarios.</p>
        </div>
      </section>

      <div v-if="showCreateAdmin" class="card create-admin-box">
        <h3 class="section-title">Nuevo usuario</h3>
        <input class="edit-input" v-model="newAdmin.name"     placeholder="Nombre" />
        <input class="edit-input" v-model="newAdmin.email"    placeholder="Correo" type="email" />
        <input class="edit-input" v-model="newAdmin.password" placeholder="Contraseña (mín. 8)" type="password" />
        <select class="edit-input" v-model="newAdmin.role">
          <option value="owner">Dueño</option>
          <option value="vendor">Vendedor</option>
        </select>
        <p v-if="createError" class="error-msg">{{ createError }}</p>
        <div class="suspend-actions">
          <button class="btn-sm" @click="doCreateAdmin" :disabled="creating">Crear</button>
          <button class="btn-sm ghost" @click="showCreateAdmin = false; createError = ''">Cancelar</button>
        </div>
      </div>
    </div>

    <div v-else class="loading">Cargando...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { platform } from '@/api/platform.js'

const router = useRouter()
const route  = useRoute()

const store  = ref(null)
const admins = ref([])

const editingName = ref(false)
const newName     = ref('')
const savingName  = ref(false)

const showSuspend   = ref(false)
const suspendReason = ref('')
const suspending    = ref(false)
const actionError   = ref('')

const showCreateAdmin = ref(false)
const creating        = ref(false)
const createError     = ref('')
const newAdmin        = ref({ name: '', email: '', password: '', role: 'vendor' })

function roleLabel(role) {
  if (role === 'owner')          return 'Dueño'
  if (role === 'vendor')         return 'Vendedor'
  if (role === 'platform_admin') return 'Admin PLA'
  return role
}

function startEditName() {
  newName.value     = store.value.name
  editingName.value = true
}

async function saveName() {
  if (!newName.value.trim()) return
  savingName.value = true
  try {
    store.value = await platform.updateStore(route.params.id, { name: newName.value.trim() })
    editingName.value = false
  } finally {
    savingName.value = false
  }
}

async function doSuspend() {
  if (!suspendReason.value.trim()) { actionError.value = 'Ingresá un motivo.'; return }
  suspending.value = true
  actionError.value = ''
  try {
    await platform.suspendStore(route.params.id, suspendReason.value.trim())
    store.value = await platform.getStore(route.params.id)
    showSuspend.value = false
    suspendReason.value = ''
  } catch (e) {
    actionError.value = e.data?.error || 'Error'
  } finally {
    suspending.value = false
  }
}

async function doActivate() {
  await platform.activateStore(route.params.id)
  store.value = await platform.getStore(route.params.id)
}

async function doDeactivateAdmin(adminId) {
  if (!confirm('¿Desactivar este usuario?')) return
  await platform.deactivateAdmin(adminId)
  admins.value = await platform.listAdmins(route.params.id)
}

async function doCreateAdmin() {
  createError.value = ''
  const { name, email, password, role } = newAdmin.value
  if (!name || !email || password.length < 8) {
    createError.value = 'Completá todos los campos (contraseña mín. 8 caracteres).'
    return
  }
  creating.value = true
  try {
    await platform.createAdmin(route.params.id, { name, email, password, role })
    admins.value = await platform.listAdmins(route.params.id)
    showCreateAdmin.value = false
    newAdmin.value = { name: '', email: '', password: '', role: 'vendor' }
  } catch (e) {
    createError.value = e.status === 409 ? 'Ese correo ya está en uso.' : (e.data?.error || 'Error')
  } finally {
    creating.value = false
  }
}

onMounted(async () => {
  const [s, a] = await Promise.all([
    platform.getStore(route.params.id),
    platform.listAdmins(route.params.id),
  ])
  store.value  = s
  admins.value = a
})
</script>

<style scoped>
.store-view { padding: 16px; max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.topbar { display: flex; align-items: center; justify-content: space-between; }
.back-btn { background: none; border: none; font-size: var(--font-base); color: var(--color-primary); cursor: pointer; padding: 4px 0; }
.store-badge { padding: 2px 10px; border-radius: 999px; font-size: var(--font-sm); font-weight: 600; }
.store-badge.active { background: #dcfce7; color: #16a34a; }
.store-badge.suspended { background: #fee2e2; color: #dc2626; }
.card { background: var(--color-card); border-radius: var(--radius); padding: 16px; display: flex; flex-direction: column; gap: 10px; }
.section-title { font-size: var(--font-base); font-weight: 700; margin: 0; color: var(--color-text); }
.section-header { display: flex; align-items: center; justify-content: space-between; }
.name-row { display: flex; align-items: center; gap: 12px; }
.big-name { font-size: var(--font-lg); font-weight: 700; color: var(--color-text); }
.btn-link { background: none; border: none; color: var(--color-primary); cursor: pointer; font-size: var(--font-sm); }
.name-edit-row { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.edit-input { width: 100%; padding: 10px 12px; border: 2px solid var(--color-border); border-radius: var(--radius); font-size: var(--font-base); background: var(--color-input-bg); color: var(--color-text); box-sizing: border-box; }
.meta-list { display: flex; flex-direction: column; gap: 6px; }
.meta-row { display: flex; gap: 8px; font-size: var(--font-sm); }
.meta-key { color: var(--color-text-light); min-width: 90px; }
.actions-card { flex-direction: row; flex-wrap: wrap; gap: 8px; }
.btn-danger { background: var(--color-danger); color: #fff; border: none; border-radius: var(--radius); padding: 10px 20px; font-size: var(--font-base); cursor: pointer; }
.btn-success { background: var(--color-success); color: #fff; border: none; border-radius: var(--radius); padding: 10px 20px; font-size: var(--font-base); cursor: pointer; }
.btn-sm { background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius); padding: 8px 16px; font-size: var(--font-sm); cursor: pointer; }
.btn-sm.ghost { background: var(--color-border); color: var(--color-text); }
.suspend-box { border: 2px solid var(--color-danger); }
.suspend-label { margin: 0; font-size: var(--font-sm); font-weight: 600; }
.suspend-actions { display: flex; gap: 8px; }
.admins-list { display: flex; flex-direction: column; gap: 8px; }
.admin-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--color-border); }
.admin-row:last-child { border-bottom: none; }
.admin-info { display: flex; flex-direction: column; gap: 2px; }
.admin-name { font-size: var(--font-base); font-weight: 600; color: var(--color-text); }
.admin-email { font-size: var(--font-sm); color: var(--color-text-light); }
.admin-right { display: flex; align-items: center; gap: 8px; }
.role-badge { padding: 2px 8px; border-radius: 999px; font-size: var(--font-sm); }
.role-badge.owner { background: #dbeafe; color: #1d4ed8; }
.role-badge.vendor { background: #fef9c3; color: #854d0e; }
.role-badge.platform_admin { background: #ede9fe; color: #6d28d9; }
.btn-icon-danger { background: none; border: none; color: var(--color-danger); cursor: pointer; font-size: 18px; padding: 2px 6px; }
.inactive-label { font-size: var(--font-sm); color: var(--color-text-light); }
.create-admin-box { border: 2px dashed var(--color-border); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
.empty { text-align: center; color: var(--color-text-light); padding: 16px 0; }
.loading { text-align: center; color: var(--color-text-light); padding: 48px 0; }
</style>
