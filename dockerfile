# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    python3 \
    python3-pip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Habilitamos mod_rewrite de Apache
RUN a2enmod rewrite

# Instalamos Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiamos el código de Laravel al contenedor
WORKDIR /var/www/html
COPY . .

# Instalamos dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Instalamos dependencias de Python (si tienes requirements.txt)
RUN pip3 install --no-cache-dir -r flask flask-cors jsonify requests blueprint

# Exponemos el puerto 80
EXPOSE 80

# Comando por defecto para iniciar Apache
CMD ["apache2-foreground"]
