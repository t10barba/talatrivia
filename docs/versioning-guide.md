# Guía de Versionado - TalaTrivia

Esta guía explica el sistema de versionado implementado en TalaTrivia y cómo gestionar releases siguiendo el flujo de trabajo con Gitflow.

## Tabla de Contenidos

1. [Sistema de Versionado](#sistema-de-versionado)
2. [Estructura de Archivos](#estructura-de-archivos)
3. [Workflow de Releases con Gitflow](#workflow-de-releases-con-gitflow)
4. [Pre-releases](#pre-releases)
5. [Docker y Versionado](#docker-y-versionado)
6. [CI/CD (Futuro)](#cicd-futuro)
7. [Comandos Útiles](#comandos-útiles)

---

## Sistema de Versionado

TalaTrivia utiliza **Semantic Versioning 2.0.0** (SemVer):

```
MAJOR.MINOR.PATCH[-prerelease]
```

### Componentes:

- **MAJOR** (X.0.0): Cambios incompatibles con versiones anteriores (breaking changes)
- **MINOR** (0.X.0): Nuevas funcionalidades compatibles con versiones anteriores
- **PATCH** (0.0.X): Correcciones de bugs compatibles

### Pre-releases:

- **alpha**: `1.1.0-alpha.1` - Desarrollo temprano, inestable
- **beta**: `1.1.0-beta.1` - Funcionalidades completas, en testing
- **rc**: `1.1.0-rc.1` - Release candidate, listo para producción

---

## Estructura de Archivos

La versión está centralizada en el archivo `/VERSION` y sincronizada en múltiples ubicaciones:

```
talatrivia/
├── VERSION                          # Fuente principal (1.0.0)
├── .env.example                     # APP_VERSION=1.0.0
├── package.json                     # "version": "1.0.0"
├── CHANGELOG.md                     # Historial de cambios
├── api/
│   ├── .env.example                 # APP_VERSION=1.0.0
│   └── config/packages/
│       └── nelmio_api_doc.yaml      # version: '%env(APP_VERSION)%'
├── backend/
│   └── .env.example                 # APP_VERSION=1.0.0
└── frontend/
    └── package.json                 # "version": "1.0.0"
```

---

## Workflow de Releases con Gitflow

### 1. Preparar Release

Desde la rama `develop`, crear una rama de release:

```bash
# Asegúrate de estar en develop actualizado
git checkout develop
git pull origin develop

# Crear rama de release (ejemplo: versión 1.1.0)
git checkout -b release/1.1.0
```

### 2. Actualizar Versión

Editar los siguientes archivos con la nueva versión:

**a) Archivo VERSION:**
```bash
echo "1.1.0" > VERSION
```

**b) package.json (raíz):**
```json
{
  "name": "talatrivia",
  "version": "1.1.0",
  ...
}
```

**c) frontend/package.json:**
```json
{
  "name": "talatrivia-frontend",
  "version": "1.1.0",
  ...
}
```

**d) Archivos .env:**
```bash
# .env.example (raíz)
APP_VERSION=1.1.0

# api/.env.example
APP_VERSION=1.1.0

# backend/.env.example
APP_VERSION=1.1.0
```

### 3. Actualizar CHANGELOG.md

Agregar sección para la nueva versión:

```markdown
## [1.1.0] - 2025-12-20

### Añadido
- Nueva funcionalidad de reportes estadísticos
- Exportación de resultados a PDF

### Cambiado
- Mejora en la interfaz de preguntas
- Optimización de consultas a base de datos

### Corregido
- Bug en cálculo de puntajes
- Error en validación de respuestas múltiples
```

### 4. Commit de Cambios de Versión

```bash
git add VERSION package.json frontend/package.json .env.example api/.env.example backend/.env.example CHANGELOG.md
git commit -m "Bump version to 1.1.0"
```

### 5. Testing y Ajustes Finales

Realizar pruebas finales en la rama de release:

```bash
# Levantar entorno con la nueva versión
docker compose build
docker compose up -d

# Verificar versión en endpoints
curl http://localhost:8080/api/version
# Debe retornar: {"version":"1.1.0","application":"TalaTrivia API","timestamp":...}

# Verificar versión en frontend
# Abrir http://localhost:3000 y revisar footer
```

Si hay bugs o ajustes:
```bash
# Realizar correcciones
git add .
git commit -m "fix: corregir bug en feature X"
```

### 6. Merge a Main

Una vez aprobado el release:

```bash
# Cambiar a main
git checkout main
git pull origin main

# Merge de release (sin fast-forward para mantener historial)
git merge --no-ff release/1.1.0

# Crear tag anotado
git tag -a v1.1.0 -m "Release version 1.1.0

Cambios principales:
- Nueva funcionalidad de reportes
- Mejoras de rendimiento
- Correcciones de bugs"

# Push a main con tags
git push origin main
git push origin v1.1.0
```

### 7. Merge de Vuelta a Develop

Integrar cambios de vuelta a develop:

```bash
git checkout develop
git merge --no-ff release/1.1.0
git push origin develop
```

### 8. Eliminar Rama de Release

```bash
git branch -d release/1.1.0
git push origin --delete release/1.1.0
```

---

## Pre-releases

Para versiones de prueba antes del release oficial:

### Alpha (Desarrollo Temprano)

```bash
# Crear desde develop
git checkout -b release/1.2.0-alpha

# Actualizar VERSION
echo "1.2.0-alpha.1" > VERSION

# Actualizar otros archivos y CHANGELOG
# ...

# Commit y tag
git commit -am "Version 1.2.0-alpha.1"
git tag v1.2.0-alpha.1
git push origin release/1.2.0-alpha
git push origin v1.2.0-alpha.1
```

### Beta (Testing)

```bash
# Continuar en la misma rama o crear nueva
echo "1.2.0-beta.1" > VERSION

# Actualizar archivos y CHANGELOG
# ...

git commit -am "Version 1.2.0-beta.1"
git tag v1.2.0-beta.1
git push origin v1.2.0-beta.1
```

### Release Candidate

```bash
# Última fase antes de release final
echo "1.2.0-rc.1" > VERSION

# Actualizar archivos y CHANGELOG
# ...

git commit -am "Version 1.2.0-rc.1"
git tag v1.2.0-rc.1
git push origin v1.2.0-rc.1
```

### Release Final

```bash
# Solo cambiar el sufijo
echo "1.2.0" > VERSION

# Seguir workflow normal de release (pasos 2-8)
```

---

## Docker y Versionado

### Variables de Entorno

El archivo `.env` en la raíz define la versión para Docker:

```bash
APP_VERSION=1.1.0
```

### Build con Versión Específica

```bash
# Build de todas las imágenes con versión
APP_VERSION=1.1.0 docker compose build

# Build de imagen específica
docker build -t talatrivia-api:1.1.0 --build-arg VERSION=1.1.0 ./api

# Listar imágenes con tags
docker images | grep talatrivia
```

### Resultado de las Imágenes:

```
talatrivia-api:1.1.0
talatrivia-backend:1.1.0
talatrivia-frontend:1.1.0
```

### Ejecutar con Versión Específica

```bash
# Configurar versión en .env
echo "APP_VERSION=1.1.0" > .env

# Levantar servicios
docker compose up -d

# Verificar imágenes en uso
docker compose ps
```

### Inspeccionar Metadata de Imagen

```bash
# Ver label de versión
docker inspect talatrivia-api:1.1.0 --format='{{.Config.Labels.version}}'
# Output: 1.1.0

# Ver todos los labels
docker inspect talatrivia-api:1.1.0 --format='{{json .Config.Labels}}' | jq
```

---

## CI/CD (Futuro)

Cuando se implemente CI/CD, el pipeline leerá el archivo `VERSION` para:

1. **Validar formato de versión**
2. **Build automático de imágenes Docker** con tag de versión
3. **Push a registry** (Docker Hub, GitHub Container Registry, etc.)
4. **Deploy automático** a ambientes según branch:
   - `develop` → staging
   - `main` (con tag) → production
5. **Notificaciones** de releases

### Estructura Sugerida (.github/workflows/release.yml):

```yaml
name: Release

on:
  push:
    tags:
      - 'v*'

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Read VERSION
        id: version
        run: echo "VERSION=$(cat VERSION)" >> $GITHUB_OUTPUT
      
      - name: Build Docker Images
        run: |
          docker compose build
          docker tag talatrivia-api:latest talatrivia-api:${{ steps.version.outputs.VERSION }}
      
      - name: Push to Registry
        run: |
          docker push talatrivia-api:${{ steps.version.outputs.VERSION }}
```

---

## Comandos Útiles

### Verificar Versión Actual

```bash
# Desde archivo VERSION
cat VERSION

# En el sistema (si está desplegado)
curl http://localhost:8080/api/version | jq
```

### Listar Tags de Git

```bash
# Todos los tags
git tag

# Tags con mensajes
git tag -n

# Tags remotos
git ls-remote --tags origin

# Último tag
git describe --tags --abbrev=0
```

### Comparar Versiones

```bash
# Diferencias entre tags
git diff v1.0.0 v1.1.0

# Commits entre dos tags
git log v1.0.0..v1.1.0 --oneline

# Archivos cambiados entre versiones
git diff v1.0.0 v1.1.0 --stat
```

### Revertir Versión (Rollback)

```bash
# Crear rama desde tag anterior
git checkout v1.0.0
git checkout -b hotfix/rollback-1.1.0

# Crear nuevo tag de hotfix
git tag v1.0.1
git push origin v1.0.1
```

### Limpiar Imágenes Docker Antiguas

```bash
# Eliminar imágenes sin tag
docker image prune

# Eliminar versiones específicas
docker rmi talatrivia-api:1.0.0
docker rmi talatrivia-backend:1.0.0
docker rmi talatrivia-frontend:1.0.0

# Eliminar todas las imágenes del proyecto (cuidado!)
docker images | grep talatrivia | awk '{print $3}' | xargs docker rmi
```

---

## Checklist de Release

Antes de cada release, verificar:

- [ ] Todos los tests pasan
- [ ] Documentación actualizada
- [ ] CHANGELOG.md completo con todos los cambios
- [ ] VERSION actualizado en todos los archivos
- [ ] .env.example actualizado con APP_VERSION
- [ ] Migraciones de base de datos ejecutadas y documentadas
- [ ] Build de Docker exitoso con nueva versión
- [ ] Endpoint /api/version retorna versión correcta
- [ ] Frontend muestra versión correcta en footer
- [ ] Tag de git creado y pusheado
- [ ] Merge a main completado
- [ ] Merge de vuelta a develop completado
- [ ] Rama de release eliminada

---

## Contacto y Soporte

Para dudas sobre el proceso de versionado:
- Revisar este documento
- Consultar [CHANGELOG.md](../CHANGELOG.md)
- Revisar archivo [VERSION](../VERSION) para ver comentarios detallados
- Contactar al equipo de desarrollo

---

**Última actualización**: 19 de diciembre de 2025  
**Versión del documento**: 1.0.0
