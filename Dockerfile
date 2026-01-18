FROM php:7.4-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Solucionar conflicto de MPMs (More than one MPM loaded)
# Asegurar que solo mpm_prefork esté habilitado (necesario para mod_php)
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Copiar archivos del proyecto
COPY . /var/www/html/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/

# Script de inicio para manejar el puerto dinámico de Railway
# Reemplaza el puerto 80 por la variable $PORT en la configuración de Apache
CMD sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground
