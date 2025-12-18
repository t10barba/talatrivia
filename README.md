# Plataforma de Trivia Empresarial

Sistema completo de trivia interactiva construido con Symfony 7.4 (Backend/API) y Next.js 16 (Frontend), diseñado para capacitación y evaluación de empleados mediante cuestionarios personalizados.

## 🎯 Características Principales

- ✅ **Sistema de Trivia Dinámico**: Preguntas con múltiples respuestas y retroalimentación en tiempo real
- ✅ **Control de Acceso por Código**: Cada usuario accede mediante código único generado por trivia
- ✅ **Gestión de Estado Completa**: Backend controla el progreso y evita respuestas duplicadas
- ✅ **Ranking Competitivo**: Leaderboard ordenado por puntaje y tiempo de finalización
- ✅ **Panel de Administración**: Gestión completa de trivias, preguntas y usuarios con EasyAdmin
- ✅ **API RESTful Documentada**: Endpoints con OpenAPI/Swagger integrado
- ✅ **Animaciones y Feedback Visual**: Confetti, rebotes y efectos para mejor UX
- ✅ **Fixtures para Testing**: Datos de prueba precargados con 4 escenarios diferentes

## 🏗️ Arquitectura

El proyecto consta de **tres aplicaciones independientes** orquestadas con Docker Compose:

```
talatrivia/
│
├── api/                          # API REST (Puerto 8080)
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── JuegoController.php        # Endpoints del juego (pregunta, respuesta, puntaje, ranking)
│   │   │   ├── PreguntaController.php     # CRUD de preguntas
│   │   │   ├── TriviaController.php       # CRUD de trivias
│   │   │   └── UsuarioController.php      # CRUD de usuarios
│   │   ├── Entity/
│   │   │   ├── Pregunta.php
│   │   │   ├── PreguntaTrivia.php
│   │   │   ├── Respuesta.php
│   │   │   ├── RespuestaUsuario.php
│   │   │   ├── Trivia.php
│   │   │   └── Usuario.php
│   │   └── Repository/
│   ├── migrations/                        # Migraciones de base de datos
│   ├── fixtures/                          # Datos de prueba (Alice/Faker)
│   ├── config/                            # Configuración Symfony
│   ├── Dockerfile
│   └── composer.json
│
├── backend/                      # Panel Admin (Puerto 8081)
│   ├── src/
│   │   ├── Controller/
│   │   └── Service/
│   ├── templates/
│   │   ├── admin/                         # Vistas EasyAdmin
│   │   └── base.html.twig
│   ├── config/
│   ├── Dockerfile
│   └── composer.json
│
├── frontend/                     # SPA Next.js (Puerto 3000)
│   ├── app/
│   │   ├── trivia/
│   │   │   └── [slug]/
│   │   │       ├── [codigo]/
│   │   │       │   └── page.tsx           # Juego principal
│   │   │       └── ranking/
│   │   │           └── page.tsx           # Leaderboard
│   │   ├── components/
│   │   │   ├── QuestionCard.jsx
│   │   │   ├── AnswerButton.jsx
│   │   │   ├── FeedbackScreen.jsx
│   │   │   └── ScoreDisplay.jsx
│   │   ├── services/
│   │   │   └── triviaApi.js               # Cliente API (Axios)
│   │   ├── layout.tsx
│   │   ├── page.tsx
│   │   └── globals.css
│   ├── Dockerfile
│   ├── next.config.ts
│   └── package.json
│
├── docker-compose.yml            # Orquestación de servicios
└── docs/                         # Documentación técnica

```

## 🛠️ Stack Tecnológico

### Backend API (Symfony 7.4)
- **PHP**: 8.2+
- **Framework**: Symfony 7.4.*
- **ORM**: Doctrine ORM 3.5
- **Migraciones**: Doctrine Migrations Bundle 3.7
- **Fixtures**: Hautelook Alice Bundle 2.16 (nelmio/alice)
- **API Doc**: NelmioApiDocBundle 5.8 (OpenAPI/Swagger)
- **CORS**: NelmioCorsBundle 2.6
- **Base de Datos**: MySQL 8.0

### Backend Admin (Symfony 7.4)
- **Framework**: Symfony 7.4.*
- **Admin Panel**: EasyCorp EasyAdmin Bundle 4.27
- **ORM**: Doctrine ORM (compartido con API)
- **Template Engine**: Twig 3.x

### Frontend (Next.js 16)
- **Framework**: Next.js 16.0.8 (App Router)
- **UI Library**: React 19.2.1
- **Styling**: Tailwind CSS v4
- **HTTP Client**: Axios 1.7.x
- **Iconos**: Font Awesome Free 6.7.x
- **Animaciones**: react-confetti 6.1.x, CSS Keyframes

### DevOps
- **Containerización**: Docker + Docker Compose
- **Servidor Web**: Nginx 1.21
- **PHP**: PHP-FPM 8.2
- **Gestor de Contenedores**: Rancher Desktop (macOS)

## � Arquitectura de Contenedores

El proyecto utiliza una **arquitectura de múltiples contenedores** donde cada servicio tiene responsabilidades específicas:

### ¿Por qué contenedores separados para Nginx y PHP-FPM?

Tanto la **API** como el **Backend Admin** utilizan dos contenedores independientes:

1. **Contenedor PHP-FPM** (`api`, `backend`)
   - Ejecuta el código PHP de Symfony
   - Procesa la lógica de negocio
   - Se comunica mediante FastCGI protocol (puerto 9000)

2. **Contenedor Nginx** (`api_nginx`, `backend_nginx`)
   - Actúa como servidor web y proxy reverso
   - Maneja peticiones HTTP/HTTPS (puertos 8080, 8081)
   - Sirve archivos estáticos directamente
   - Envía peticiones PHP a PHP-FPM mediante FastCGI

#### Ventajas de esta arquitectura:

✅ **Separación de responsabilidades**: Nginx maneja HTTP, PHP-FPM ejecuta código  
✅ **Mejor rendimiento**: Nginx optimizado para servir archivos estáticos  
✅ **Escalabilidad independiente**: Se pueden escalar Nginx y PHP-FPM por separado  
✅ **Seguridad**: PHP-FPM no expuesto directamente al exterior  
✅ **Mantenimiento**: Actualizaciones independientes de cada componente  
✅ **Estándar de la industria**: Patrón ampliamente usado en producción

#### Flujo de una petición:

```
Cliente → Nginx (8080/8081) → PHP-FPM (9000) → Symfony → Base de Datos
         ↑                     ↑
         |                     |
    Archivos estáticos    Procesamiento PHP
```

## �🚀 Instalación y Configuración

### Prerrequisitos
- Docker Desktop o Rancher Desktop
- Git

### 1. Clonar el Repositorio
```bash
git clone https://github.com/t10barba/talatrivia.git
cd talatrivia
```

### 2. Configurar Variables de Entorno
```bash
# Copiar archivos de ejemplo para API y Backend
cp api/.env.example api/.env
cp backend/.env.example backend/.env

# Opcional: Personalizar credenciales en .env si es necesario
# Los valores por defecto están listos para Docker Compose
```

### 3. Levantar los Servicios con Docker Compose
```bash
docker compose up -d --build
```

Esto iniciará:
- **API REST**: http://localhost:8080
- **Panel Admin**: http://localhost:8081/admin
- **Frontend**: http://localhost:3000
- **MySQL**: Puerto 3306 (interno)

### 4. Ejecutar Migraciones y Cargar Fixtures
```bash
# Crear estructura de base de datos
docker compose exec api bin/console doctrine:migrations:migrate --no-interaction

# Cargar datos de prueba
docker compose exec api bin/console hautelook:fixtures:load --no-interaction
```

### 5. Acceder a las Aplicaciones

#### Frontend (Juego)
```
http://localhost:3000
```

Para jugar una trivia específica:
```
http://localhost:3000/trivia/{id}
```

---

## 🔥 Hot Reload en Desarrollo

El proyecto está configurado para **hot reload automático** en Next.js cuando trabajas en Docker.

### ✅ Compatibilidad:
- ✅ **macOS** (Docker Desktop / Rancher Desktop) 
- ✅ **Windows** (Docker Desktop / WSL2)
- ✅ **Linux** (Docker Engine nativo)

### ⚙️ Configuración Actual

El hot reload utiliza **webpack con file polling** en lugar de Turbopack, optimizado para compatibilidad máxima con volúmenes de Docker:

**En `frontend/package.json`:**
```json
"dev": "next dev --webpack -H 0.0.0.0"
```

**En `frontend/next.config.ts`:**
```typescript
webpack: (config, { dev }) => {
  if (dev) {
    config.watchOptions = {
      poll: 1000,           // Revisa cambios cada 1 segundo
      aggregateTimeout: 300 // Espera 300ms antes de recompilar
    };
  }
  return config;
}
```

**En `docker-compose.yml`:**
```yaml
environment:
  WATCHPACK_POLLING: "true"
  CHOKIDAR_USEPOLLING: "true"
```

### Cómo Funciona:

1. **Edita cualquier archivo** en `frontend/app/` o `frontend/components/`
2. **Guarda el archivo** (Cmd+S / Ctrl+S)
3. **Espera ~1-2 segundos** - El sistema detecta el cambio
4. **La recompilación toma ~5-7 segundos** en la primera carga de una página
5. **Cambios subsecuentes en archivos ya compilados: ~1-2 segundos**
6. **El navegador se recarga automáticamente**

### 🔍 Verificar que Funciona:

```bash
# Ver logs del frontend en tiempo real
docker compose logs -f frontend

# Al refrescar el navegador después de editar, verás:
#  ○ Compiling /trivia/[slug]/[codigo] ...
#  GET /trivia/rrhh-2025/TEST01 200 in 1.2s (compile: 950ms, render: 250ms)
```

**Nota**: Webpack **no muestra un mensaje explícito** cuando detecta cambios en archivos. Los cambios se aplican silenciosamente y se compilan cuando refrescas el navegador o cuando el Fast Refresh lo hace automáticamente.

### ⚠️ Si No Funciona el Hot Reload:

#### Opción 1: Reiniciar el contenedor
```bash
docker compose restart frontend
```

#### Opción 2: Rebuild completo del contenedor
```bash
docker compose down
docker compose build frontend
docker compose up -d
```

#### Opción 3: Limpiar caché de Next.js
```bash
docker compose exec frontend rm -rf .next
docker compose restart frontend
```

#### Opción 4: Hard refresh del navegador
```
Cmd + Shift + R (macOS)
Ctrl + Shift + R (Windows/Linux)
```

### 🐧 Optimización para Linux Nativo

Si usas **Docker Engine en Linux nativo** (no Docker Desktop, Rancher Desktop o WSL2), el sistema de archivos puede usar **inotify** directamente, que es más eficiente que polling.

Puedes crear un override para desactivar polling:

```bash
# Crear docker-compose.override.yml para Linux nativo
cat > docker-compose.override.yml << 'EOF'
services:
  frontend:
    environment:
      WATCHPACK_POLLING: "false"
      CHOKIDAR_USEPOLLING: "false"
EOF
```

Luego inicia normalmente:
```bash
docker compose up -d
```

**Nota**: Esta optimización **solo** funciona en Linux nativo. En macOS/Windows (incluso con WSL2), el polling es necesario porque los eventos del filesystem no se propagan correctamente desde el host → VM → contenedor.

### 📊 Detalles Técnicos

#### ¿Por qué webpack en lugar de Turbopack?

Aunque **Turbopack es más rápido** (10x en algunos casos), actualmente tiene problemas de compatibilidad con file polling en volúmenes de Docker en macOS/Windows. Por eso usamos **webpack con configuración optimizada**:

| Característica | Turbopack | Webpack (actual) |
|----------------|-----------|------------------|
| Velocidad de compilación | ⚡ Muy rápida | ⏱️ Moderada (5-7s inicial) |
| Hot reload en Docker | ❌ Inconsistente | ✅ Funciona siempre |
| Compatibilidad | ⚠️ Requiere polling especial | ✅ Polling estándar |
| Recompilaciones | ⚡ <1s | ⏱️ 1-2s |

#### ¿Por qué es necesario el polling?

En **macOS y Windows**, Docker corre dentro de una VM (máquina virtual). Los eventos del filesystem (como `inotify` en Linux o `FSEvents` en macOS) **no atraviesan** la capa de virtualización:

```
Host (tu Mac/Windows)
    ↓ (volumen montado)
VM de Docker (Linux)
    ↓ (sin eventos de filesystem)
Contenedor de Next.js
```

El **polling** resuelve esto revisando activamente los archivos cada segundo:
- ✅ **Ventaja**: Funciona en todos los sistemas operativos y configuraciones
- ⚠️ **Desventaja**: Consume más CPU y es más lento que eventos nativos

#### Variables de entorno:

- `WATCHPACK_POLLING="true"`: Activa polling en webpack
- `CHOKIDAR_USEPOLLING="true"`: Activa polling en el watcher de archivos (Chokidar es la librería que usa webpack internamente para detectar cambios)

#### Volúmenes optimizados:

El `docker-compose.yml` usa el flag `:cached` en el volumen del frontend:

```yaml
volumes:
  - ./frontend:/app:cached
```

Esto mejora el rendimiento en macOS/Windows al permitir que la escritura en el volumen sea asíncrona.

### 🎯 Recomendaciones

**Para desarrollo óptimo:**

1. ✅ **Usa el polling actual** - Es la configuración más confiable
2. ✅ **Ten paciencia con la primera compilación** (~5-7s)
3. ✅ **Los cambios subsecuentes serán más rápidos** (~1-2s)
4. ⚠️ **Evita editar múltiples archivos simultáneamente** - Espera que se complete cada recompilación
5. ✅ **Si trabajas en Linux nativo, usa el override** para mejor performance

**Nota**: Si necesitas velocidad máxima de desarrollo, considera correr Next.js directamente en tu host (sin Docker) con `npm run dev`. El hot reload será instantáneo, pero perderás el entorno reproducible de Docker.

---

#### API REST
```
http://localhost:8080/api/doc
```
Documentación completa de endpoints con Swagger UI.

#### Panel de Administración
```
http://localhost:8081/admin
```
Gestiona trivias, preguntas, usuarios y resultados mediante interfaz EasyAdmin.

## 📋 Datos de Prueba (Fixtures)

El sistema incluye **1 trivia de ejemplo** con **4 usuarios en diferentes estados**:

### Trivia: Recursos Humanos 2025
- **Slug**: `rrhh-2025`
- **Nombre**: Trivia de Recursos Humanos
- **Descripción**: Pon a prueba tus conocimientos sobre gestión de recursos humanos y buenas prácticas laborales
- **Preguntas**: 10 preguntas de diferentes niveles de dificultad

#### Usuarios de prueba:

1. **TEST01** - Daniela Sandía
   - **Estado**: Sin iniciar (estado 0)
   - **Progreso**: 0/10 preguntas
   - **URL**: http://localhost:3000/trivia/rrhh-2025/TEST01

2. **TEST02** - José Limón  
   - **Estado**: En progreso (estado 1)
   - **Progreso**: Ha respondido algunas preguntas
   - **URL**: http://localhost:3000/trivia/rrhh-2025/TEST02

3. **TEST03** - Carlos Manzana
   - **Estado**: Finalizado (estado 2)
   - **Puntaje**: 18 puntos (todas correctas)
   - **URL**: http://localhost:3000/trivia/rrhh-2025/TEST03

4. **TEST04** - Ana Kiwi
   - **Estado**: Finalizado (estado 2)
   - **Puntaje**: 11 puntos (respuestas mixtas)
   - **URL**: http://localhost:3000/trivia/rrhh-2025/TEST04

## 🎮 API Endpoints

### Base URL
```
http://localhost:8080
```

### Juego

#### 1. Obtener Siguiente Pregunta
```http
GET /api/juego/{slug}/pregunta/{codigo}
```

**Parámetros:**
- `slug`: Slug de la trivia (ej: "rrhh-2025")
- `codigo`: Código único del usuario (ej: "TEST01")

**Respuesta Exitosa (200):**
```json
{
  "preguntaId": 1,
  "texto": "¿Qué es el onboarding en recursos humanos?",
  "respuestas": [
    {"id": 1, "texto": "Proceso de integración de nuevos empleados"},
    {"id": 2, "texto": "Programa de capacitación continua"},
    {"id": 3, "texto": "Evaluación anual de desempeño"},
    {"id": 4, "texto": "Sistema de gestión de nómina"}
  ],
  "numeroPregunta": 1,
  "totalPreguntas": 10,
  "nombreTrivia": "Trivia de Recursos Humanos",
  "nombreUsuario": "Daniela"
}
```

**Respuesta cuando ya terminó:**
```json
{
  "finalizado": true,
  "nombreUsuario": "Daniela",
  "nombreTrivia": "Trivia de Recursos Humanos"
}
```

#### 2. Enviar Respuesta
```http
POST /api/juego/{slug}/respuesta/{codigo}
```

**Body:**
```json
{
  "respuestaId": 1
}
```

**Respuesta Exitosa (200):**
```json
{
  "correcto": true,
  "puntajeObtenido": 2,
  "finalizado": false
}
```

#### 3. Obtener Puntaje Final
```http
GET /api/juego/{slug}/puntaje/{codigo}
```

**Respuesta Exitosa (200):**
```json
{
  "puntajeTotal": 18,
  "tiempoTranscurrido": "10:00",
  "nombreTrivia": "Trivia de Recursos Humanos",
  "nombreUsuario": "Carlos"
}
```

#### 4. Obtener Ranking
```http
GET /api/juego/{slug}/ranking?limit=50
```

**Parámetros Query:**
- `limit` (opcional): Número máximo de resultados (default: 50)

**Respuesta Exitosa (200):**
```json
{
  "nombreTrivia": "Trivia de Recursos Humanos",
  "ranking": [
    {
      "posicion": 1,
      "nombreUsuario": "Carlos",
      "puntajeTotal": 18,
      "tiempoTranscurrido": "10:00",
      "finishedAt": "2025-12-12T15:30:45+00:00"
    },
    {
      "posicion": 2,
      "nombreUsuario": "Ana",
      "puntajeTotal": 11,
      "tiempoTranscurrido": "12:00",
      "finishedAt": "2025-12-12T16:10:22+00:00"
    }
  ]
}
```

### Códigos de Error Comunes

```json
// Trivia no encontrada o inactiva (404)
{
  "error": "Trivia no encontrada o inactiva"
}

// Usuario no encontrado (404)
{
  "error": "Usuario no encontrado o no autorizado para esta trivia"
}

// Respuesta duplicada (400)
{
  "error": "Ya has respondido esta pregunta"
}

// Pregunta no pertenece a la trivia (400)
{
  "error": "La pregunta no pertenece a esta trivia"
}
```

## 🧪 Testing con Postman

Importa la colección incluida en `/api/postman_collection.json`:

- **Juego - Flujo completo**: 6 requests con tests automatizados
  1. Obtener primera pregunta
  2. Enviar respuesta correcta
  3. Obtener segunda pregunta
  4. Obtener puntaje
  5. Obtener ranking (nuevo)
  6. Intentar obtener pregunta después de terminar

- **Validaciones y Errores**: 6 escenarios de error
- **Quick Tests**: 5 casos de prueba rápidos

## 📂 Gestión de Migraciones

### Crear nueva migración
```bash
docker compose exec api bin/console make:migration
```

### Aplicar migraciones
```bash
docker compose exec api bin/console doctrine:migrations:migrate
```

### Ver estado de migraciones
```bash
docker compose exec api bin/console doctrine:migrations:status
```

## 🔄 Resetear Base de Datos

Para empezar desde cero (⚠️ **elimina todos los datos**):

```bash
# Opción 1: Resetear todo (método recomendado para Docker)
docker compose exec api bin/console doctrine:database:drop --force
docker compose exec database mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS talatrivia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
docker compose exec api bin/console doctrine:migrations:migrate --no-interaction
docker compose exec api bin/console hautelook:fixtures:load --no-interaction

# Opción 2: Solo recargar fixtures (si solo necesitas datos frescos)
docker compose exec api bin/console hautelook:fixtures:load --no-interaction
```

> **Nota**: El comando `doctrine:database:create` de Symfony no funciona correctamente en este entorno Docker porque requiere una conexión existente. Por eso se usa MySQL directamente para crear la base de datos.

## 🎨 Características del Frontend

### Animaciones Personalizadas
- **Bounce Slow**: Rebote vertical suave (respuesta correcta)
- **Shake Horizontal**: Sacudida lateral (respuesta incorrecta)
- **Confetti**: Celebración con confeti al finalizar

### Flujo de Usuario
1. Selecciona una respuesta (resaltado visual)
2. Click en "Enviar Respuesta"
3. Feedback inmediato (2 segundos con animación)
4. Carga automática de siguiente pregunta
5. Pantalla final con puntaje y confeti

### Responsive Design
- Mobile-first con Tailwind CSS
- Adaptable a tablets y desktop
- Animaciones optimizadas para todos los dispositivos

## 📁 Estructura de Entidades

```
Trivia (1) ←→ (N) PreguntaTrivia (N) ←→ (1) Pregunta
  ↓                                              ↓
Usuario (1) ←→ (N) RespuestaUsuario (N) ←→ (1) Respuesta
```

### Relaciones Clave
- **Trivia → Usuario**: Cada trivia genera códigos únicos para múltiples usuarios
- **PreguntaTrivia**: Tabla pivote con orden personalizado de preguntas por trivia
- **RespuestaUsuario**: Guarda progreso completo (respuesta, tiempo, puntaje)

## 🔐 Seguridad

- **Validación de Código**: El backend valida que el código pertenezca a la trivia
- **Control de Duplicados**: No se pueden responder preguntas dos veces
- **Estado en Backend**: Todo el progreso se mantiene en el servidor
- **CORS Configurado**: Solo permite orígenes autorizados

## 🐳 Comandos Docker Útiles

```bash
# Ver logs en tiempo real
docker compose logs -f

# Reiniciar servicios
docker compose restart

# Detener servicios
docker compose down

# Eliminar volúmenes (⚠️ borra la BD)
docker compose down -v

# Acceder a contenedor específico
docker compose exec api bash
docker compose exec frontend sh
```

## 📊 Monitoring

### Ver estado de servicios
```bash
docker compose ps
```

### Acceder a MySQL directamente
```bash
docker compose exec database mysql -u root -proot trivia_db
```

## 🚧 TODO / Mejoras Futuras

- [ ] **Autenticación JWT**: Reemplazar códigos simples por tokens
- [ ] **Límite de Tiempo por Pregunta**: Cronómetro visual con timeout
- [ ] **Categorías de Preguntas**: Filtrado y agrupación avanzada
- [ ] **Reportes en PDF**: Exportar resultados individuales y grupales
- [ ] **Modo Multijugador**: Trivias simultáneas con ranking en vivo
- [ ] **Notificaciones**: Alertas cuando se asigne una nueva trivia
- [ ] **Multimedia**: Soporte para imágenes/videos en preguntas
- [ ] **Analíticas**: Dashboard con estadísticas de desempeño
- [ ] **Tests Unitarios**: PHPUnit para backend, Jest/RTL para frontend
- [ ] **CI/CD**: Pipeline automatizado con GitHub Actions
- [ ] **i18n**: Soporte multiidioma (ES, EN, PT)
- [ ] **Temas Personalizables**: Dark mode y colores corporativos

## 📝 Notas de Desarrollo

- **Backend mantiene todo el estado**: No se usa localStorage en frontend
- **Respuestas mezcladas**: El backend aleatoriza el orden cada vez
- **Botones dinámicos**: Genera automáticamente A, B, C, D... según cantidad
- **Fixtures con Alice**: Usa Faker para datos realistas y variados
- **Migraciones versionadas**: Doctrine Migrations con historial completo

## 🤝 Contribución

Este proyecto es una prueba técnica. Para preguntas o sugerencias, contactar al autor.

## 📄 Licencia

Proyecto desarrollado como ejercicio técnico para evaluación.

---

**Desarrollado con ❤️ usando Symfony 7.4, Next.js 16 y Docker**
