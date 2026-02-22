FROM php:8.2-fpm

# Instalamos dependencias de sistema y Node.js (para compilar Vue)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Limpiamos caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos extensiones necesarias para Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www