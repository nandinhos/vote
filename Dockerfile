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

# Usar www-data padrão do PHP-FPM (mais compatível)
RUN addgroup -g 82 -S www-data || true && \
    adduser -u 82 -S www-data -G www-data || true

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

# Criar diretórios necessários primeiro
RUN mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/app/public

# Criar arquivo .env se não existir (para build)
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Gerar APP_KEY se necessário
RUN if ! grep -q "APP_KEY=base64:" .env; then php artisan key:generate --force; fi

# Definir permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar configurações
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/init.sh /usr/local/bin/init.sh

# Tornar o script executável
RUN chmod +x /usr/local/bin/init.sh

# Build assets
RUN npm run build

# Limpar cache npm
RUN npm cache clean --force && rm -rf node_modules

# Expor porta
EXPOSE 80

# Comando de inicialização usando nosso script
ENTRYPOINT ["/usr/local/bin/init.sh"]