<template>
  <div class="login-page">
    <div class="logo">🏪</div>
    <h1 class="app-name">Despensa</h1>
    <p class="tagline">Tu almacén en el bolsillo</p>

    <form class="form" @submit.prevent="handleLogin">
      <BigInput v-model="email" label="Correo" type="email" placeholder="tu@correo.com" :error="errors.email" />
      <BigInput v-model="password" label="Contraseña" type="password" placeholder="••••••••" :error="errors.password" />
      <p v-if="errors.general" class="error-msg">{{ errors.general }}</p>
      <BigBtn type="submit" :loading="loading">Entrar</BigBtn>
    </form>

    <p class="register-link">
      ¿Todavía no tenés cuenta?
      <router-link to="/register">Registrate</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'
import { login } from '@/api/auth.js'
import BigInput from '@/components/ui/BigInput.vue'
import BigBtn from '@/components/ui/BigBtn.vue'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errors = ref({})

async function handleLogin() {
  errors.value = {}
  if (!email.value) { errors.value.email = 'Requerido'; return }
  if (!password.value) { errors.value.password = 'Requerido'; return }

  loading.value = true
  try {
    const data = await login(email.value, password.value)
    auth.setSession(data.token, data.user)
    if (data.user.role === 'platform_admin') {
      router.push('/admin')
    } else if (!data.user.onboardingCompleted) {
      router.push('/onboarding')
    } else {
      router.push('/')
    }
  } catch (e) {
    errors.value.general = e.status === 401 ? 'Correo o contraseña incorrectos' : e.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--gap);
  gap: var(--gap);
  background: var(--color-bg);
}
.logo { font-size: 72px; }
.app-name { font-size: var(--font-2xl); margin: 0; color: var(--color-primary); }
.tagline { color: var(--color-text-light); margin: 0 0 var(--gap); font-size: var(--font-md); }
.form { width: 100%; max-width: 420px; display: flex; flex-direction: column; gap: var(--gap); }
.error-msg { color: var(--color-danger); font-size: var(--font-sm); margin: 0; text-align: center; }
.register-link { font-size: var(--font-sm); color: var(--color-text-light); }
.register-link a { color: var(--color-primary); font-weight: 600; }
</style>
