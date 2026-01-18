# CalzadEC - Tienda de Calzado

Sistema de gestión de ventas de calzado desarrollado con arquitectura MVC en PHP.

## 🚀 Despliegue en Railway

### Paso 1: Subir a GitHub
```bash
cd c:/xampp/htdocs/grupal
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/grupal.git
git push -u origin main
```

### Paso 2: Crear Proyecto en Railway
1. Ve a [railway.app](https://railway.app) y crea una cuenta
2. Clic en **"New Project"**
3. Selecciona **"Deploy from GitHub repo"**
4. Conecta tu cuenta de GitHub y selecciona el repositorio

### Paso 3: Agregar Base de Datos MySQL
1. En tu proyecto Railway, clic en **"+ New"**
2. Selecciona **"Database" → "MySQL"**
3. Espera a que se aprovisione (1-2 minutos)

### Paso 4: Conectar Variables
Railway configura automáticamente las variables `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`.

La aplicación las detectará automáticamente gracias a `config/conexion.php`.

### Paso 5: Importar Base de Datos
1. En Railway, clic en el servicio MySQL
2. Ve a la pestaña **"Data"**
3. Clic en **"Query"**
4. Copia y pega el contenido de `database/schema.sql`
5. Ejecuta el script

### Paso 6: Generar Dominio
1. Clic en tu servicio PHP
2. Ve a **Settings → Networking**
3. Clic en **"Generate Domain"**
4. Tu app estará en `https://tu-proyecto.up.railway.app`

---

## 💻 Desarrollo Local (XAMPP)

1. Copia el proyecto a `C:/xampp/htdocs/grupal`
2. Inicia Apache y MySQL desde XAMPP
3. Importa `database/schema.sql` en phpMyAdmin
4. Accede a `http://localhost/grupal`

### Credenciales de Prueba
- **Admin:** admin@tiendacalzado.com / admin123
- **Cliente:** juan.perez@email.com / cliente123

---

## 📁 Estructura del Proyecto

```
grupal/
├── config/          # Configuración de base de datos
├── controlador/     # Controladores MVC
├── modelo/          # Modelos de datos
├── vista/           # Vistas HTML/PHP
│   ├── admin/       # Panel de administración
│   └── cliente/     # Área de clientes
├── css/             # Estilos
├── js/              # JavaScript
├── img/             # Imágenes
├── database/        # Script SQL
└── index.php        # Punto de entrada
```
# CalzadEC
