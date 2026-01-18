FROM php:7.4-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/

# Exponer puerto (Railway usa variable PORT, pero 80 es el default interno)
EXPOSE 80
