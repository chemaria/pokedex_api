# docker/Dockerfile
FROM php:8.4-fpm-bookworm

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      $PHPIZE_DEPS \
      git unzip \
      libicu-dev \
      libzip-dev \
      zlib1g-dev \
      libsqlite3-dev \
      libonig-dev \
      pkg-config \
 && docker-php-ext-install -j"$(nproc)" \
      intl \
      mbstring \
      pdo_sqlite \
      zip \
 && rm -rf /var/lib/apt/lists/*

# Composer binario
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
