# Frontend - Next.js Trivia App

Aplicación Next.js 16 con App Router para el sistema de trivia interactiva. Soporta múltiples modos de desarrollo para adaptarse a diferentes flujos de trabajo.

---

## 📋 Requisitos Previos

### Para todos los modos
- **Node.js**: 18.x o superior
- **npm**: 9.x o superior

### Para modos con Docker
- **Docker Desktop** (Windows/macOS) o **Rancher Desktop** (recomendado para macOS)
- **Docker Compose**: v2.x

### Verificar instalación
```bash
node --version   # Debe ser v18 o superior
npm --version    # Debe ser v9 o superior
docker --version # Debe ser v20 o superior
docker compose version # Debe ser v2 o superior
```

---

## 🚀 Instalación Inicial

### 1. Instalar dependencias del frontend
```bash
# Desde la raíz del proyecto
npm run install:frontend

# O directamente en el directorio frontend
cd frontend
npm install
```

### 2. Configurar variables de entorno
```bash
# Copiar el archivo de ejemplo
cp .env.example .env.local

# Editar según tu modo de desarrollo (ver secciones siguientes)
```

El archivo `.env.local` ya viene con valores por defecto para desarrollo local.

---

## 🎯 Modos de Desarrollo

Elige el modo que mejor se adapte a tus necesidades y recursos disponibles.

---

## 🐳 Modo 1: Docker Completo (Recomendado)

**Cuándo usarlo:**
- ✅ Configuración inicial / Primera vez
- ✅ Desarrollo en equipo (entorno reproducible)
- ✅ Cuando necesitas todo el stack funcionando
- ✅ Testing de integración

### Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                    TU COMPUTADORA                        │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │              DOCKER CONTAINERS                      │ │
│  │                                                     │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐ │ │
│  │  │   Frontend   │  │   API REST   │  │  MySQL   │ │ │
│  │  │  (Next.js)   │◄─┤  (Symfony)   │◄─┤    DB    │ │ │
│  │  │  Port 3000   │  │  Port 8080   │  │Port 3306 │ │ │
│  │  └──────────────┘  └──────────────┘  └──────────┘ │ │
│  │         ▲                                           │ │
│  │         │ Hot Reload (polling)                     │ │
│  │         │                                           │ │
│  └─────────┼───────────────────────────────────────────┘ │
│            │                                             │
│     ┌──────▼────────┐                                    │
│     │  Editas en    │                                    │
│     │  VS Code      │                                    │
│     └───────────────┘                                    │
└─────────────────────────────────────────────────────────┘
```

### Inicializar todo el proyecto

```bash
# Desde la raíz del proyecto
npm run setup:full
```

Este comando:
1. Construye e inicia todos los contenedores Docker
2. Ejecuta migraciones de base de datos
3. Carga datos de prueba (fixtures)

### Comandos útiles

```bash
# Iniciar todos los servicios
npm run docker:up

# Detener todos los servicios
npm run docker:down

# Ver logs de todos los servicios
npm run docker:logs

# Ver logs solo del frontend
npm run docker:logs:frontend

# Reiniciar servicios
npm run docker:restart

# Limpiar todo (elimina volúmenes)
npm run docker:clean
```

### Acceder a la aplicación

- **Frontend**: http://localhost:3000
- **API REST**: http://localhost:8080
- **Panel Admin**: http://localhost:8081/admin

### Hot Reload

El hot reload está configurado con **file polling** para funcionar correctamente en Docker:

1. Edita cualquier archivo en `frontend/app/` o `frontend/components/`
2. Guarda el archivo (Cmd+S / Ctrl+S)
3. Espera ~1-2 segundos para detección
4. La recompilación toma ~5-7 segundos (primera vez)
5. El navegador se recarga automáticamente

**Nota**: Los cambios subsecuentes son más rápidos (~1-2s).

---

## 💻 Modo 2: Desarrollo Híbrido (Local + Docker)

**Cuándo usarlo:**
- ✅ Desarrollo activo en frontend
- ✅ Hot reload más rápido (~instantáneo)
- ✅ Menor consumo de recursos
- ✅ Debugging más sencillo

### Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                    TU COMPUTADORA                        │
│                                                          │
│  ┌──────────────┐    ┌────────────────────────────────┐ │
│  │   Frontend   │    │      DOCKER CONTAINERS         │ │
│  │  (Next.js)   │    │                                │ │
│  │ Local/Native │◄───┤  ┌──────────────┐  ┌────────┐ │ │
│  │  Port 3000   │    │  │   API REST   │  │ MySQL  │ │ │
│  │              │    │  │  (Symfony)   │◄─┤   DB   │ │ │
│  └──────────────┘    │  │  Port 8080   │  │Port 3306│ │
│         ▲            │  └──────────────┘  └────────┘ │ │
│         │            └────────────────────────────────┘ │
│         │ Hot Reload (instantáneo)                      │
│         │                                               │
│  ┌──────▼────────┐                                      │
│  │  Editas en    │                                      │
│  │  VS Code      │                                      │
│  └───────────────┘                                      │
└─────────────────────────────────────────────────────────┘
```

### Paso 1: Configurar variables de entorno

Asegúrate de que `.env.local` contenga:

```env
NEXT_PUBLIC_API_URL=http://localhost:8080
NEXT_PUBLIC_FEEDBACK_DELAY=2000
```

### Paso 2: Iniciar servicios backend en Docker

```bash
# Desde la raíz del proyecto
npm run docker:backend

# O desde el directorio frontend
cd frontend
npm run docker:backend
```

Este comando inicia **solo** los servicios necesarios:
- MySQL (database)
- API Symfony (api)
- Nginx para API (api_nginx)

### Paso 3: Ejecutar frontend localmente

```bash
# Desde el directorio frontend
cd frontend
npm run dev

# O desde la raíz del proyecto
npm run dev:frontend
```

### Acceder a la aplicación

- **Frontend**: http://localhost:3000 (local)
- **API REST**: http://localhost:8080 (Docker)

### Detener servicios backend

```bash
# Desde la raíz
npm run docker:backend:down

# O desde frontend
cd frontend
npm run docker:backend:down
```

### Ventajas de este modo

✅ **Hot Reload instantáneo** - Usa el sistema de archivos nativo  
✅ **Menos recursos** - Solo 3 contenedores vs 6  
✅ **Debugging fácil** - VS Code debugging nativo  
✅ **Compilación más rápida** - Sin overhead de Docker  

### Desventajas

⚠️ **Requiere Node.js local** - Debe coincidir con versión de Docker  
⚠️ **Gestión manual** - Debes iniciar/detener servicios por separado  

---

## 🎭 Modo 3: Con Mock API (Mockoon o similar)

**Cuándo usarlo:**
- ✅ Desarrollo de UI sin backend
- ✅ Testing de componentes visuales
- ✅ Diseño de interfaces
- ✅ Sin Docker disponible (recursos limitados)

### Arquitectura

```
┌─────────────────────────────────────────────────────────┐
│                    TU COMPUTADORA                        │
│                                                          │
│  ┌──────────────┐         ┌──────────────┐             │
│  │   Frontend   │         │   Mock API   │             │
│  │  (Next.js)   │◄────────┤  (Mockoon)   │             │
│  │  Port 3000   │         │  Port 3001   │             │
│  │              │         │              │             │
│  └──────────────┘         └──────────────┘             │
│         ▲                                               │
│         │ Hot Reload (instantáneo)                      │
│         │                                               │
│  ┌──────▼────────┐                                      │
│  │  Editas en    │                                      │
│  │  VS Code      │                                      │
│  └───────────────┘                                      │
└─────────────────────────────────────────────────────────┘
```

### Paso 1: Configurar variables de entorno

Edita `.env.local` para apuntar a tu mock API:

```env
# Para Mockoon (puerto por defecto 3001)
NEXT_PUBLIC_API_URL=http://localhost:3001

# O cualquier otro puerto que uses
# NEXT_PUBLIC_API_URL=http://localhost:8000

NEXT_PUBLIC_FEEDBACK_DELAY=2000
```

### Paso 2: Configurar Mockoon

#### Opción A: Importar configuración lista (Recomendado)

1. Abre Mockoon Desktop
2. Click en **"Open environment"** o **"Import"**
3. Selecciona el archivo `mockoon-environment.json` desde la carpeta `docs/` del proyecto
4. Verifica que esté corriendo en puerto `3001`
5. Click en el botón **"Start server"** (play ▶️)

El archivo incluye:
- ✅ 4 endpoints completos de la API
- ✅ Datos de prueba de los usuarios TEST01-TEST04
- ✅ Respuestas basadas en fixtures reales
- ✅ CORS configurado correctamente
- ✅ Reglas dinámicas según el código de usuario

#### Opción B: Configuración manual

1. Abre Mockoon
2. Crea un nuevo entorno en puerto `3001`
3. Define los endpoints manualmente (ver sección siguiente)

### Endpoints a mockear

```
GET  /api/juego/{slug}/pregunta/{codigo}
POST /api/juego/{slug}/respuesta/{codigo}
GET  /api/juego/{slug}/puntaje/{codigo}
GET  /api/juego/{slug}/ranking
```

#### Ejemplo: Mock de pregunta

**Endpoint**: `GET /api/juego/rrhh-2025/pregunta/TEST01`

**Respuesta** (200):
```json
{
  "preguntaId": 1,
  "texto": "¿Qué es el onboarding?",
  "respuestas": [
    {"id": 1, "texto": "Proceso de integración"},
    {"id": 2, "texto": "Capacitación continua"},
    {"id": 3, "texto": "Evaluación anual"},
    {"id": 4, "texto": "Gestión de nómina"}
  ],
  "numeroPregunta": 1,
  "totalPreguntas": 10,
  "nombreTrivia": "Trivia de RRHH",
  "nombreUsuario": "Test"
}
```

#### Ejemplo: Mock de respuesta

**Endpoint**: `POST /api/juego/rrhh-2025/respuesta/TEST01`

**Body**:
```json
{
  "respuestaId": 1
}
```

**Respuesta** (200):
```json
{
  "correcto": true,
  "puntajeObtenido": 2,
  "finalizado": false
}
```

### Paso 3: Ejecutar frontend localmente

```bash
cd frontend
npm run dev
```

### Usuarios mockeados disponibles

El archivo [mockoon-environment.json](../docs/mockoon-environment.json) incluye datos de los 4 usuarios de prueba:

| Código | Usuario | Estado | Comportamiento |
|--------|---------|--------|----------------|
| `TEST01` | Daniela Sandía | Sin iniciar | Muestra primera pregunta |
| `TEST02` | José Limón | En progreso | Muestra pregunta #3 |
| `TEST03` | Carlos Manzana | Finalizado | Muestra mensaje de finalizado (18 puntos) |
| `TEST04` | Ana Kiwi | Finalizado | Muestra mensaje de finalizado (11 puntos) |

**Ejemplo de prueba:**
```
http://localhost:3000/trivia/rrhh-2025/TEST01
```

### Ventajas de este modo

✅ **Sin Docker** - No requiere contenedores  
✅ **Desarrollo UI independiente** - No necesitas backend  
✅ **Control total** - Defines responses a tu medida  
✅ **Recursos mínimos** - Solo Node.js  
✅ **Datos realistas** - Basados en fixtures reales de la BD  

### Desventajas

⚠️ **No testing real** - Los datos son ficticios  
⚠️ **Mantenimiento manual** - Debes actualizar mocks si la API cambia  
⚠️ **Sin lógica de negocio** - No valida reglas del backend  

---

## 📜 Scripts Disponibles

### Scripts desde la raíz del proyecto

```bash
# Instalación
npm run install:frontend        # Instala dependencias del frontend

# Setup completo
npm run setup                   # Inicia backend y ejecuta migraciones
npm run setup:full              # Setup completo (todos los servicios)

# Docker - Todos los servicios
npm run docker:up               # Inicia todos los contenedores
npm run docker:down             # Detiene todos los contenedores
npm run docker:restart          # Reinicia todos los contenedores
npm run docker:clean            # Elimina todo (incluye volúmenes)

# Docker - Solo backend
npm run docker:backend          # Inicia database + api + api_nginx
npm run docker:backend:down     # Detiene backend services

# Logs
npm run docker:logs             # Ver logs de todos los servicios
npm run docker:logs:frontend    # Ver logs del frontend
npm run docker:logs:api         # Ver logs de la API

# Desarrollo local
npm run dev:frontend            # Ejecuta frontend en modo local
npm run dev:api                 # Alias de docker:backend
npm run dev:all                 # Backend en Docker + Frontend local
```

### Scripts desde el directorio frontend

```bash
# Desarrollo
npm run dev                     # Servidor de desarrollo Next.js
npm run build                   # Build de producción
npm run start                   # Servidor de producción
npm run lint                    # Linter ESLint

# Docker (ejecutan comandos en la raíz)
npm run docker:backend          # Inicia servicios backend
npm run docker:backend:down     # Detiene servicios backend
npm run docker:logs             # Ver logs del frontend
npm run docker:logs:api         # Ver logs de la API
```

---

## 🛠️ Troubleshooting

### Error: "Puerto 3000 ya está en uso"

```bash
# Encuentra el proceso usando el puerto
lsof -i :3000

# O en Windows
netstat -ano | findstr :3000

# Mata el proceso o usa otro puerto
PORT=3001 npm run dev
```

### Hot reload no funciona en Docker

```bash
# Reinicia el contenedor frontend
npm run docker:restart

# O reconstruye
docker compose build frontend
npm run docker:up
```

### Hot reload lento en Docker

Esto es normal debido al file polling. Para desarrollo más rápido:
- Usa **Modo 2: Desarrollo Híbrido** (frontend local)
- El hot reload será instantáneo

### Error: "Cannot connect to API"

**Verifica que la API esté corriendo:**

```bash
# Para Docker completo
docker compose ps

# Para híbrido
docker compose ps | grep api

# Prueba el endpoint manualmente
curl http://localhost:8080/api/doc
```

**Verifica tu `.env.local`:**

```env
# Debe ser:
NEXT_PUBLIC_API_URL=http://localhost:8080
```

### Error al instalar dependencias

```bash
# Limpia caché de npm
npm cache clean --force

# Elimina node_modules y reinstala
rm -rf node_modules package-lock.json
npm install
```

### Conflicto entre node_modules local y Docker

Si usas **Modo 2 (híbrido)** y antes usaste **Modo 1 (Docker completo)**:

```bash
# Reconstruye el contenedor frontend sin volumen anónimo
docker compose down
docker compose build frontend --no-cache
docker compose up -d frontend
```

### Error: "Module not found" después de instalar paquete

```bash
# En Docker completo - reinicia el contenedor
npm run docker:restart

# En desarrollo local - solo reinicia el servidor
# Ctrl+C y luego npm run dev
```

### Base de datos sin datos de prueba

```bash
# Cargar fixtures nuevamente
docker compose exec api bin/console hautelook:fixtures:load --no-interaction

# O resetear todo desde cero
npm run docker:clean
npm run setup:full
```

---

## 🔗 Enlaces Útiles

- **Documentación Next.js**: https://nextjs.org/docs
- **Documentación React**: https://react.dev
- **Tailwind CSS**: https://tailwindcss.com/docs
- **Mockoon**: https://mockoon.com
- **Docker Docs**: https://docs.docker.com

---

## 📁 Estructura del Proyecto

```
frontend/
├── app/
│   ├── components/          # Componentes reutilizables
│   ├── services/            # Cliente API (Axios)
│   ├── trivia/
│   │   └── [slug]/
│   │       ├── [codigo]/    # Página del juego
│   │       └── ranking/     # Leaderboard
│   ├── layout.tsx           # Layout principal
│   ├── page.tsx             # Home page
│   └── globals.css          # Estilos globales
├── public/                  # Archivos estáticos
├── .env.example             # Plantilla de variables de entorno
├── .env.local               # Variables locales (no committed)
├── next.config.ts           # Configuración de Next.js
├── tailwind.config.ts       # Configuración de Tailwind
├── package.json             # Dependencias y scripts
└── README.md                # Este archivo
```

---

## 💡 Mejores Prácticas

### Desarrollo en equipo

✅ **Usa Docker completo** para mantener entornos consistentes  
✅ **Commitea `.env.example`** pero NO `.env.local`  
✅ **Documenta cambios** en variables de entorno en este README  

### Desarrollo individual

✅ **Usa modo híbrido** para desarrollo rápido de UI  
✅ **Usa mock APIs** para prototipar sin backend  
✅ **Cambia entre modos** según la tarea  

### Testing

✅ **Prueba en Docker** antes de hacer commit  
✅ **Valida con API real** antes de merges importantes  
✅ **Usa fixtures** para datos consistentes  

---

## 📝 Notas Importantes

- **Puerto 3000**: Solo un modo puede usar este puerto a la vez
- **`.env.local`**: Este archivo NO se incluye en Git (ya está en `.gitignore`)
- **Polling en Docker**: Es normal que sea más lento que desarrollo local
- **Variables `NEXT_PUBLIC_*`**: Son expuestas al navegador (no guardes secretos aquí)
- **Hot Reload**: En Docker es con polling, en local es con eventos del filesystem

---

**Desarrollado con ❤️ usando Next.js 16 y React 19**
