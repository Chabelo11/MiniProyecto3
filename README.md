# AgendaPro — Mini Proyecto 3
**Full Stack: Vue 3 + PHP + MySQL/MariaDB**

---

## 📁 Estructura del Proyecto

```
MiniProyecto3_AgendaPro/
├── backend/
│   ├── database.sql              ← Ejecutar primero en MySQL
│   └── api/
│       ├── .htaccess
│       ├── config/
│       │   ├── database.php      ← ⚠️ EDITAR credenciales
│       │   ├── cors.php
│       │   └── auth.php
│       ├── auth/
│       │   ├── login.php
│       │   ├── registrar.php
│       │   ├── perfil.php
│       │   ├── editar.php
│       │   └── logout.php
│       └── contactos/
│           ├── index.php
│           ├── detalle.php
│           ├── crear.php
│           ├── actualizar.php
│           └── eliminar.php
│
├── frontend/
│   ├── public/
│   │   └── config.json           ← ⚠️ EDITAR URL del backend
│   ├── src/
│   │   ├── config/api.js
│   │   ├── stores/ (auth.js, contactos.js)
│   │   ├── router/index.js
│   │   ├── views/ (7 vistas)
│   │   ├── components/ (NavBar, ContactForm)
│   │   ├── assets/css/main.css
│   │   ├── App.vue
│   │   └── main.js
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
└── Reporte_MiniProyecto3.docx
```

---

## 🚀 Pasos de Despliegue

### 1️⃣ Base de Datos

1. Accede a **phpMyAdmin** (o tu cliente MySQL)
2. Ejecuta el archivo `backend/database.sql`
3. Esto crea la base de datos `agenda_db` con las tablas `usuarios` y `contactos`

---

### 2️⃣ Back-End (Hosting PHP)

1. Edita `backend/api/config/database.php`:
   ```php
   private string $host     = 'localhost';   // tu host
   private string $db_name  = 'agenda_db';   // nombre de tu BD
   private string $username = 'root';        // tu usuario MySQL
   private string $password = '';            // tu contraseña MySQL
   ```

2. Sube **toda la carpeta** `backend/` a tu hosting (free.nf, InfinityFree, etc.)
   - La carpeta `api/` debe quedar accesible como `https://tu-dominio.free.nf/api/`

3. Verifica que los directorios de uploads tengan permisos de escritura:
   ```
   api/uploads/usuarios/   → chmod 755
   api/uploads/contactos/  → chmod 755
   ```

4. Prueba el endpoint: `https://tu-dominio.free.nf/api/auth/login.php`
   - Debe responder con JSON (aunque sea error 405 por GET)

---

### 3️⃣ Front-End (GitHub Pages)

1. Edita `frontend/public/config.json` con la URL real de tu backend:
   ```json
   {
     "API_URL": "https://tu-equipo.free.nf/api"
   }
   ```

2. Edita `frontend/vite.config.js` y cambia `base` por el nombre de tu repositorio:
   ```js
   base: '/nombre-de-tu-repo/',
   ```
   > También actualiza la línea en `src/config/api.js`:
   > ```js
   > const response = await fetch('/nombre-de-tu-repo/config.json')
   > ```

3. Instala dependencias y construye:
   ```bash
   cd frontend
   npm install
   npm run build
   ```

4. El directorio `dist/` generado es el que se publica en **GitHub Pages**:
   - Ve a tu repositorio → Settings → Pages
   - Source: **GitHub Actions** o sube el contenido de `dist/` a la rama `gh-pages`

   Con GitHub Actions (recomendado), crea `.github/workflows/deploy.yml`:
   ```yaml
   name: Deploy to GitHub Pages
   on:
     push:
       branches: [main]
   jobs:
     deploy:
       runs-on: ubuntu-latest
       steps:
         - uses: actions/checkout@v4
         - uses: actions/setup-node@v4
           with:
             node-version: 20
         - run: cd frontend && npm install && npm run build
         - uses: peaceiris/actions-gh-pages@v3
           with:
             github_token: ${{ secrets.GITHUB_TOKEN }}
             publish_dir: ./frontend/dist
   ```

---

### 4️⃣ Cambiar de Backend (Interoperabilidad)

Para conectar tu Front-End al backend de otro equipo:

1. Edita **únicamente** `frontend/public/config.json`:
   ```json
   {
     "API_URL": "https://otro-equipo.free.nf/api"
   }
   ```

2. **NO** recompiles el proyecto
3. Recarga el navegador → el sistema se conecta al nuevo backend ✅

---

## 🔐 Autenticación

| Paso | Descripción |
|------|-------------|
| Registro | `POST /api/auth/registrar.php` con `nombre_de_usuario` + `password` |
| Login | `POST /api/auth/login.php` → devuelve `token` |
| Peticiones protegidas | Header: `Authorization: Bearer TOKEN` |
| Logout | `POST /api/auth/logout.php` → invalida el token en BD |
| Expiración | 8 horas por defecto |

---

## 📡 Endpoints Completos

| Método | Endpoint | Auth |
|--------|----------|------|
| POST | `/api/auth/login.php` | No |
| POST | `/api/auth/registrar.php` | No |
| GET | `/api/auth/perfil.php` | ✅ |
| POST | `/api/auth/editar.php` | ✅ |
| POST | `/api/auth/logout.php` | ✅ |
| GET | `/api/contactos/index.php` | ✅ |
| GET | `/api/contactos/detalle.php?id=N` | ✅ |
| POST | `/api/contactos/crear.php` | ✅ |
| POST | `/api/contactos/actualizar.php` | ✅ |
| POST | `/api/contactos/eliminar.php` | ✅ |

---

## 🛣️ Rutas del Front-End

| Ruta | Vista | Protegida |
|------|-------|-----------|
| `/` | HomeView | No |
| `/login` | LoginView | Solo guests |
| `/registro` | RegistroView | Solo guests |
| `/agenda` | AgendaView | ✅ |
| `/agenda/crear` | CrearContactoView | ✅ |
| `/agenda/:id` | EditarContactoView | ✅ |
| `/perfil` | PerfilView | ✅ |

---

## ⚠️ Checklist Final antes de la Exposición

- [ ] `database.sql` ejecutado y tablas creadas
- [ ] Credenciales de BD actualizadas en `database.php`
- [ ] Carpetas `uploads/` con permisos de escritura
- [ ] `config.json` apuntando a la URL correcta del backend
- [ ] Frontend publicado en GitHub Pages
- [ ] Backend publicado en hosting PHP
- [ ] Prueba de interoperabilidad con `config.json` de otro equipo
- [ ] Reporte PDF entregado con capturas reales del sistema funcionando

---

*TecNM Campus Tuxtla Gutiérrez — Programación Web — S5A — 2026*
