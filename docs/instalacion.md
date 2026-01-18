# Guía de Instalación y Configuración
## Sistema de Gestión de Tienda de Calzado

### Requisitos del Sistema

**Software necesario:**
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache o Nginx (servidor web)
- XAMPP, WAMP o LAMP (recomendado para desarrollo local)

**Extensiones PHP requeridas:**
- PDO
- PDO_MySQL
- mbstring
- json

### Instalación Paso a Paso

#### 1. Preparar el Entorno

**Opción A: Usando XAMPP (Recomendado para Windows)**
1. Descargar e instalar XAMPP desde https://www.apachefriends.org/
2. Iniciar el panel de control de XAMPP
3. Activar los servicios Apache y MySQL

**Opción B: Usando WAMP (Windows)**
1. Descargar e instalar WAMP desde http://www.wampserver.com/
2. Iniciar WAMP
3. Verificar que el icono esté en verde (servicios activos)

#### 2. Configurar la Base de Datos

1. Abrir phpMyAdmin:
   - URL: http://localhost/phpmyadmin
   - Usuario: root
   - Contraseña: (dejar en blanco o según configuración)

2. Crear la base de datos:
   - Click en "Nueva" en el panel izquierdo
   - Nombre: `tienda_calzado`
   - Cotejamiento: `utf8mb4_unicode_ci`
   - Click en "Crear"

3. Importar el esquema:
   - Seleccionar la base de datos `tienda_calzado`
   - Click en la pestaña "Importar"
   - Click en "Seleccionar archivo"
   - Navegar a: `database/schema_final.sql`
   - Click en "Continuar"

#### 3. Instalar la Aplicación

1. Copiar los archivos del proyecto:
   ```
   - Si usa XAMPP: Copiar la carpeta del proyecto a C:\xampp\htdocs\
   - Si usa WAMP: Copiar la carpeta del proyecto a C:\wamp64\www\
   ```

2. Renombrar la carpeta a `tienda-calzado` (opcional pero recomendado)

#### 4. Configurar la Conexión

1. Abrir el archivo: `config/conexion.php`

2. Verificar/modificar las credenciales de la base de datos:
   ```php
   private static $host = 'localhost';
   private static $dbname = 'tienda_calzado';
   private static $username = 'root';
   private static $password = ''; // Cambiar si tiene contraseña
   ```

3. Si su MySQL tiene contraseña, modificar la línea `$password`

#### 5. Verificar la Instalación

1. Abrir navegador web

2. Acceder a: `http://localhost/tienda-calzado/index.php`

3. Verificar que la página principal cargue correctamente

### Usuarios de Prueba

El sistema incluye usuarios preconfigurados:

**Administrador:**
- Usuario: admin@tiendacalzado.com
- Contraseña: admin123
- Cédula: 1234567890

**Cliente:**
- Usuario: juan.perez@email.com
- Contraseña: cliente123
- Cédula: 0987654321

### Solución de Problemas Comunes

**Error: "No se pudo conectar a la base de datos"**
- Verificar que MySQL esté activo
- Revisar credenciales en `config/conexion.php`
- Verificar que la base de datos exista

**Error 404 al acceder**
- Verificar que la carpeta esté en el directorio correcto (htdocs o www)
- Verificar el nombre de la carpeta en la URL

**Los productos no cargan**
- Verificar que la base de datos tenga datos de prueba
- Abrir la consola del navegador (F12) para ver errores JavaScript
- Verificar que los scripts estén cargando correctamente

**Errores de sesión**
- Verificar que PHP tenga permisos de escritura en el directorio de sesiones
- En XAMPP: Verificar php.ini que session.save_path esté configurado

### Configuración Adicional

**Cambiar el puerto de Apache (si el puerto 80 está ocupado):**
1. Abrir: `C:\xampp\apache\conf\httpd.conf`
2. Buscar: `Listen 80`
3. Cambiar a: `Listen 8080` (o el puerto deseado)
4. Reiniciar Apache
5. Acceder vía: `http://localhost:8080/tienda-calzado/`

**Habilitar mostrar errores PHP (solo desarrollo):**
1. Abrir: `C:\xampp\php\php.ini`
2. Buscar: `display_errors`
3. Cambiar a: `display_errors = On`
4. Reiniciar Apache

### Despliegue en Servidor de Producción

1. **Subir archivos:**
   - Usar FTP/SFTP para subir todos los archivos al servidor
   - Mantener la estructura de carpetas

2. **Crear base de datos:**
   - Usar el panel de control del hosting (cPanel, Plesk, etc.)
   - Crear base de datos MySQL
   - Importar `database/schema_final.sql`

3. **Configurar conexión:**
   - Editar `config/conexion.php` con credenciales del servidor

4. **Ajustes de seguridad:**
   - Cambiar contraseñas de usuarios de prueba
   - Deshabilitar mostrar errores en producción
   - Configurar HTTPS (certificado SSL)

### Mantenimiento

**Respaldo de base de datos:**
```sql
-- Exportar vía phpMyAdmin o línea de comandos:
mysqldump -u root -p tienda_calzado > respaldo.sql
```

**Actualizar datos:**
- Acceder al panel de administración
- Usar las funciones CRUD para gestionar productos, clientes y promociones
- Administrar datos de perfil, ubicación geográfica y generar reportes financieros con exportación PDF

### Soporte

Para soporte técnico, contactar a:
- Email: contacto@calzadec.com
- Teléfono: +593 99 876 5432

---

**Última actualización:** Enero 2026  
**Versión:** 1.0
