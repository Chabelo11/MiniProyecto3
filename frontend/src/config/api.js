// src/config/api.js
// Lee la URL del backend desde /public/config.json
// Así se puede cambiar el backend SIN recompilar.

let cachedUrl = null

export async function getApiUrl() {
  if (cachedUrl) return cachedUrl

  const response = await fetch('/agenda-app/config.json')
  if (!response.ok) throw new Error('No se pudo cargar config.json')

  const config = await response.json()
  cachedUrl = config.API_URL.replace(/\/$/, '') // quitar trailing slash
  return cachedUrl
}

/**
 * Helper para peticiones protegidas (envía Authorization Bearer).
 * @param {string} endpoint  - Ruta relativa, ej: '/auth/perfil.php'
 * @param {Object} options   - Opciones de fetch
 * @param {string} token     - Token del store
 */
export async function apiFetch(endpoint, options = {}, token = null) {
  const base = await getApiUrl()
  const url  = `${base}${endpoint}`

  const headers = { ...(options.headers || {}) }
  if (token) headers['Authorization'] = `Bearer ${token}`

  // Solo añadir Content-Type JSON si NO es FormData
  if (!(options.body instanceof FormData) && !headers['Content-Type']) {
    headers['Content-Type'] = 'application/json'
  }

  const res = await fetch(url, { ...options, headers })

  if (!res.ok && res.status !== 401 && res.status !== 404) {
    // intenta parsear el error igual
  }

  return res.json()
}
