FROM node:20-alpine

# Configurar directorio de trabajo
WORKDIR /app

# Copiar package files primero (mejor caching de layers)
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

# Exponer puerto de desarrollo
EXPOSE 3000

# Variables de entorno para habilitar polling
# (Necesario para hot reload en Docker con macOS/Windows/Rancher Desktop)
ENV WATCHPACK_POLLING=true
ENV CHOKIDAR_USEPOLLING=true

# Comando de desarrollo
CMD ["npm", "run", "dev"]
