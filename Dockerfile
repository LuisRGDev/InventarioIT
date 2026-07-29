# 1. Definimos la imagen base. Usaremos PHP 8.2 con FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# 2. Instalamos dependencias del sistema operativo que Laravel necesita
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# 3. Limpiamos la caché de apt para que la imagen sea más ligera
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Instalamos las extensiones de PHP que requiere Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 5. Descargamos e instalamos Composer directamente desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecemos el directorio de trabajo dentro del contenedor
WORKDIR /var/www
