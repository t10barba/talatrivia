FROM node:20-alpine

# Configurar directorio de trabajo
WORKDIR /app

# Exponer puerto de desarrollo
EXPOSE 3000

# Script de inicialización
COPY <<'EOF' /usr/local/bin/init-nextjs.sh
#!/bin/sh
if [ ! -f "package.json" ]; then
    echo "Creando proyecto Next.js..."
    # Limpiar archivos que pueden causar conflicto
    rm -rf .next node_modules
    # Crear proyecto en temporal y mover
    npx create-next-app@latest temp --typescript --tailwind --app --no-src-dir --import-alias '@/*' --use-npm --yes
    mv temp/* temp/.* . 2>/dev/null || true
    rm -rf temp
fi
echo "Instalando dependencias..."
npm install
echo "Iniciando servidor de desarrollo..."
npm run dev
EOF

RUN chmod +x /usr/local/bin/init-nextjs.sh

# Comando por defecto (desarrollo)
CMD ["/bin/sh", "/usr/local/bin/init-nextjs.sh"]
