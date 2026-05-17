// src/stores/contactos.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { apiFetch } from '@/config/api'
import { useAuthStore } from './auth'

export const useContactosStore = defineStore('contactos', () => {
  const contactos = ref([])
  const cargando  = ref(false)

  function getToken() {
    return useAuthStore().token
  }

  async function listar() {
    cargando.value = true
    try {
      const data = await apiFetch('/contactos/index.php', {}, getToken())
      if (data.success) contactos.value = data.contactos
      return data
    } finally {
      cargando.value = false
    }
  }

  async function obtener(id) {
    return apiFetch(`/contactos/detalle.php?id=${id}`, {}, getToken())
  }

  async function crear(formData) {
    const data = await apiFetch('/contactos/crear.php', {
      method: 'POST',
      body: formData
    }, getToken())
    if (data.success) await listar()
    return data
  }

  async function actualizar(formData) {
    const data = await apiFetch('/contactos/actualizar.php', {
      method: 'POST',
      body: formData
    }, getToken())
    if (data.success) await listar()
    return data
  }

  async function eliminar(id) {
    const data = await apiFetch('/contactos/eliminar.php', {
      method: 'POST',
      body: JSON.stringify({ id })
    }, getToken())
    if (data.success) await listar()
    return data
  }

  return { contactos, cargando, listar, obtener, crear, actualizar, eliminar }
})
