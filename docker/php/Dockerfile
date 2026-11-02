# Imagen PHP-FPM para desarrollo de DinamyLAB
# 8.5: versión objetivo del proyecto (último parche disponible en la etiqueta)
FROM php:8.5-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip intl bcmath gd exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Mismo UID/GID que el usuario de WSL para evitar problemas de permisos en el bind mount
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-dinamylab.ini

WORKDIR /var/www/html
USER www-data
