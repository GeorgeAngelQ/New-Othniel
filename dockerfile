# Imagen base PHP 8.2 + Apache
FROM php:8.2-apache

# ---- DEPENDENCIAS DEL SISTEMA ----
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev python3 python3-pip curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath \
    && a2enmod rewrite

# Configurar DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# ---- INSTALAR COMPOSER ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ---- COPIAR ARCHIVOS NECESARIOS PARA CACHE DE NODE ----
COPY package.json package-lock.json vite.config.js ./

# ---- INSTALAR NODE ----
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar dependencias frontend
RUN npm ci --legacy-peer-deps

# ---- COPIAR TODO EL PROYECTO ----
COPY . .

# 🔥 Copiar las imágenes a storage en el contenedor
COPY storage/app/public storage/app/public

# ---- COMPILAR ASSETS ANTES DE INSTALAR DEPENDENCIAS PHP ----
RUN npm run build

# ---- INSTALAR DEPENDENCIAS PHP ----
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ---- CREAR STORAGE LINK ----
RUN php artisan storage:link

# ---- INSTALAR DEPENDENCIAS PYTHON DEL AGENTE ----
RUN pip3 install --no-cache-dir --break-system-packages -r pai_agent/requirements.txt

# ---- PERMISOS ----
RUN chown -R www-data:www-data storage bootstrap/cache public/build

EXPOSE 80
CMD ["apache2-foreground"]
