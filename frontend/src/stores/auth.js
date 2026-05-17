// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiFetch } from '@/config/api'

export const useAuthStore = defineStore('auth', () => {
  // ── State ──────────────────────────────────────────────
  const token   = ref(localStorage.getItem('token')   || null)
  const usuario = ref(JSON.parse(localStorage.getItem('usuario') || 'null'))

  // ── Getters ────────────────────────────────────────────
  const isAuthenticated = computed(() => !!token.value)

  // ── Actions ────────────────────────────────────────────
  async function login(nombre_de_usuario, password) {
    const data = await apiFetch('/auth/login.php', {
      method: 'POST',
      body: JSON.stringify({ nombre_de_usuario, password })
    })

    if (data.success) {
      token.value   = data.token
      usuario.value = data.usuario
      localStorage.setItem('token',   data.token)
      localStorage.setItem('usuario', JSON.stringify(data.usuario))
    }

    return data
  }

  async function registrar(formData) {
    // formData = FormData con nombre_de_usuario, password, foto(opcional)
    const data = await apiFetch('/auth/registrar.php', {
      method: 'POST',
      body: formData
    })
    return data
  }

  async function obtenerPerfil() {
    const data = await apiFetch('/auth/perfil.php', {}, token.value)
    if (data.success) {
      usuario.value = data.usuario
      localStorage.setItem('usuario', JSON.stringify(data.usuario))
    }
    return data
  }

  async function editarPerfil(formData) {
    const data = await apiFetch('/auth/editar.php', {
      method: 'POST',
      body: formData
    }, token.value)

    if (data.success) {
      await obtenerPerfil()
    }
    return data
  }

  async function logout() {
    try {
      await apiFetch('/auth/logout.php', { method: 'POST' }, token.value)
    } finally {
      token.value   = null
      usuario.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('usuario')
    }
  }

  return {
    token, usuario,
    isAuthenticated,
    login, registrar, obtenerPerfil, editarPerfil, logout
  }
})
