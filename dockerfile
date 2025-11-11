# Imagen base PHP 8.2 + Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpq-dev libonig-dev python3 python3-pip curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Cambiar DocumentRoot a public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar proyecto completo al contenedor
WORKDIR /var/www/html
COPY . .

# ✅ Instalar Node y build de Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install --force \
    && npm run build

# Instalar dependencias del frontend
RUN npm ci --legacy-peer-deps && npm run build

# Instalar dependencias Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias Python del agente
RUN pip3 install --no-cache-dir --break-system-packages -r pai_agent/requirements.txt

# Permisos necesarios
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
