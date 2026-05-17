<template>
  <div class="home-page">
    <!-- Hero -->
    <div class="hero-bg"></div>
    <section class="hero container">
      <div class="hero-badge">
        <span class="badge">Full Stack · Vue 3 + PHP · MySQL</span>
      </div>
      <h1 class="hero-title">
        Tu agenda de<br /><em class="highlight">contactos</em><br />en la nube.
      </h1>
      <p class="hero-sub">
        Gestiona todos tus contactos de forma segura, desde cualquier dispositivo.
        Desarrollado con Vue 3, Pinia, PHP y MySQL.
      </p>
      <div class="hero-actions">
        <router-link to="/registro" class="btn btn-primary btn-hero">
          Crear cuenta gratis
        </router-link>
        <router-link to="/login" class="btn btn-ghost btn-hero">
          Iniciar sesión
        </router-link>
      </div>
    </section>

    <!-- Features -->
    <section class="features container">
      <div class="feat-grid">
        <div class="feat-card" v-for="f in features" :key="f.title">
          <div class="feat-icon">{{ f.icon }}</div>
          <h3>{{ f.title }}</h3>
          <p>{{ f.desc }}</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const features = [
  { icon: '🔐', title: 'Autenticación segura', desc: 'Token propietario almacenado en Pinia. Cada usuario solo ve sus propios contactos.' },
  { icon: '📇', title: 'CRUD completo', desc: 'Crea, edita y elimina contactos con foto. Interfaz rápida y reactiva con Vue 3.' },
  { icon: '🖼️', title: 'Subida de imágenes', desc: 'Foto de perfil y foto de cada contacto. Formatos JPG, PNG y WEBP.' },
  { icon: '⚡', title: 'Interoperable', desc: 'Cambia el backend editando solo config.json. Sin recompilar.' },
]
</script>

<style scoped>
.home-page { position: relative; overflow: hidden; }

.hero-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 70% 0%, rgba(108,99,255,0.18) 0%, transparent 70%),
    radial-gradient(ellipse 40% 40% at 10% 80%, rgba(255,101,132,0.1) 0%, transparent 60%);
  pointer-events: none;
}

.hero {
  padding: 100px 24px 80px;
  max-width: 700px;
}

.hero-badge { margin-bottom: 24px; }

.hero-title {
  font-size: clamp(2.6rem, 7vw, 5rem);
  line-height: 1.08;
  margin-bottom: 24px;
  letter-spacing: -0.02em;
}
.highlight {
  font-style: normal;
  background: linear-gradient(135deg, var(--accent), var(--accent-2));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.hero-sub {
  font-size: 1.05rem;
  color: var(--text-2);
  max-width: 520px;
  margin-bottom: 36px;
}
.hero-actions { display: flex; gap: 14px; flex-wrap: wrap; }
.btn-hero { padding: 14px 32px; font-size: 1rem; }

.features { padding: 60px 24px; }
.feat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 16px;
}
.feat-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 28px;
  transition: border-color var(--transition);
}
.feat-card:hover { border-color: var(--accent); }
.feat-icon { font-size: 2rem; margin-bottom: 14px; }
.feat-card h3 { font-size: 1rem; margin-bottom: 8px; }
.feat-card p  { font-size: 0.88rem; color: var(--text-2); }
</style>
