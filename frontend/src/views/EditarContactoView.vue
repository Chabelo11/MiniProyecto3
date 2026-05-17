<template>
  <div class="page">
    <div class="container" style="max-width:680px">
      <div class="page-back">
        <router-link to="/agenda" class="btn btn-ghost btn-sm">← Volver</router-link>
      </div>

      <div v-if="cargandoDatos" class="spinner"></div>

      <div v-else-if="!contacto" class="card">
        <div class="alert alert-error">Contacto no encontrado.</div>
      </div>

      <div v-else class="card">
        <h2 class="section-title">Editar contacto</h2>
        <p class="section-sub">Modifica los datos de {{ contacto.nombre }}.</p>
        <ContactForm
          :model-value="contacto"
          :es-edicion="true"
          :cargando="cargando"
          :mensaje="mensaje"
          @submit="handleActualizar"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ContactForm from '@/components/ContactForm.vue'
import { useContactosStore } from '@/stores/contactos'

const store       = useContactosStore()
const route       = useRoute()
const router      = useRouter()
const cargando     = ref(false)
const cargandoDatos= ref(true)
const contacto     = ref(null)
const mensaje      = reactive({ texto: '', tipo: 'error' })

onMounted(async () => {
  const data = await store.obtener(route.params.id)
  if (data.success) {
    contacto.value = data.contacto
  }
  cargandoDatos.value = false
})

async function handleActualizar(fd) {
  if (!fd.get('nombre').trim())   { mensaje.texto = 'El nombre es obligatorio.';   mensaje.tipo = 'error'; return }
  if (!fd.get('telefono').trim()) { mensaje.texto = 'El teléfono es obligatorio.'; mensaje.tipo = 'error'; return }

  fd.append('id', contacto.value.id)
  cargando.value = true
  mensaje.texto  = ''
  try {
    const data = await store.actualizar(fd)
    if (data.success) {
      mensaje.texto = '¡Contacto actualizado!'
      mensaje.tipo  = 'success'
      setTimeout(() => router.push('/agenda'), 1000)
    } else {
      mensaje.texto = data.message || 'Error al actualizar.'
      mensaje.tipo  = 'error'
    }
  } catch {
    mensaje.texto = 'Error de conexión.'
    mensaje.tipo  = 'error'
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.page-back  { margin-bottom: 16px; }
.section-title { font-size: 1.5rem; margin-bottom: 4px; }
.section-sub   { color: var(--text-2); font-size: 0.88rem; margin-bottom: 24px; }
.btn-sm { padding: 7px 14px; font-size: 0.82rem; }
</style>
