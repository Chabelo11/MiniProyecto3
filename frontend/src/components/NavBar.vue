<template>
  <nav class="navbar">
    <div class="container nav-inner">
      <!-- Logo -->
      <router-link to="/agenda" class="nav-brand">
        <span class="brand-icon">◈</span>
        AgendaPro
      </router-link>

      <!-- Links -->
      <div class="nav-links">
        <router-link to="/agenda" class="nav-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          Agenda
        </router-link>
        <router-link to="/agenda/crear" class="nav-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
          Nuevo
        </router-link>
        <router-link to="/perfil" class="nav-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          Perfil
        </router-link>
      </div>

      <!-- Usuario + logout -->
      <div class="nav-user">
        <div class="user-info">
          <img v-if="auth.usuario?.foto" :src="auth.usuario.foto" class="avatar nav-avatar" alt="foto" />
          <div v-else class="avatar-placeholder nav-avatar">
            {{ inicial }}
          </div>
          <span class="username">{{ auth.usuario?.nombre_de_usuario }}</span>
        </div>
        <button class="btn btn-ghost btn-sm" @click="handleLogout">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Salir
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth   = useAuthStore()
const router = useRouter()

const inicial = computed(() =>
  (auth.usuario?.nombre_de_usuario?.[0] || '?').toUpperCase()
)

async function handleLogout() {
  await auth.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  background: rgba(10, 10, 15, 0.9);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 100;
}
.nav-inner {
  display: flex;
  align-items: center;
  gap: 24px;
  height: 60px;
}
.nav-brand {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 1.15rem;
  color: var(--text-1);
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.brand-icon { color: var(--accent); font-size: 1.3rem; }
.nav-links {
  display: flex;
  gap: 4px;
  flex: 1;
}
.nav-link {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: var(--radius-sm);
  color: var(--text-2);
  font-size: 0.88rem;
  font-weight: 500;
  transition: background var(--transition), color var(--transition);
}
.nav-link:hover { background: var(--bg-2); color: var(--text-1); opacity: 1; }
.nav-link.router-link-active {
  background: rgba(108,99,255,0.14);
  color: var(--accent);
}
.nav-user {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}
.user-info { display: flex; align-items: center; gap: 8px; }
.nav-avatar { width: 32px; height: 32px; font-size: 0.85rem; }
.username {
  font-size: 0.85rem;
  color: var(--text-2);
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.btn-sm { padding: 7px 14px; font-size: 0.8rem; }

@media (max-width: 640px) {
  .username { display: none; }
  .nav-links .nav-link span { display: none; }
}
</style>
