import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth.js'

const routes = [
  { path: '/login', name: 'login', component: () => import('@/views/auth/LoginView.vue'), meta: { public: true } },
  { path: '/register', name: 'register', component: () => import('@/views/auth/RegisterView.vue'), meta: { public: true } },
  { path: '/onboarding', name: 'onboarding', component: () => import('@/views/onboarding/OnboardingView.vue') },
  { path: '/public/fiado/:token', name: 'publicFiado', component: () => import('@/views/fiado/PublicSummaryView.vue'), meta: { public: true } },

  // Main app (requires auth + onboarding)
  { path: '/', redirect: '/home' },
  { path: '/home', name: 'home', component: () => import('@/views/home/HomeView.vue') },
  { path: '/configuracion', name: 'config', component: () => import('@/views/config/ConfigView.vue') },
  { path: '/config', redirect: '/configuracion' },
  { path: '/configuracion/usuarios', name: 'users', component: () => import('@/views/config/UsersView.vue') },
  { path: '/catalogo', name: 'catalog', component: () => import('@/views/catalog/CatalogView.vue') },
  { path: '/catalogo/categoria/nueva', name: 'categoryNew', component: () => import('@/views/catalog/CategoryFormView.vue') },
  { path: '/catalogo/categoria/:id', name: 'categoryEdit', component: () => import('@/views/catalog/CategoryFormView.vue') },
  { path: '/catalogo/producto/nuevo', name: 'productNew', component: () => import('@/views/catalog/ProductFormView.vue') },
  { path: '/catalogo/producto/:id', name: 'productEdit', component: () => import('@/views/catalog/ProductFormView.vue') },
  { path: '/catalogo/producto/:id/stock', name: 'stockAdjust', component: () => import('@/views/catalog/StockAdjustView.vue') },

  { path: '/vender', name: 'saleNew', component: () => import('@/views/sales/SaleNewView.vue') },
  { path: '/ventas', name: 'saleHistory', component: () => import('@/views/sales/SaleHistoryView.vue') },
  { path: '/ventas/:id', name: 'saleDetail', component: () => import('@/views/sales/SaleDetailView.vue') },

  { path: '/clientes', name: 'clientList', component: () => import('@/views/fiado/ClientListView.vue') },
  { path: '/clientes/nuevo', name: 'clientNew', component: () => import('@/views/fiado/ClientFormView.vue') },
  { path: '/clientes/:id', name: 'clientEdit', component: () => import('@/views/fiado/ClientFormView.vue') },
  { path: '/fiado/:clientId', name: 'fiadoAccount', component: () => import('@/views/fiado/FiadoAccountView.vue') },

  // Platform admin
  { path: '/admin', name: 'adminDash', component: () => import('@/views/admin/AdminDashView.vue'), meta: { requiresPlatformAdmin: true } },
  { path: '/admin/stores/:id', name: 'adminStore', component: () => import('@/views/admin/AdminStoreView.vue'), meta: { requiresPlatformAdmin: true } },
]

const router = createRouter({
  history: createWebHistory('/'),
  routes
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.public) return true

  if (!auth.isAuthenticated) return { name: 'login' }

  if (to.meta.requiresPlatformAdmin && !auth.isPlatformAdmin) return { path: '/' }

  // Platform admins bypass onboarding check
  if (auth.isPlatformAdmin) return true

  if (!auth.onboardingCompleted && to.name !== 'onboarding') {
    return { name: 'onboarding' }
  }

  return true
})

export default router
