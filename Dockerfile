FROM webdevops/php-apache:8.1

# Configurar Apache para escuchar en el puerto que Railway asigna
ENV WEB_DOCUMENT_ROOT=/app
ENV WEB_DOCUMENT_INDEX=index.php

# El puerto interno de Apache - Railway hace el mapeo automáticamente
# Configurar para usar puerto 80 (default) y decirle a Railway que use 80
ENV APACHE_LISTEN=80

# Copiar archivos del proyecto
COPY . /app/

# Configurar permisos
RUN chown -R application:application /app/

# Apache escucha en puerto 80 por defecto
EXPOSE 80
