<template>
  <div class="auth-page">
    <div class="auth-card card">
      <div class="auth-header">
        <div class="auth-brand">◈ AgendaPro</div>
        <h2>Iniciar sesión</h2>
        <p class="auth-sub">Accede a tu agenda personal</p>
      </div>

      <div v-if="error" class="alert alert-error">{{ error }}</div>

      <div class="form-group">
        <label>Nombre de usuario</label>
        <input
          v-model="form.nombre_de_usuario"
          type="text"
          placeholder="tu_usuario"
          autocomplete="username"
          @keyup.enter="handleLogin"
        />
      </div>

      <div class="form-group">
        <label>Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          placeholder="••••••••"
          autocomplete="current-password"
          @keyup.enter="handleLogin"
        />
      </div>

      <button
        class="btn btn-primary btn-full"
        :disabled="cargando"
        @click="handleLogin"
      >
        <span v-if="cargando">Ingresando…</span>
        <span v-else>Ingresar</span>
      </button>

      <p class="auth-switch">
        ¿No tienes cuenta?
        <router-link to="/registro">Regístrate</router-link>
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
const error    = ref('')

const form = reactive({
  nombre_de_usuario: '',
  password: ''
})

async function handleLogin() {
  error.value = ''

  // Validación front-end
  if (!form.nombre_de_usuario.trim()) { error.value = 'Ingresa tu nombre de usuario.'; return }
  if (!form.password.trim())          { error.value = 'Ingresa tu contraseña.'; return }

  cargando.value = true
  try {
    const data = await auth.login(form.nombre_de_usuario, form.password)
    if (data.success) {
      router.push('/agenda')
    } else {
      error.value = data.message || 'Credenciales incorrectas.'
    }
  } catch {
    error.value = 'Error de conexión con el servidor.'
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
    radial-gradient(ellipse 50% 50% at 50% 0%, rgba(108,99,255,0.15) 0%, transparent 70%);
}
.auth-card {
  width: 100%;
  max-width: 420px;
  padding: 40px;
}
.auth-brand {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 1.1rem;
  color: var(--accent);
  margin-bottom: 20px;
}
.auth-header h2 { font-size: 1.8rem; margin-bottom: 6px; }
.auth-sub { color: var(--text-2); font-size: 0.9rem; margin-bottom: 28px; }
.btn-full { width: 100%; margin-top: 4px; padding: 13px; font-size: 0.95rem; }
.auth-switch {
  text-align: center;
  margin-top: 20px;
  font-size: 0.88rem;
  color: var(--text-2);
}
</style>
