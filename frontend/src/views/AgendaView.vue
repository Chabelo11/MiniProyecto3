<template>
  <div class="page">
    <div class="container">
      <!-- Header -->
      <div class="agenda-header">
        <div>
          <h1 class="page-title">Mis Contactos</h1>
          <p class="page-sub">{{ contactosStore.contactos.length }} contacto(s) registrado(s)</p>
        </div>
        <router-link to="/agenda/crear" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 5v14M5 12h14"/>
          </svg>
          Agregar
        </router-link>
      </div>

      <!-- Buscador -->
      <div class="search-bar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
          <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        <input v-model="busqueda" type="text" placeholder="Buscar contacto…" class="search-input" />
      </div>

      <!-- Cargando -->
      <div v-if="contactosStore.cargando" class="spinner"></div>

      <!-- Vacío -->
      <div v-else-if="filtrados.length === 0" class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>{{ busqueda ? 'Sin resultados' : 'Sin contactos aún' }}</h3>
        <p>{{ busqueda ? 'Prueba con otro término.' : 'Agrega tu primer contacto.' }}</p>
        <router-link v-if="!busqueda" to="/agenda/crear" class="btn btn-primary" style="margin-top:16px">
          Crear contacto
        </router-link>
      </div>

      <!-- Grid de contactos -->
      <div v-else class="grid-2">
        <div
          v-for="c in filtrados"
          :key="c.id"
          class="contact-card card"
        >
          <!-- Foto -->
          <div class="contact-top">
            <img
              v-if="c.foto"
              :src="c.foto"
              class="avatar avatar-lg contact-foto"
              :alt="c.nombre"
            />
            <div v-else class="avatar-placeholder avatar-lg contact-foto">
              {{ (c.nombre[0] || '?').toUpperCase() }}
            </div>
            <div class="contact-info">
              <h3 class="contact-name">{{ c.nombre }} {{ c.apellido }}</h3>
              <p class="contact-tel">📞 {{ c.telefono }}</p>
              <p v-if="c.email" class="contact-detail">✉ {{ c.email }}</p>
            </div>
          </div>

          <div v-if="c.direccion" class="contact-dir">📍 {{ c.direccion }}</div>
          <div v-if="c.notas" class="contact-notes">{{ c.notas }}</div>

          <!-- Acciones -->
          <div class="contact-actions">
            <router-link :to="`/agenda/${c.id}`" class="btn btn-ghost btn-sm-card">
              ✏️ Editar
            </router-link>
            <button class="btn btn-danger btn-sm-card" @click="confirmarEliminar(c)">
              🗑 Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal confirmación eliminación -->
    <Teleport to="body">
      <div v-if="modalEliminar" class="modal-overlay" @click.self="modalEliminar = null">
        <div class="modal-box card">
          <h3>¿Eliminar contacto?</h3>
          <p>Se eliminará a <strong>{{ modalEliminar.nombre }}</strong> permanentemente.</p>
          <div class="modal-actions">
            <button class="btn btn-ghost" @click="modalEliminar = null">Cancelar</button>
            <button class="btn btn-danger" :disabled="eliminando" @click="doEliminar">
              {{ eliminando ? 'Eliminando…' : 'Sí, eliminar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useContactosStore } from '@/stores/contactos'

const contactosStore = useContactosStore()
const busqueda       = ref('')
const modalEliminar  = ref(null)
const eliminando     = ref(false)

const filtrados = computed(() => {
  if (!busqueda.value.trim()) return contactosStore.contactos
  const q = busqueda.value.toLowerCase()
  return contactosStore.contactos.filter(c =>
    `${c.nombre} ${c.apellido} ${c.telefono} ${c.email}`.toLowerCase().includes(q)
  )
})

function confirmarEliminar(c) { modalEliminar.value = c }

async function doEliminar() {
  eliminando.value = true
  await contactosStore.eliminar(modalEliminar.value.id)
  eliminando.value = false
  modalEliminar.value = null
}

onMounted(() => contactosStore.listar())
</script>

<style scoped>
.agenda-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 28px;
  flex-wrap: wrap;
}
.page-title { font-size: 2rem; }
.page-sub   { color: var(--text-2); font-size: 0.88rem; margin-top: 4px; }

.search-bar {
  position: relative;
  margin-bottom: 28px;
}
.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-3);
}
.search-input {
  padding-left: 44px !important;
  background: var(--bg-1) !important;
}

.contact-card { display: flex; flex-direction: column; gap: 12px; }
.contact-top  { display: flex; gap: 14px; align-items: flex-start; }
.contact-foto { flex-shrink: 0; }
.contact-info { flex: 1; min-width: 0; }
.contact-name { font-size: 1rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.contact-tel  { font-size: 0.88rem; color: var(--accent-3); margin-top: 2px; }
.contact-detail { font-size: 0.82rem; color: var(--text-2); }
.contact-dir  { font-size: 0.82rem; color: var(--text-2); }
.contact-notes {
  font-size: 0.82rem;
  color: var(--text-3);
  background: var(--bg-2);
  border-radius: var(--radius-sm);
  padding: 8px 12px;
  white-space: pre-wrap;
}
.contact-actions { display: flex; gap: 8px; margin-top: 4px; }
.btn-sm-card { padding: 7px 14px; font-size: 0.8rem; flex: 1; justify-content: center; }

.empty-state {
  text-align: center;
  padding: 80px 20px;
  color: var(--text-2);
}
.empty-icon { font-size: 3rem; margin-bottom: 16px; }
.empty-state h3 { font-size: 1.2rem; color: var(--text-1); margin-bottom: 6px; }

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 24px;
}
.modal-box { max-width: 380px; width: 100%; }
.modal-box h3 { font-size: 1.2rem; margin-bottom: 10px; }
.modal-box p  { color: var(--text-2); font-size: 0.9rem; margin-bottom: 20px; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
</style>
