import { createRouter, createWebHistory } from 'vue-router'
import { useAdminStore } from '@/stores/auth.js'
import LoginView       from '@/views/LoginView.vue'
import AdminLayout     from '@/layouts/AdminLayout.vue'
import DashboardView   from '@/views/DashboardView.vue'
import StoresView      from '@/views/StoresView.vue'
import StoreDetailView from '@/views/StoreDetailView.vue'
import StoreAdminsView from '@/views/StoreAdminsView.vue'

const routes = [
  { path: '/login', component: LoginView, meta: { public: true } },
  {
    path: '/',
    component: AdminLayout,
    children: [
      { path: '',              redirect: '/dashboard' },
      { path: 'dashboard',    component: DashboardView },
      { path: 'stores',       component: StoresView },
      { path: 'stores/:id',         component: StoreDetailView },
      { path: 'stores/:id/users',   component: StoreAdminsView },
    ]
  }
]

const router = createRouter({
  history: createWebHistory('/admin/'),
  routes
})

router.beforeEach((to) => {
  const auth = useAdminStore()
  if (to.meta.public) return true
  if (!auth.isAuthenticated) return '/login'
  return true
})

export default router
