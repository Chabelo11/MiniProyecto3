<template>
  <div id="app-root">
    <!-- Navbar solo si está autenticado o en rutas no-auth -->
    <NavBar v-if="auth.isAuthenticated" />
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
</script>

<style>
#app-root {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
