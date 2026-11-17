<template>
  <AppShell title="Configuración">

    <!-- Perfil del usuario -->
    <section class="config-section">
      <h2 class="section-title">Mi perfil</h2>
      <AppCard>
        <div class="profile-row">
          <div class="avatar">{{ auth.user?.name?.charAt(0)?.toUpperCase() }}</div>
          <div class="profile-info">
            <span class="profile-name">{{ auth.user?.name }}</span>
            <span class="profile-email">{{ auth.user?.email }}</span>
            <span class="profile-role">{{ roleLabel }}</span>
          </div>
        </div>
      </AppCard>
    </section>

    <!-- Datos del negocio -->
    <section class="config-section">
      <h2 class="section-title">Negocio</h2>
      <AppCard>
        <div class="field-row">
          <span class="field-label">Nombre</span>
          <span class="field-val" v-if="!editingName">{{ storeName }}</span>
          <input v-else v-model="newStoreName" class="inline-input" @keydown.enter="saveName" />
          <button class="btn-link" @click="toggleEditName">{{ editingName ? 'Cancelar' : 'Editar' }}</button>
        </div>
        <div v-if="editingName" class="save-row">
          <BigBtn @click="saveName" :loading="savingName">Guardar</BigBtn>
        </div>
        <div class="field-row">
          <span class="field-label">Rubro</span>
          <span class="field-val">{{ storeIndustry || '—' }}</span>
        </div>
        <div class="field-row" v-if="auth.isOwner">
          <span class="field-label">Usuarios</span>
          <router-link to="/configuracion/usuarios" class="btn-link">Gestionar →</router-link>
        </div>
      </AppCard>
    </section>

    <!-- Tema de color -->
    <section class="config-section">
      <h2 class="section-title">Color de la app</h2>
      <ThemePicker v-model="currentTheme" @update:modelValue="changeTheme" />
    </section>

    <!-- Cerrar sesión -->
    <section class="config-section">
      <BigBtn variant="ghost" @click="doLogout">Cerrar sesión</BigBtn>
      <BigBtn variant="danger" @click="showDeleteConfirm = true">Darme de baja</BigBtn>
    </section>

    <p v-if="saveMsg" class="save-msg">{{ saveMsg }}</p>

    <!-- Confirmar baja -->
    <div v-if="showDeleteConfirm" class="modal-overlay" @click.self="showDeleteConfirm = false">
      <div class="modal">
        <h3 class="modal-title">¿Eliminar mi cuenta?</h3>
        <p class="modal-text">Se borrará tu usuario permanentemente. Esta acción no se puede deshacer.</p>
        <div class="modal-btns">
          <BigBtn variant="ghost" @click="showDeleteConfirm = false">Cancelar</BigBtn>
          <BigBtn variant="danger" :loading="deletingMe" @click="deleteMyAccount">Eliminar</BigBtn>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '@/components/layout/AppShell.vue'
import AppCard from '@/components/ui/AppCard.vue'
import BigBtn from '@/components/ui/BigBtn.vue'
import { useAuthStore } from '@/stores/auth.js'
import { api } from '@/api/client.js'
import ThemePicker from '@/components/ui/ThemePicker.vue'

const router = useRouter()
const auth   = useAuthStore()


const currentTheme  = ref(auth.theme)
const storeName     = ref('')
const storeIndustry = ref('')
const editingName   = ref(false)
const newStoreName  = ref('')
const savingName    = ref(false)
const saveMsg          = ref('')
const showDeleteConfirm = ref(false)
const deletingMe        = ref(false)

const roleLabel = computed(() => {
  const r = auth.user?.role
  if (r === 'owner')  return 'Dueño'
  if (r === 'vendor') return 'Vendedor'
  return r || ''
})

async function changeTheme(id) {
  currentTheme.value = id
  auth.setTheme(id)
  try {
    await api.put('/onboarding/theme', { theme: id })
    saveMsg.value = 'Tema guardado.'
  } catch (e) {
    saveMsg.value = 'Error al guardar tema.'
  }
  setTimeout(() => { saveMsg.value = '' }, 2000)
}

function toggleEditName() {
  if (editingName.value) {
    editingName.value = false
  } else {
    newStoreName.value = storeName.value
    editingName.value = true
  }
}

async function saveName() {
  if (!newStoreName.value.trim()) return
  savingName.value = true
  try {
    await api.put('/onboarding/name', { name: newStoreName.value.trim() })
    storeName.value   = newStoreName.value.trim()
    editingName.value = false
    saveMsg.value = 'Nombre actualizado.'
    setTimeout(() => { saveMsg.value = '' }, 2000)
  } catch (e) {
    editingName.value = false
  } finally {
    savingName.value = false
  }
}

function doLogout() {
  auth.logout()
  router.push('/login')
}

async function deleteMyAccount() {
  deletingMe.value = true
  try {
    await api.delete('/me')
    auth.logout()
    router.push('/login')
  } catch (e) {
    saveMsg.value = 'Error al eliminar la cuenta.'
    showDeleteConfirm.value = false
  } finally {
    deletingMe.value = false
  }
}

onMounted(async () => {
  try {
    const data = await api.get('/onboarding')
    if (data) {
      storeName.value     = data.name || ''
      storeIndustry.value = data.industry || ''
      if (data.theme) {
        currentTheme.value = data.theme
        auth.setTheme(data.theme)
      }
    }
  } catch (e) { /* silent */ }
})
</script>

<style scoped>
.config-section { margin-bottom: var(--gap); display: flex; flex-direction: column; gap: 8px; }
.section-title { font-size: var(--font-base); font-weight: 700; margin: 0; color: var(--color-text); }
.profile-row { display: flex; align-items: center; gap: 14px; }
.avatar {
  width: 52px; height: 52px; border-radius: 50%;
  background: var(--color-primary); color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 700; flex-shrink: 0;
}
.profile-info { display: flex; flex-direction: column; gap: 2px; }
.profile-name { font-size: var(--font-base); font-weight: 700; color: var(--color-text); }
.profile-email { font-size: var(--font-sm); color: var(--color-text-light); }
.profile-role { font-size: var(--font-sm); color: var(--color-primary); font-weight: 600; }
.field-row {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 0; border-bottom: 1px solid var(--color-border);
}
.field-row:last-child { border-bottom: none; }
.field-label { font-size: var(--font-sm); color: var(--color-text-light); min-width: 70px; }
.field-val { flex: 1; font-size: var(--font-base); color: var(--color-text); }
.inline-input {
  flex: 1; padding: 6px 10px;
  border: 2px solid var(--color-primary); border-radius: var(--radius);
  font-size: var(--font-base); background: var(--color-input-bg); color: var(--color-text);
}
.btn-link { background: none; border: none; color: var(--color-primary); font-size: var(--font-sm); cursor: pointer; white-space: nowrap; }
.save-row { padding: 6px 0; }
.themes-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.theme-btn {
  aspect-ratio: 1; border-radius: var(--radius); border: 3px solid transparent;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  cursor: pointer; transition: border-color .15s; gap: 2px;
}
.theme-btn.selected { border-color: var(--color-text); }
.check { color: #fff; font-size: 18px; font-weight: 700; }
.theme-name { color: #fff; font-size: 10px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,.5); }
.save-msg { text-align: center; color: var(--color-primary); font-size: var(--font-sm); font-weight: 600; padding: 8px 0; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: flex-end; justify-content: center; z-index: 100; padding: var(--gap); }
.modal { background: var(--color-surface); border-radius: var(--radius-lg); padding: var(--gap); width: 100%; max-width: 500px; display: flex; flex-direction: column; gap: var(--gap); }
.modal-title { font-size: var(--font-lg); font-weight: 700; margin: 0; }
.modal-text { font-size: var(--font-base); color: var(--color-text-light); margin: 0; }
.modal-btns { display: flex; gap: var(--gap); }
.modal-btns > * { flex: 1; }
</style>
