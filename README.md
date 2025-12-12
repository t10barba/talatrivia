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

### 2. Levantar los Servicios con Docker Compose
```bash
docker compose up -d --build
```

Esto iniciará:
- **API REST**: http://localhost:8080
- **Panel Admin**: http://localhost:8081/admin
- **Frontend**: http://localhost:3000
- **MySQL**: Puerto 3306 (interno)

### 3. Ejecutar Migraciones y Cargar Fixtures
```bash
# Crear estructura de base de datos
docker compose exec api bin/console doctrine:migrations:migrate --no-interaction

# Cargar datos de prueba
docker compose exec api bin/console hautelook:fixtures:load --no-interaction
```

### 4. Acceder a las Aplicaciones

#### Frontend (Juego)
```
http://localhost:3000
```

Para jugar una trivia específica:
```
http://localhost:3000/trivia/{slug}/{codigo}
```

Ejemplo con fixtures:
```
http://localhost:3000/trivia/rrhh-2025/TEST01
```

#### Ranking de la Trivia
```
http://localhost:3000/trivia/{slug}/ranking
```

Ejemplo con fixtures:
```
http://localhost:3000/trivia/rrhh-2025/ranking
```
Muestra el leaderboard con todos los usuarios que completaron la trivia, ordenados por puntaje y tiempo.

#### Panel de Administración
```
http://localhost:8081/admin
```
Gestiona trivias, preguntas, usuarios y resultados mediante interfaz EasyAdmin.

#### Documentación de la API (Swagger)
```
http://localhost:8080/api/doc
```

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
  "pregunta_id": 1,
  "titulo": "¿Qué es el onboarding en recursos humanos?",
  "respuestas": [
    {"id": 1, "texto": "Proceso de integración de nuevos empleados"},
    {"id": 2, "texto": "Programa de capacitación continua"},
    {"id": 3, "texto": "Evaluación anual de desempeño"},
    {"id": 4, "texto": "Sistema de gestión de nómina"}
  ],
  "numero_pregunta": 1,
  "total_preguntas": 10
}
```

**Respuesta cuando ya terminó:**
```json
{
  "mensaje": "Ya has respondido todas las preguntas de esta trivia",
  "completada": true
}
```

#### 2. Enviar Respuesta
```http
POST /api/juego/{slug}/respuesta/{codigo}
```

**Body:**
```json
{
  "pregunta_id": 1,
  "respuesta_id": 2
}
```

**Respuesta Exitosa (200):**
```json
{
  "correcto": true,
  "respuesta_correcta_id": 2,
  "completada": false
}
```

#### 3. Obtener Puntaje Final
```http
GET /api/juego/{slug}/puntaje/{codigo}
```

**Respuesta Exitosa (200):**
```json
{
  "puntaje": 18,
  "total_preguntas": 10,
  "respuestas_correctas": 10,
  "trivia": "Trivia de Recursos Humanos",
  "tiempo_segundos": 600
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
[
  {
    "usuario_id": 3,
    "nombre": "Carlos",
    "apellidos": "Manzana",
    "puntaje": 18,
    "tiempo_segundos": 600,
    "completada_en": "2025-12-12T15:30:45+00:00"
  },
  {
    "usuario_id": 4,
    "nombre": "Ana",
    "apellidos": "Kiwi",
    "puntaje": 11,
    "tiempo_segundos": 720,
    "completada_en": "2025-12-12T16:10:22+00:00"
  }
]
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
# Opción 1: Resetear todo
docker compose exec api bin/console doctrine:database:drop --force
docker compose exec api bin/console doctrine:database:create
docker compose exec api bin/console doctrine:migrations:migrate --no-interaction
docker compose exec api bin/console hautelook:fixtures:load --no-interaction

# Opción 2: Solo recargar fixtures
docker compose exec api bin/console doctrine:fixtures:load --no-interaction
```

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
