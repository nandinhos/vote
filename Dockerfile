# Dockerfile para Sistema de Votação Laravel
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    sqlite-dev \
    nodejs \
    npm \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário para aplicação
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de dependências
COPY composer.json composer.lock package.json package-lock.json ./

# Instalar dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Instalar dependências Node.js (incluindo dev para build)
RUN npm ci --legacy-peer-deps

# Copiar código da aplicação
COPY . .

# Criar diretórios necessários e definir permissões
RUN mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && chown -R www:www /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copiar configurações
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Build assets
RUN npm run build

# Limpar cache npm
RUN npm cache clean --force && rm -rf node_modules

# Executar comandos Laravel
RUN php artisan config:cache \
    && php artisan route:cache

# Expor porta
EXPOSE 80

# Comando de inicialização
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]