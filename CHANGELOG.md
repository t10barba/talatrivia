# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Versionado Semántico](https://semver.org/lang/es/).

## [1.0.0] - 2025-12-19

### Añadido
- Sistema de versionado centralizado con archivo `VERSION`
- Endpoints `/api/version` en API y Backend para consultar versión
- Visualización de versión en el footer del frontend
- Variables de entorno `APP_VERSION` en todos los servicios
- Tags de versión en imágenes Docker (`talatrivia-api:1.0.0`, etc.)
- Metadata labels en Dockerfiles con información de versión
- Documentación de proceso de versionado

### Funcionalidades Principales
- **Sistema de Trivia Empresarial**: Plataforma completa de trivias con múltiples categorías
- **API REST**: Backend Symfony con endpoints para juegos, preguntas, respuestas y estadísticas
- **Panel de Administración**: Interface EasyAdmin para gestión de contenido
- **Frontend Interactivo**: Aplicación Next.js con diseño responsivo
- **Base de Datos**: MySQL 8.0 con migraciones y fixtures
- **Dockerización**: Entorno completamente containerizado con Docker Compose


---

## Formato de Versiones

### Versiones Estables
- **MAJOR.MINOR.PATCH** (ej: `1.0.0`, `2.1.3`)
  - **MAJOR**: Cambios incompatibles con versiones anteriores (breaking changes)
  - **MINOR**: Nuevas funcionalidades compatibles con versiones anteriores
  - **PATCH**: Correcciones de bugs compatibles con versiones anteriores

### Pre-releases
Para versiones en desarrollo o prueba, se añaden sufijos:

#### Alpha (Desarrollo Temprano)
```
1.1.0-alpha.1
1.1.0-alpha.2
```
- Funcionalidades en desarrollo activo
- Puede contener bugs significativos
- No recomendado para uso en producción

#### Beta (Pruebas)
```
1.1.0-beta.1
1.1.0-beta.2
```
- Funcionalidades completas pero en fase de testing
- Puede contener bugs menores
- Apta para pruebas en entornos de staging

#### Release Candidate (RC)
```
1.1.0-rc.1
1.1.0-rc.2
```
- Candidata para lanzamiento final
- Solo correcciones críticas permitidas
- Lista para producción pendiente validación final

---

## Guía Rápida de Versionado

### Cuándo incrementar cada número:

**MAJOR (X.0.0)** - Incrementar cuando:
- Cambios en la API que rompen compatibilidad
- Eliminación de funcionalidades existentes
- Cambios en el esquema de base de datos no retrocompatibles
- Reestructuración mayor de la arquitectura

**MINOR (0.X.0)** - Incrementar cuando:
- Nuevas funcionalidades añadidas (backward compatible)
- Mejoras significativas en funcionalidades existentes
- Nuevos endpoints en la API
- Deprecación de funcionalidades (pero aún funcionan)

**PATCH (0.0.X)** - Incrementar cuando:
- Correcciones de bugs
- Mejoras de rendimiento
- Ajustes menores de UI/UX
- Actualizaciones de documentación
- Correcciones de seguridad menores

### Ejemplos:
```
1.0.0 → 1.0.1   (fix de bug crítico)
1.0.1 → 1.1.0   (nueva funcionalidad: reportes)
1.1.0 → 2.0.0   (cambio en estructura de API)
1.1.0 → 1.1.1-beta.1   (pre-release para testing)
```

---

## Plantilla para Nuevas Versiones

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Añadido
- Nueva funcionalidad o característica

### Cambiado
- Cambios en funcionalidades existentes

### Obsoleto
- Funcionalidades que serán removidas próximamente

### Eliminado
- Funcionalidades removidas

### Corregido
- Bugs corregidos

### Seguridad
- Parches de seguridad
```

---

