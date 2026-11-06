FROM php:8.2-apache

# Instalar dependencias del sistema y módulos necesarios para PDO SQLite y MySQL
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configurar DocumentRoot de Apache hacia la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar AllowOverride All para soporte de reescritura de URLs
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copiar el código fuente de la aplicación
WORKDIR /var/www/html
COPY . /var/www/html/

# Asegurar directorios de almacenamiento y permisos para el usuario www-data de Apache
RUN mkdir -p /var/www/html/storage/uploads \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage

# Copiar script de arranque para soporte dinámico del puerto PORT de Render
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Render asigna el puerto mediante la variable de entorno $PORT (por defecto 10000 o 80)
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]
