FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql zip mbstring intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar extensão Redis
RUN pecl install redis && docker-php-ext-enable redis

# Configurar diretório de trabalho
WORKDIR /var/www

# Ajustar permissões
RUN chown -R www-data:www-data /var/www

# Expor porta PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]