<template>
  <div class="page">
    <div class="container" style="max-width:680px">
      <div class="page-back">
        <router-link to="/agenda" class="btn btn-ghost btn-sm">← Volver</router-link>
      </div>
      <div class="card">
        <h2 class="section-title">Nuevo contacto</h2>
        <p class="section-sub">Completa los campos para agregar un contacto.</p>
        <ContactForm
          :cargando="cargando"
          :mensaje="mensaje"
          @submit="handleCrear"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import ContactForm from '@/components/ContactForm.vue'
import { useContactosStore } from '@/stores/contactos'

const store    = useContactosStore()
const router   = useRouter()
const cargando = ref(false)
const mensaje  = reactive({ texto: '', tipo: 'error' })

async function handleCrear(fd) {
  // Validación front-end
  if (!fd.get('nombre').trim())   { mensaje.texto = 'El nombre es obligatorio.';   mensaje.tipo = 'error'; return }
  if (!fd.get('telefono').trim()) { mensaje.texto = 'El teléfono es obligatorio.'; mensaje.tipo = 'error'; return }
  if (!fd.get('foto')?.size)      { mensaje.texto = 'La foto del contacto es obligatoria.'; mensaje.tipo = 'error'; return }

  cargando.value = true
  mensaje.texto  = ''
  try {
    const data = await store.crear(fd)
    if (data.success) {
      router.push('/agenda')
    } else {
      mensaje.texto = data.message || 'Error al crear el contacto.'
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
