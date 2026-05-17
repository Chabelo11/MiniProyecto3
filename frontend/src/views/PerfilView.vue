<template>
  <div class="page">
    <div class="container" style="max-width:560px">
      <h2 class="page-title" style="margin-bottom:24px">Mi Perfil</h2>

      <!-- Info actual -->
      <div class="card perfil-card">
        <div class="perfil-top">
          <img v-if="auth.usuario?.foto" :src="auth.usuario.foto" class="avatar avatar-lg" alt="foto" />
          <div v-else class="avatar-placeholder avatar-lg">
            {{ (auth.usuario?.nombre_de_usuario?.[0] || '?').toUpperCase() }}
          </div>
          <div>
            <h3>{{ auth.usuario?.nombre_de_usuario }}</h3>
            <p class="text-muted">Miembro desde {{ fechaRegistro }}</p>
          </div>
        </div>
      </div>

      <!-- Formulario de edición -->
      <div class="card" style="margin-top:20px">
        <h3 class="section-title">Editar perfil</h3>

        <div v-if="mensaje.texto" :class="['alert', `alert-${mensaje.tipo}`]">{{ mensaje.texto }}</div>

        <div class="form-group">
          <label>Nuevo nombre de usuario</label>
          <input v-model="form.nombre_de_usuario" type="text" placeholder="Dejar vacío para no cambiar" />
        </div>

        <div class="form-group">
          <label>Nueva contraseña</label>
          <input v-model="form.password" type="password" placeholder="Dejar vacío para no cambiar" />
        </div>

        <div class="form-group">
          <label>Nueva foto de perfil <span class="opt">(opcional)</span></label>
          <div class="file-row">
            <div class="file-input-wrapper">
              <div class="file-input-btn">{{ fotoNombre || '📷 Seleccionar imagen' }}</div>
              <input type="file" accept="image/jpeg,image/png,image/webp" @change="onFoto" />
            </div>
            <img v-if="fotoPreview" :src="fotoPreview" class="foto-preview" alt="preview" />
          </div>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary" :disabled="cargando" @click="handleEditar">
            {{ cargando ? 'Guardando…' : 'Guardar cambios' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const cargando = ref(false)
const mensaje  = reactive({ texto: '', tipo: 'error' })

const form = reactive({ nombre_de_usuario: '', password: '' })
const fotoArchivo = ref(null)
const fotoNombre  = ref('')
const fotoPreview = ref('')

const fechaRegistro = computed(() => {
  const u = auth.usuario
  if (!u?.fecha_registro) return '—'
  return new Date(u.fecha_registro).toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' })
})

function onFoto(e) {
  const file = e.target.files[0]
  if (!file) return
  fotoArchivo.value = file
  fotoNombre.value  = file.name
  fotoPreview.value = URL.createObjectURL(file)
}

async function handleEditar() {
  mensaje.texto = ''

  if (form.password && form.password.length < 6) {
    mensaje.texto = 'La contraseña debe tener al menos 6 caracteres.'
    mensaje.tipo  = 'error'
    return
  }

  const fd = new FormData()
  if (form.nombre_de_usuario.trim()) fd.append('nombre_de_usuario', form.nombre_de_usuario)
  if (form.password.trim())          fd.append('password', form.password)
  if (fotoArchivo.value)             fd.append('foto', fotoArchivo.value)

  cargando.value = true
  try {
    const data = await auth.editarPerfil(fd)
    if (data.success) {
      mensaje.texto = '¡Perfil actualizado correctamente!'
      mensaje.tipo  = 'success'
      form.nombre_de_usuario = ''
      form.password          = ''
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

onMounted(() => auth.obtenerPerfil())
</script>

<style scoped>
.perfil-top {
  display: flex;
  align-items: center;
  gap: 20px;
}
.perfil-top h3 { font-size: 1.25rem; }
.text-muted { color: var(--text-2); font-size: 0.85rem; margin-top: 4px; }
.section-title { font-size: 1.15rem; margin-bottom: 20px; }
.opt  { color: var(--text-3); font-size: 0.75rem; text-transform: none; letter-spacing: 0; }
.file-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.foto-preview {
  width: 64px; height: 64px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border-hi);
}
.form-actions { display: flex; justify-content: flex-end; margin-top: 8px; }
</style>
