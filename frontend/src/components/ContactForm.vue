<template>
  <div class="contact-form-wrap">
    <div v-if="mensaje.texto" :class="['alert', `alert-${mensaje.tipo}`]">{{ mensaje.texto }}</div>

    <div class="form-row">
      <div class="form-group">
        <label>Nombre <span class="req">*</span></label>
        <input v-model="local.nombre" type="text" placeholder="Nombre" />
      </div>
      <div class="form-group">
        <label>Apellido</label>
        <input v-model="local.apellido" type="text" placeholder="Apellido (opcional)" />
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Teléfono <span class="req">*</span></label>
        <input v-model="local.telefono" type="tel" placeholder="+52 961 000 0000" />
      </div>
      <div class="form-group">
        <label>Email</label>
        <input v-model="local.email" type="email" placeholder="correo@ejemplo.com" />
      </div>
    </div>

    <div class="form-group">
      <label>Dirección</label>
      <input v-model="local.direccion" type="text" placeholder="Calle, número, colonia…" />
    </div>

    <div class="form-group">
      <label>Notas</label>
      <textarea v-model="local.notas" placeholder="Información adicional…"></textarea>
    </div>

    <div class="form-group">
      <label>
        Foto del contacto
        <span v-if="!esEdicion" class="req">*</span>
        <span v-else class="opt">(deja vacío para mantener la actual)</span>
      </label>
      <div class="file-row">
        <div class="file-input-wrapper">
          <div class="file-input-btn">
            {{ fotoNombre || '📷 Seleccionar imagen' }}
          </div>
          <input type="file" accept="image/jpeg,image/png,image/webp" @change="onFoto" />
        </div>
        <img v-if="fotoPreview" :src="fotoPreview" class="foto-preview" alt="preview" />
      </div>
    </div>

    <div class="form-actions">
      <router-link to="/agenda" class="btn btn-ghost">Cancelar</router-link>
      <button class="btn btn-primary" :disabled="cargando" @click="handleSubmit">
        <span v-if="cargando">Guardando…</span>
        <span v-else>{{ esEdicion ? 'Actualizar contacto' : 'Crear contacto' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({}) },
  esEdicion:  { type: Boolean, default: false },
  cargando:   { type: Boolean, default: false },
  mensaje:    { type: Object, default: () => ({ texto: '', tipo: 'error' }) },
})

const emit = defineEmits(['submit'])

const local = reactive({
  nombre: '', apellido: '', telefono: '', email: '', direccion: '', notas: ''
})

// Sincronizar cuando viene datos de edición
watch(() => props.modelValue, (val) => {
  if (val) Object.assign(local, val)
}, { immediate: true })

const fotoArchivo = ref(null)
const fotoNombre  = ref('')
const fotoPreview = ref(props.modelValue?.foto || '')

function onFoto(e) {
  const file = e.target.files[0]
  if (!file) return
  fotoArchivo.value = file
  fotoNombre.value  = file.name
  fotoPreview.value = URL.createObjectURL(file)
}

function handleSubmit() {
  const fd = new FormData()
  fd.append('nombre',    local.nombre)
  fd.append('apellido',  local.apellido)
  fd.append('telefono',  local.telefono)
  fd.append('email',     local.email)
  fd.append('direccion', local.direccion)
  fd.append('notas',     local.notas)
  if (fotoArchivo.value) fd.append('foto', fotoArchivo.value)
  emit('submit', fd)
}
</script>

<style scoped>
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0 16px;
}
@media (max-width: 540px) { .form-row { grid-template-columns: 1fr; } }

.req  { color: var(--accent-2); font-size: 0.85em; }
.opt  { color: var(--text-3); font-size: 0.75rem; text-transform: none; letter-spacing: 0; }
.file-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.foto-preview {
  width: 72px; height: 72px;
  border-radius: var(--radius-sm);
  object-fit: cover;
  border: 2px solid var(--border-hi);
}
.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 8px;
}
</style>
