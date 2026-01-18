# Metodología de Desarrollo

Para el desarrollo de esta aplicación web se ha seleccionado una **metodología ágil iterativa** basada en el modelo de **Prototipado Evolutivo**, que permite desarrollar el sistema en ciclos incrementales con retroalimentación continua.

## Fases de Desarrollo

### 1. Análisis

**Objetivo:** Identificar y documentar los requerimientos funcionales y no funcionales del sistema.

**Actividades:**
- Realizar entrevista con stakeholders relacionados al negocio de calzado
- Documentar requerimientos funcionales del sistema
- Identificar roles de usuario (Administrador y Cliente)
- Definir alcance del proyecto

**Requerimientos Identificados:**
- Gestión de catálogo de productos (CRUD completo)
- Clasificación de productos por género: Hombres, Mujeres, Niños
- Clasificación de productos por tipo: Deportivos, No Deportivos
- Control de stock con actualización automática
- Sistema de promociones y descuentos
- Validación de datos de clientes (cédula ecuatoriana, email, teléfono)
- Sistema de autenticación y autorización por roles
- Carrito de compras para clientes
- Generación automática de facturas/recibos
- Panel administrativo con reportes y estadísticas
- Sección de ayuda para usuarios

### 2. Diseño

**Objetivo:** Diseñar la arquitectura del sistema, interfaces de usuario y estructura de datos.

**Actividades:**

#### 2.1 Casos de Uso
- Caso de Uso 1: Gestión de Productos (Administrador)
- Caso de Uso 2: Gestión de Clientes (Administrador)
- Caso de Uso 3: Registro y Autenticación de Usuarios
- Caso de Uso 4: Navegación de Catálogo (Cliente)
- Caso de Uso 5: Realizar Compra (Cliente)
- Caso de Uso 6: Gestión de Promociones (Administrador)
- Caso de Uso 7: Generación de Reportes y Exportación PDF (Administrador)
- Caso de Uso 8: Gestión de Perfil de Usuario (Cliente)

#### 2.2 Mockups y Wireframes
Se desarrollarán mockups para las siguientes interfaces:
- Página de inicio con catálogo público
- Formulario de login
- Dashboard administrativo
- Interfaz de gestión de productos
- Catálogo de cliente con filtros
- Carrito de compras
- Página de factura

#### 2.3 Diseño de Base de Datos

**Modelo Entidad-Relación:**

**Entidades principales:**
- **Usuarios:** Almacena información de administradores y clientes
- **Productos:** Catálogo de calzado con detalles y clasificaciones
- **Marcas:** Catálogo de fabricantes de calzado
- **Promociones:** Descuentos aplicables a marcas, géneros o tipos
- **Ventas:** Registro de transacciones
- **Detalle_Ventas:** Items específicos de cada venta

**Relaciones:**
- Un Usuario puede realizar muchas Ventas (1:N)
- Una Venta contiene muchos Detalle_Ventas (1:N)
- Un Producto puede estar en muchos Detalle_Ventas (1:N)
- Un Producto puede tener una Promoción (1:1 opcional)
- Una Promoción puede aplicarse a muchos Productos (1:N)

### 3. Implementación

**Objetivo:** Codificar el sistema siguiendo la arquitectura MVC definida.

**Actividades:**

#### 3.1 Configuración del Entorno
- Crear estructura de carpetas del proyecto
- Configurar conexión a base de datos MySQL
- Integrar Bootstrap y jQuery
- Configurar FontAwesome para iconos

#### 3.2 Implementación de la Capa Modelo
- Desarrollar clase Conexion.php con PDO
- Implementar Producto.php con métodos CRUD
- Implementar Marca.php para gestión de fabricantes
- Implementar Cliente.php con validaciones
- Implementar Venta.php con lógica de negocio
- Implementar Usuario.php para autenticación

#### 3.3 Implementación de la Capa Controlador
- ProductoController.php: Manejo de solicitudes de productos
- MarcaController.php: Gestión de marcas y catálogo
- ClienteController.php: Gestión de clientes
- VentaController.php: Procesamiento de ventas
- AuthController.php: Autenticación y sesiones
- ReporteController.php: Agregación de datos y reportes
- UsuarioController.php: Gestión de perfil de cliente

#### 3.4 Implementación de la Capa Vista
**Vistas Administrativas:**
- Dashboard con estadísticas
- Gestión de productos (tabla con CRUD)
- Gestión de marcas
- Gestión de clientes
- Historial de ventas
- Gestión de promociones
- Centro de reportes con exportación PDF

**Vistas de Cliente:**
- Catálogo con filtros dinámicos
- Carrito de compras
- Mis compras (historial)
- Gestión de perfil personal

**Vistas Compartidas:**
- Login
- Header y footer
- Sección de ayuda

#### 3.5 Estilos y JavaScript
- Crear estilos.css con diseño moderno y responsivo
- Implementar funciones.js con:
  - Validación de formularios
  - Validación de cédula ecuatoriana
  - Gestión de carrito
  - Filtros dinámicos de productos
  - Integración jsPDF para reportes

### 4. Pruebas

**Objetivo:** Verificar el correcto funcionamiento de todas las funcionalidades.

**Tipos de Pruebas:**

#### 4.1 Pruebas Unitarias
- Validación de cédula ecuatoriana
- Cálculo de totales con descuentos
- Actualización de stock

#### 4.2 Pruebas de Integración
- Flujo completo de compra
- Proceso de autenticación
- Generación de facturas

#### 4.3 Pruebas de Interfaz
- Navegación en todos los módulos
- Responsividad en diferentes dispositivos
- Compatibilidad con navegadores

### 5. Implantación

**Objetivo:** Desplegar la aplicación en un servidor web y documentar su uso.

**Actividades:**

#### 5.1 Preparación para Producción
- Configurar variables de entorno
- Optimizar consultas a base de datos
- Validar seguridad (SQL injection, XSS)

#### 5.2 Documentación
- Manual de usuario para clientes
- Manual de administrador
- Documentación técnica del código
- Guía de instalación y configuración

#### 5.3 Despliegue
- Configurar servidor web (Apache/Nginx)
- Crear base de datos en servidor
- Subir archivos del proyecto
- Configurar permisos de archivos

#### 5.4 Capacitación
- Crear video tutoriales
- Sección de ayuda interactiva
- Documentación de preguntas frecuentes

## Herramientas Utilizadas

- **Control de Versiones:** Git
- **IDE:** Visual Studio Code
- **Base de Datos:** MySQL
- **Servidor Local:** XAMPP/WAMP
- **Diseño:** Figma (mockups)
- **Documentación:** Markdown

## Cronograma Estimado

| Fase | Duración Estimada |
|------|-------------------|
| Análisis | 4 días |
| Diseño | 6 días |
| Implementación | 15 días |
| Pruebas | 4 días |
| Implantación | 3 días |
| **Total** | **32 días** |
