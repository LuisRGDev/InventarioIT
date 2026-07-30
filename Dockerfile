FROM php:8.3-fpm

# ============================================================
# Dependencias del sistema
# ============================================================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        opcache \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ============================================================
# PHP - Directorio temporal
# ============================================================
RUN mkdir -p /tmp \
    && chmod 1777 /tmp

RUN printf "sys_temp_dir=/tmp\nupload_tmp_dir=/tmp\n" \
    > /usr/local/etc/php/conf.d/docker-php-temp.ini

# ============================================================
# PHP - OPcache
# ============================================================
RUN printf "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=1\n" \
> /usr/local/etc/php/conf.d/docker-php-opcache.ini

# ============================================================
# Composer
# ============================================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================================
# Directorio de trabajo
# ============================================================
WORKDIR /var/www

# ============================================================
# Copiar proyecto
# ============================================================
COPY . .

# ============================================================
# Dependencias Laravel
# ============================================================
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# ============================================================
# Permisos iniciales de Laravel
# ============================================================
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data \
        storage \
        bootstrap/cache \
    && chmod -R 775 \
        storage \
        bootstrap/cache

# ============================================================
# PHP-FPM
# ============================================================
EXPOSE 9000

CMD ["php-fpm"]