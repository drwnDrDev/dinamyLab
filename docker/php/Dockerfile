# Imagen PHP-FPM para desarrollo de DinamyLAB
# 8.5: versión objetivo del proyecto (último parche disponible en la etiqueta)
FROM mysql:8.4 AS mysql-client

FROM php:8.5-fpm

ARG UID=1000
ARG GID=1000

# libncurses6 es necesaria para el cliente mysql oficial.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libncurses6 libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_sqlite zip intl bcmath gd exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Clientes de la misma versión que el servicio db para los schema dumps de Laravel.
COPY --from=mysql-client /usr/bin/mysql /usr/bin/mysqldump /usr/local/bin/
RUN mysql --version && mysqldump --version

# Mismo UID/GID que el usuario de WSL para evitar problemas de permisos en el bind mount
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-dinamylab.ini

WORKDIR /var/www/html
USER www-data
