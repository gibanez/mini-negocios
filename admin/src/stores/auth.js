import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAdminStore = defineStore('admin-auth', () => {
  const token    = ref(localStorage.getItem('admin_token') || null)
  const user     = ref(JSON.parse(localStorage.getItem('admin_user') || 'null'))
  const darkMode = ref(localStorage.getItem('admin_dark') === 'true')

  const isAuthenticated = computed(() => !!token.value)

  function setSession(t, u) {
    token.value = t
    user.value  = u
    localStorage.setItem('admin_token', t)
    localStorage.setItem('admin_user', JSON.stringify(u))
  }

  function toggleDark() {
    darkMode.value = !darkMode.value
    localStorage.setItem('admin_dark', darkMode.value ? 'true' : 'false')
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('admin_token')
    localStorage.removeItem('admin_user')
  }

  return { token, user, darkMode, isAuthenticated, setSession, toggleDark, logout }
})
