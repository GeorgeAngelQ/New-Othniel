# Imagen base PHP + Apache
FROM php:8.2-apache

# Instalar dependencias del sistema + Python
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev \
    python3 python3-pip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath

# Habilitar mod_rewrite en Apache para Laravel
RUN a2enmod rewrite

# Instalar Composer dentro del contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar todo el proyecto al contenedor
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a storage y bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Instalar dependencias de Python del agente
RUN pip3 install --no-cache-dir --break-system-packages -r pai_agent/requirements.txt

# Copiar entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
