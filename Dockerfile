FROM webdevops/php-apache:8.1

# Variables de entorno para Apache
ENV WEB_DOCUMENT_ROOT=/app
ENV WEB_DOCUMENT_INDEX=index.php

# Copiar archivos del proyecto
COPY . /app/

# Configurar permisos
RUN chown -R application:application /app/

# Puerto para Railway
EXPOSE 8080
ENV WEB_ALIAS_DOMAIN=*
ENV APACHE_LISTEN=8080
