<template>
  <AppShell title="Usuarios" back="/configuracion">
    <template v-if="auth.isOwner" #actions>
      <button class="add-btn" @click="openCreate">+</button>
    </template>

    <LoadingSpinner v-if="loading" />
    <EmptyState v-else-if="!users.length" icon="👥" text="No hay usuarios aún" />

    <div v-else class="users-list">
      <AppCard
        v-for="u in users" :key="u.id"
        class="user-row"
        :clickable="auth.isOwner"
        @click="auth.isOwner && openEdit(u)"
      >
        <div class="user-main">
          <div class="user-avatar">{{ u.name.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <span class="user-name">{{ u.name }}</span>
            <span class="user-email">{{ u.email }}</span>
          </div>
        </div>
        <div class="user-badges">
          <span class="badge role" :class="u.role">{{ roleLabel(u.role) }}</span>
          <span class="badge status" :class="u.status">{{ u.status === 'active' ? 'Activo' : 'Inactivo' }}</span>
        </div>
      </AppCard>
    </div>

    <!-- Create modal -->
    <div v-if="showCreate" class="modal-overlay" @click.self="showCreate = false">
      <div class="modal">
        <h3 class="modal-title">Nuevo usuario</h3>
        <div class="field">
          <label class="label">Nombre *</label>
          <input v-model="form.name" class="input" placeholder="Nombre completo" />
        </div>
        <div class="field">
          <label class="label">Email *</label>
          <input v-model="form.email" class="input" type="email" placeholder="correo@ejemplo.com" />
        </div>
        <div class="field">
          <label class="label">Contraseña *</label>
          <input v-model="form.password" class="input" type="password" placeholder="Mínimo 6 caracteres" />
        </div>
        <div class="field">
          <label class="label">Rol</label>
          <select v-model="form.role" class="select">
            <option value="vendor">Vendedor</option>
            <option value="owner">Dueño</option>
          </select>
        </div>
        <p v-if="formError" class="error-msg">{{ formError }}</p>
        <div class="modal-actions">
          <button class="btn-ghost" @click="showCreate = false">Cancelar</button>
          <button class="btn-primary" @click="submitCreate" :disabled="saving">Guardar</button>
        </div>
      </div>
    </div>

    <!-- Edit modal -->
    <div v-if="editingUser" class="modal-overlay" @click.self="editingUser = null">
      <div class="modal">
        <h3 class="modal-title">Editar usuario</h3>
        <div class="field">
          <label class="label">Nombre</label>
          <input v-model="editForm.name" class="input" />
        </div>
        <div class="field">
          <label class="label">Estado</label>
          <select v-model="editForm.status" class="select">
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
          </select>
        </div>
        <p v-if="editError" class="error-msg">{{ editError }}</p>
        <div class="modal-actions">
          <button class="btn-ghost" @click="editingUser = null">Cancelar</button>
          <button class="btn-primary" @click="submitEdit" :disabled="saving">Guardar</button>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getUsers, createUser, updateUser } from '@/api/users.js'
import { useAuthStore } from '@/stores/auth.js'
import AppShell from '@/components/layout/AppShell.vue'
import AppCard from '@/components/ui/AppCard.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'

const auth = useAuthStore()

const users = ref([])
const loading = ref(false)
const saving = ref(false)

const showCreate = ref(false)
const form = ref({ name: '', email: '', password: '', role: 'vendor' })
const formError = ref('')

const editingUser = ref(null)
const editForm = ref({ name: '', status: 'active' })
const editError = ref('')

async function load() {
  loading.value = true
  try {
    const data = await getUsers()
    users.value = Array.isArray(data) ? data : []
  } finally {
    loading.value = false
  }
}

onMounted(load)

function openCreate() {
  form.value = { name: '', email: '', password: '', role: 'vendor' }
  formError.value = ''
  showCreate.value = true
}

function openEdit(u) {
  editingUser.value = u
  editForm.value = { name: u.name, status: u.status }
  editError.value = ''
}

async function submitCreate() {
  formError.value = ''
  if (!form.value.name.trim() || !form.value.email.trim() || !form.value.password.trim()) {
    formError.value = 'Nombre, email y contraseña son requeridos.'
    return
  }
  saving.value = true
  try {
    await createUser({
      name: form.value.name.trim(),
      email: form.value.email.trim(),
      password: form.value.password,
      role: form.value.role,
    })
    showCreate.value = false
    await load()
  } catch (e) {
    formError.value = e.message
  } finally {
    saving.value = false
  }
}

async function submitEdit() {
  editError.value = ''
  saving.value = true
  try {
    await updateUser(editingUser.value.id, {
      name: editForm.value.name.trim(),
      status: editForm.value.status,
    })
    editingUser.value = null
    await load()
  } catch (e) {
    editError.value = e.message
  } finally {
    saving.value = false
  }
}

function roleLabel(role) {
  if (role === 'owner') return 'Dueño'
  if (role === 'vendor') return 'Vendedor'
  return role
}
</script>

<style scoped>
.add-btn {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,0.25); color: #fff;
  font-size: 24px; font-weight: bold;
  display: flex; align-items: center; justify-content: center;
}
.users-list { display: flex; flex-direction: column; gap: var(--gap-sm); }
.user-row { display: flex; justify-content: space-between; align-items: center; gap: var(--gap); }
.user-main { display: flex; align-items: center; gap: 12px; min-width: 0; }
.user-avatar {
  width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
  background: var(--color-primary); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: var(--font-lg); font-weight: 700;
}
.user-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.user-name { font-weight: 600; font-size: var(--font-base); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-email { font-size: var(--font-sm); color: var(--color-text-light); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-badges { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0; }
.badge { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
.badge.role.owner  { background: var(--color-primary-light); color: var(--color-primary); }
.badge.role.vendor { background: #e3f2fd; color: #1565c0; }
.badge.status.active   { background: #e8f5e9; color: #2e7d32; }
.badge.status.inactive { background: #fce4e4; color: #c62828; }

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
.field { display: flex; flex-direction: column; gap: 6px; }
.label { font-size: var(--font-sm); font-weight: 600; }
.input {
  width: 100%; min-height: 54px; padding: 0 var(--gap);
  border: 1.5px solid var(--color-border); border-radius: var(--radius-md);
  background: var(--color-bg); color: var(--color-text);
  font-size: var(--font-base); box-sizing: border-box;
}
.select { min-height: 54px; }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; }
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
