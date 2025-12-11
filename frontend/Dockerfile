FROM node:20-alpine

# Configurar directorio de trabajo
WORKDIR /app

# Exponer puerto de desarrollo
EXPOSE 3000

# Variable de entorno para habilitar polling
ENV WATCHPACK_POLLING=true
ENV CHOKIDAR_USEPOLLING=true

# Comando por defecto (desarrollo)
CMD ["sh", "-c", "npm install && npm run dev"]
