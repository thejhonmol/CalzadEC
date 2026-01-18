# Guía de Despliegue en Railway

Esta guía documenta el proceso completo para desplegar la aplicación CalzadEC en Railway con almacenamiento de imágenes en Cloudinary.

## Requisitos Previos

- Cuenta en [GitHub](https://github.com)
- Cuenta en [Railway](https://railway.app) (gratis)
- Cuenta en [Cloudinary](https://cloudinary.com) (gratis)
- [DBeaver](https://dbeaver.io) o cliente SQL similar

---

## 1. Preparación del Proyecto

### 1.1 Estructura del Dockerfile

El proyecto incluye un `Dockerfile` configurado para Railway:

```dockerfile
FROM webdevops/php-apache:8.1

ENV WEB_DOCUMENT_ROOT=/app
ENV WEB_DOCUMENT_INDEX=index.php
ENV APACHE_LISTEN=80

COPY . /app/
RUN chown -R application:application /app/

EXPOSE 80
```

### 1.2 Subir a GitHub

```bash
git init
git add .
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/TU_REPO.git
git push -u origin main
```

---

## 2. Configuración de Railway

### 2.1 Crear Proyecto

1. Inicia sesión en [Railway](https://railway.app)
2. Clic en **"New Project"** → **"Deploy from GitHub repo"**
3. Selecciona tu repositorio
4. Railway detectará el Dockerfile automáticamente

### 2.2 Añadir Base de Datos MySQL

1. En tu proyecto, clic en **"New"** → **"Database"** → **"MySQL"**
2. Espera a que se cree (unos segundos)

### 2.3 Conectar App con MySQL

1. Clic en el servicio de tu **aplicación** (no MySQL)
2. Ve a la pestaña **"Variables"**
3. Añade las siguientes variables con referencias:

| Variable | Referencia |
|----------|------------|
| `MYSQLHOST` | `${{MySQL.MYSQLHOST}}` |
| `MYSQLPORT` | `${{MySQL.MYSQLPORT}}` |
| `MYSQLUSER` | `${{MySQL.MYSQLUSER}}` |
| `MYSQLPASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `MYSQLDATABASE` | `${{MySQL.MYSQLDATABASE}}` |

### 2.4 Generar Dominio Público

1. Clic en tu servicio de aplicación
2. Ve a **"Settings"** → **"Networking"**
3. Clic en **"Generate Domain"**
4. Selecciona puerto **80**
5. Copia la URL generada (ej: `https://calzadec-production.up.railway.app`)

---

## 3. Importar Base de Datos con DBeaver

### 3.1 Instalar DBeaver

1. Descarga desde: https://dbeaver.io/download/
2. Instala con las opciones por defecto (incluir Java)

### 3.2 Obtener Credenciales de Railway

1. En Railway, clic en el servicio **MySQL**
2. Ve a **"Connect"** → **"Public Network"**
3. Copia los datos de conexión:
   - Host (ej: `mainline.proxy.rlwy.net`)
   - Port (ej: `50643`)
   - Username: `root`
   - Password
   - Database: `railway`

### 3.3 Conectar DBeaver a Railway

1. Abre DBeaver
2. Clic en **Nueva Conexión** (ícono de enchufe con +)
3. Selecciona **MySQL** → **Siguiente**
4. Ingresa los datos de conexión de Railway
5. Ve a **"Driver properties"** y configura:
   - `allowPublicKeyRetrieval` = `true`
   - `useSSL` = `false`
6. Clic en **"Test Connection"** → Debería aparecer "Connected"
7. Clic en **"Finish"**

### 3.4 Ejecutar Script SQL

1. Clic derecho sobre la conexión → **"SQL Editor"** → **"Open SQL Script"**
2. Abre el archivo `database/schema_railway.sql`
3. Presiona **Ctrl+Enter** para ejecutar todo el script
4. Verifica que se crearon las tablas (Refresh en el panel izquierdo)

---

## 4. Configuración de Cloudinary

Cloudinary permite almacenar imágenes de productos en la nube, ya que Railway tiene sistema de archivos efímero.

### 4.1 Crear Cuenta

1. Regístrate en [Cloudinary](https://cloudinary.com) (gratis)
2. Confirma tu email

### 4.2 Crear Upload Preset

1. Ve a **Settings** (⚙️) → **Upload**
2. Baja a **"Upload presets"** → **"Add upload preset"**
3. Configura:
   - **Signing Mode**: `Unsigned` (importante)
   - **Preset name**: `calzadec_preset`
4. Guarda

### 4.3 Obtener Credenciales

Desde tu Dashboard de Cloudinary, copia:
- **Cloud Name** (ej: `dhdsmsdkp`)
- **Preset Name** (el que creaste en el paso anterior)

### 4.4 Configurar en el Proyecto

Edita el archivo `vista/admin/productos.php` (~línea 215):

```javascript
const CLOUDINARY_CLOUD_NAME = 'TU_CLOUD_NAME';
const CLOUDINARY_UPLOAD_PRESET = 'TU_PRESET_NAME';
```

Haz commit y push de los cambios.

---

## 5. Verificación Final

### 5.1 Probar la Aplicación

1. Abre la URL de Railway en tu navegador
2. Verifica que cargue la página principal

### 5.2 Probar Login

| Rol | Email | Contraseña | Provincia/Ciudad |
|-----|-------|------------|------------------|
| Admin | `admin@tiendacalzado.com` | `admin123` | Chimborazo/Riobamba |
| Cliente | `juan.perez@email.com` | `cliente123` | Chimborazo/Riobamba |

### 5.3 Probar Subida de Imágenes

1. Inicia sesión como administrador
2. Ve a **Productos** → **Nuevo Producto**
3. Haz clic en **"Subir Imagen"**
4. Selecciona una imagen de tu PC
5. Verifica que la URL se inserte automáticamente

---

## Solución de Problemas

### Error: "Application failed to respond"
- Verifica que el puerto configurado en Railway coincida con el del Dockerfile
- Revisa los logs en Railway (Deploy → View Logs)

### Error: "Error al cargar productos"
- Verifica que las variables de entorno de MySQL estén configuradas
- Comprueba que el schema se haya importado correctamente

### Error: "Public Key Retrieval is not allowed" (DBeaver)
- En Driver properties, cambiar `allowPublicKeyRetrieval` a `true`

### Error: MPM Conflict en Apache
- Usar la imagen `webdevops/php-apache:8.1` en lugar de `php:X.X-apache`

---

## Archivos Importantes

| Archivo | Descripción |
|---------|-------------|
| `Dockerfile` | Configuración del contenedor para Railway |
| `config/conexion.php` | Conexión a BD con soporte para variables de entorno |
| `database/schema_railway.sql` | Schema adaptado para Railway (con Provincia/Ciudad) |
| `js/ecuador-locations.js` | Base de datos de localizaciones y lógica de dropdowns |
| `vista/admin/reportes.php` | Centro de reportes con exportación PDF |
| `vista/cliente/perfil.php` | Gestión de perfil (Teléfono, Dirección, Ubicación) |

---

## Recursos Adicionales

- [Documentación de Railway](https://docs.railway.app)
- [Documentación de Cloudinary](https://cloudinary.com/documentation)
- [Documentación de DBeaver](https://dbeaver.io/docs/)
