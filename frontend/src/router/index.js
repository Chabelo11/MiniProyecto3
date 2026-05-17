// src/router/index.js
import { createRouter, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import HomeView     from '@/views/HomeView.vue'
import LoginView    from '@/views/LoginView.vue'
import RegistroView from '@/views/RegistroView.vue'
import AgendaView   from '@/views/AgendaView.vue'
import CrearView    from '@/views/CrearContactoView.vue'
import EditarView   from '@/views/EditarContactoView.vue'
import PerfilView   from '@/views/PerfilView.vue'

const routes = [
  { path: '/',              name: 'home',    component: HomeView },
  { path: '/login',         name: 'login',   component: LoginView,    meta: { guest: true } },
  { path: '/registro',      name: 'registro',component: RegistroView, meta: { guest: true } },
  { path: '/agenda',        name: 'agenda',  component: AgendaView,   meta: { requiresAuth: true } },
  { path: '/agenda/crear',  name: 'crear',   component: CrearView,    meta: { requiresAuth: true } },
  { path: '/agenda/:id',    name: 'editar',  component: EditarView,   meta: { requiresAuth: true } },
  { path: '/perfil',        name: 'perfil',  component: PerfilView,   meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHashHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 })
})

// Guard global
router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login' }
  }
  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'agenda' }
  }
})

export default router
