<template>
  <div class="page">
    <div class="logo">🏪</div>
    <h1 class="title">Crear cuenta</h1>

    <form class="form" @submit.prevent="handleRegister">
      <BigInput v-model="storeName" label="Nombre del negocio" type="text" placeholder="Ej: Almacén Don Juan" :error="errors.storeName" />
      <BigInput v-model="ownerName" label="Tu nombre" type="text" placeholder="Ej: Juan García" :error="errors.ownerName" />
      <BigInput v-model="email" label="Correo" type="email" placeholder="tu@correo.com" :error="errors.email" />
      <BigInput v-model="password" label="Contraseña" type="password" placeholder="Mínimo 8 caracteres" :error="errors.password" />
      <BigInput v-model="confirm" label="Repetir contraseña" type="password" placeholder="••••••••" :error="errors.confirm" />
      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">Crear cuenta</BigBtn>
    </form>

    <p class="login-link">
      ¿Ya tenés cuenta?
      <router-link to="/login">Entrá</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { register } from '@/api/auth.js'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'

const router = useRouter()
const auth = useAuthStore()

const storeName = ref('')
const ownerName = ref('')
const email = ref('')
const password = ref('')
const confirm = ref('')
const loading = ref(false)
const errors = ref({})

async function handleRegister() {
  errors.value = {}
  if (!storeName.value.trim()) { errors.value.storeName = 'Requerido'; return }
  if (!ownerName.value.trim()) { errors.value.ownerName = 'Requerido'; return }
  if (!email.value) { errors.value.email = 'Requerido'; return }
  if (password.value.length < 8) { errors.value.password = 'Mínimo 8 caracteres'; return }
  if (password.value !== confirm.value) { errors.value.confirm = 'Las contraseñas no coinciden'; return }

  loading.value = true
  try {
    const data = await register(storeName.value.trim(), ownerName.value.trim(), email.value, password.value)
    auth.setSession(data.token, data.user)
    router.push('/onboarding')
  } catch (e) {
    errors.value.general = e.status === 409 ? 'Ese correo ya está registrado' : (e.data?.error || 'Error al crear la cuenta')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--gap);
  gap: var(--gap);
  background: var(--color-bg);
}
.logo { font-size: 64px; }
.title { font-size: var(--font-xl); margin: 0; color: var(--color-primary); }
.form { width: 100%; max-width: 420px; display: flex; flex-direction: column; gap: var(--gap); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; text-align: center; }
.login-link { font-size: var(--font-sm); color: var(--color-text-light); }
.login-link a { color: var(--color-primary); font-weight: 600; }
</style>
