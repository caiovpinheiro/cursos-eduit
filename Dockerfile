FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    poppler-utils \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip xml dom

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos de configuração
COPY . .

# Instalar dependências do PHP
RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache/data \
        storage/logs \
        bootstrap/cache \
    && chmod +x /var/www/html/docker/entrypoint.sh \
    && composer install --no-dev --optimize-autoloader

# Configurar permissões (bind mount no runtime pode sobrescrever — ver docker/entrypoint.sh)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R ug+rwx /var/www/html/storage \
    && chmod -R ug+rwx /var/www/html/bootstrap/cache

# Configurar Nginx
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Configurar Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expor porta
EXPOSE 80

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
# Comando de inicialização
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
