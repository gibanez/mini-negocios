<template>
  <div class="login-wrap">
    <el-card class="login-card" shadow="always">
      <div class="login-header">
        <span class="login-icon">🏪</span>
        <h2>Despensa Admin</h2>
        <p class="login-sub">Panel de administración de plataforma</p>
      </div>
      <el-form :model="form" label-position="top" @submit.prevent="handleLogin">
        <el-form-item label="Correo electrónico">
          <el-input
            v-model="form.email"
            type="email"
            size="large"
            placeholder="admin@despensa.app"
            :prefix-icon="Message"
          />
        </el-form-item>
        <el-form-item label="Contraseña">
          <el-input
            v-model="form.password"
            type="password"
            size="large"
            show-password
            placeholder="••••••••"
            :prefix-icon="Lock"
            @keydown.enter="handleLogin"
          />
        </el-form-item>
        <el-alert
          v-if="error"
          :title="error"
          type="error"
          show-icon
          :closable="false"
          style="margin-bottom: 16px;"
        />
        <el-button
          type="primary"
          native-type="submit"
          size="large"
          :loading="loading"
          style="width: 100%;"
        >
          Ingresar
        </el-button>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { Message, Lock } from '@element-plus/icons-vue'
import { useAdminStore } from '@/stores/auth.js'
import { login } from '@/api/auth.js'

const router  = useRouter()
const auth    = useAdminStore()
const form    = reactive({ email: '', password: '' })
const loading = ref(false)
const error   = ref('')

async function handleLogin() {
  error.value   = ''
  loading.value = true
  try {
    const data = await login(form.email, form.password)
    if (data.user.role !== 'platform_admin') {
      error.value = 'No tenés acceso al panel de administración.'
      return
    }
    auth.setSession(data.token, data.user)
    router.push('/dashboard')
  } catch (e) {
    error.value = e.status === 401
      ? 'Correo o contraseña incorrectos.'
      : 'Error de conexión. Intentá de nuevo.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-wrap {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--el-bg-color-page);
}
.login-card {
  width: 420px;
  max-width: 95vw;
}
.login-header {
  text-align: center;
  margin-bottom: 28px;
}
.login-icon { font-size: 52px; display: block; }
.login-header h2 {
  margin: 10px 0 4px;
  font-size: 22px;
  color: var(--el-color-primary);
}
.login-sub {
  margin: 0;
  font-size: 13px;
  color: var(--el-text-color-secondary);
}
</style>
