# Imagem base do PHP com extensões comuns
FROM php:8.3-fpm

# Instalar dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Instalar Composer globalmente
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar os arquivos do projeto
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# Ajustar permissões do storage e bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
