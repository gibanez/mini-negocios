import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const theme = ref(localStorage.getItem('theme') || 'verde')
  const onboardingCompleted = ref(localStorage.getItem('onboardingCompleted') === 'true')

  const isAuthenticated = computed(() => !!token.value)
  const isOwner = computed(() => user.value?.role === 'owner')
  const isPlatformAdmin = computed(() => user.value?.role === 'platform_admin')
  const storeName = computed(() => user.value?.store_name || 'Mi Despensa')

  function setSession(tokenVal, userData) {
    token.value = tokenVal
    user.value = userData
    localStorage.setItem('token', tokenVal)
    localStorage.setItem('user', JSON.stringify(userData))

    if (userData.theme) {
      setTheme(userData.theme)
    }
    if (userData.onboardingCompleted !== undefined) {
      setOnboardingCompleted(userData.onboardingCompleted)
    }
  }

  function setTheme(t) {
    theme.value = t
    localStorage.setItem('theme', t)
    document.documentElement.setAttribute('data-theme', t)
  }

  function setOnboardingCompleted(val) {
    onboardingCompleted.value = !!val
    localStorage.setItem('onboardingCompleted', val ? 'true' : 'false')
  }

  function logout() {
    token.value = null
    user.value = null
    onboardingCompleted.value = false
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('onboardingCompleted')
  }

  // Apply theme on boot
  document.documentElement.setAttribute('data-theme', theme.value)

  return { token, user, theme, onboardingCompleted, isAuthenticated, isOwner, isPlatformAdmin, storeName, setSession, setTheme, setOnboardingCompleted, logout }
})
