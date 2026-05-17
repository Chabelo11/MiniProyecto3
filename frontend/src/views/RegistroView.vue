<template>
  <div class="auth-page">
    <div class="auth-card card">
      <div class="auth-header">
        <div class="auth-brand">◈ AgendaPro</div>
        <h2>Crear cuenta</h2>
        <p class="auth-sub">Únete y gestiona tus contactos</p>
      </div>

      <div v-if="mensaje.texto" :class="['alert', `alert-${mensaje.tipo}`]">
        {{ mensaje.texto }}
      </div>

      <div class="form-group">
        <label>Nombre de usuario</label>
        <input
          v-model="form.nombre_de_usuario"
          type="text"
          placeholder="min. 3 caracteres"
          autocomplete="username"
        />
      </div>

      <div class="form-group">
        <label>Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          placeholder="min. 6 caracteres"
          autocomplete="new-password"
        />
      </div>

      <div class="form-group">
        <label>Foto de perfil <span class="opt">(opcional)</span></label>
        <div class="file-input-wrapper">
          <div class="file-input-btn">
            {{ fotoNombre || '📷 Seleccionar imagen' }}
          </div>
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            @change="onFoto"
          />
        </div>
        <img v-if="fotoPreview" :src="fotoPreview" class="preview-img" alt="preview" />
      </div>

      <button
        class="btn btn-primary btn-full"
        :disabled="cargando"
        @click="handleRegistro"
      >
        <span v-if="cargando">Registrando…</span>
        <span v-else>Crear cuenta</span>
      </button>

      <p class="auth-switch">
        ¿Ya tienes cuenta?
        <router-link to="/login">Inicia sesión</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth    = useAuthStore()
const router  = useRouter()
const cargando = ref(false)
const mensaje  = reactive({ texto: '', tipo: 'error' })

const form = reactive({ nombre_de_usuario: '', password: '' })
const fotoArchivo = ref(null)
const fotoNombre  = ref('')
const fotoPreview = ref('')

function onFoto(e) {
  const file = e.target.files[0]
  if (!file) return
  fotoArchivo.value = file
  fotoNombre.value  = file.name
  fotoPreview.value = URL.createObjectURL(file)
}

async function handleRegistro() {
  mensaje.texto = ''

  if (form.nombre_de_usuario.trim().length < 3) {
    mensaje.texto = 'El nombre de usuario debe tener al menos 3 caracteres.'; mensaje.tipo = 'error'; return
  }
  if (form.password.trim().length < 6) {
    mensaje.texto = 'La contraseña debe tener al menos 6 caracteres.'; mensaje.tipo = 'error'; return
  }

  const fd = new FormData()
  fd.append('nombre_de_usuario', form.nombre_de_usuario)
  fd.append('password', form.password)
  if (fotoArchivo.value) fd.append('foto', fotoArchivo.value)

  cargando.value = true
  try {
    const data = await auth.registrar(fd)
    if (data.success) {
      mensaje.texto = '¡Cuenta creada! Redirigiendo…'
      mensaje.tipo  = 'success'
      setTimeout(() => router.push('/login'), 1400)
    } else {
      mensaje.texto = data.message || 'Error al registrar.'
      mensaje.tipo  = 'error'
    }
  } catch {
    mensaje.texto = 'Error de conexión con el servidor.'
    mensaje.tipo  = 'error'
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background:
    radial-gradient(ellipse 50% 50% at 30% 10%, rgba(255,101,132,0.12) 0%, transparent 70%);
}
.auth-card { width: 100%; max-width: 420px; padding: 40px; }
.auth-brand { font-family: 'Syne',sans-serif; font-weight:800; font-size:1.1rem; color:var(--accent); margin-bottom:20px; }
.auth-header h2 { font-size:1.8rem; margin-bottom:6px; }
.auth-sub { color:var(--text-2); font-size:0.9rem; margin-bottom:28px; }
.btn-full { width:100%; margin-top:4px; padding:13px; font-size:0.95rem; }
.auth-switch { text-align:center; margin-top:20px; font-size:0.88rem; color:var(--text-2); }
.opt { color:var(--text-3); font-size:0.75rem; text-transform:none; letter-spacing:0; }
.preview-img {
  margin-top: 10px;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--border-hi);
}
</style>
