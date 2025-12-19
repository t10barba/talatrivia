FROM php:8.2-fpm

# Build argument for version
ARG VERSION=latest

# Metadata labels
LABEL version="${VERSION}"
LABEL description="TalaTrivia Backend - Symfony Admin Panel"
LABEL maintainer="TalaTrivia Team"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    intl \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/backend

# Script de inicialización
COPY <<'EOF' /usr/local/bin/init-symfony.sh
#!/bin/sh
if [ ! -f "composer.json" ]; then
    echo "Creando proyecto Symfony con EasyAdmin..."
    composer config --global audit.block-insecure false
    composer create-project symfony/skeleton temp
    mv temp/* temp/.* . 2>/dev/null || true
    rm -rf temp
    composer require symfony/orm-pack symfony/maker-bundle --dev
    composer require easycorp/easyadmin-bundle
else
    echo "Proyecto Symfony existe, instalando dependencias..."
    composer install --no-interaction --optimize-autoloader
fi
php-fpm
EOF

RUN chmod +x /usr/local/bin/init-symfony.sh

EXPOSE 9000

CMD ["/bin/sh", "/usr/local/bin/init-symfony.sh"]
