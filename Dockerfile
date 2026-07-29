# 1. Definimos la imagen base. Usaremos PHP 8.3 con FPM (FastCGI Process Manager)
FROM php:8.3-fpm

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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Configurar OPcache para mejorar rendimiento
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# 5. Descargamos e instalamos Composer directamente desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecemos el directorio de trabajo dentro del contenedor
WORKDIR /var/www
