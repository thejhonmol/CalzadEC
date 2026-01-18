# INFORME FINAL
## Sistema de Gestión de Tienda de Calzado con Arquitectura MVC

---

### DATOS GENERALES

**Institución:** Escuela Superior Politécnica de Chimborazo  
**Facultad:** Informática y Electrónica  
**Carrera:** Software  
**Asignatura:** Aplicaciones Informáticas I  
**Código:** SOFIP29  

**Integrantes del Grupo:**
- Jeremy Carrasco
- Alex Chicaiza
- Jhonny Molina
- Francisco Vega

**Tema:** Desarrollo de una Aplicación Web para Tienda de Calzado  
**Práctica No.:** 03  
**Fecha:** Enero 20, 2026

---

## 1. PROBLEMA

Las tiendas de calzado tradicionales en Ecuador enfrentan desafíos significativos en la gestión de inventarios, ventas y atención al cliente, lo que resulta en pérdidas económicas y baja satisfacción del consumidor. La falta de sistemas digitalizados impide el control eficiente del stock, generando problemas de sobrecompra o desabastecimiento de productos. Los procesos manuales de registro de ventas consumen tiempo valioso y son propensos a errores humanos en la facturación. La ausencia de un catálogo en línea limita el alcance de mercado y la capacidad de los clientes para explorar productos según sus necesidades específicas (género, tipo, talla). **Asimismo, la carencia de información geográfica estructurada (provincia/ciudad) dificulta la logística de envíos y el análisis demográfico del mercado.** Finalmente, la gestión de promociones y descuentos se vuelve compleja sin un sistema automatizado que permita aplicar y controlar estas ofertas de manera efectiva.

## 2. JUSTIFICACIÓN

El desarrollo de esta aplicación web se justifica por la necesidad imperante de modernizar la gestión comercial de tiendas de calzado en el contexto ecuatoriano actual. La implementación de un sistema MVC permitirá automatizar procesos críticos como control de inventario, registro de ventas y generación automática de facturas, reduciendo errores y optimizando tiempos operativos. Los beneficios incluyen: mejor control del stock en tiempo real que previene pérdidas por desabastecimiento o sobrestock, acceso 24/7 para clientes que pueden explorar catálogos organizados por género y tipo, reducción de costos operativos mediante la automatización de tareas administrativas, trazabilidad completa de transacciones con facturación electrónica, y capacidad de implementar estrategias de marketing mediante sistema de promociones dinámicas. La validación de datos específicos como cédulas ecuatorianas asegura la integridad de la información y cumplimiento de regulaciones locales.

## 3. OBJETIVOS

### 3.1 Objetivo General

Desarrollar una aplicación web con arquitectura Modelo-Vista-Controlador para la gestión integral de una tienda de calzado, que permita administrar productos, clientes, ventas y promociones de manera eficiente y segura.

### 3.2 Objetivos Específicos

1. Implementar un módulo de gestión de marcas y productos con clasificación por género y tipo
2. Desarrollar un sistema de control de inventario con validación de stock en tiempo real
3. Crear un sistema de autenticación seguro con roles definidos (Admin/Cliente)
4. Integrar validaciones de datos locales, incluyendo algoritmo de cédula ecuatoriana y recolección de ubicación geográfica (Provincia/Ciudad)
5. Diseñar un sistema de promociones flexible que permita descuentos por marca, categoría o género
6. Implementar un carrito de compras interactivo con persistencia y cálculo automático de totales
7. Crear una interfaz de usuario intuitiva y responsiva con sección de ayuda y gestión de perfil personalizada

## 4. METODOLOGÍA

Se utilizó una metodología ágil iterativa basada en el modelo de Prototipado Evolutivo, desarrollando el sistema en ciclos incrementales.

### 4.1Análisis

**Requerimientos Funcionales:**
- Gestión completa de productos (CRUD)
- Clasificación por género: Hombres, Mujeres, Niños
- Clasificación por tipo: Deportivos, No Deportivos
- Control automático de stock
- Sistema de promociones con descuentos
- Validación de cédulas ecuatorianas
- Sistema de autenticación por roles
- Carrito de compras
- Generación de facturas
- Panel administrativo con reportes detallados y exportación PDF
- Gestión de perfil de usuario (Cliente)

### 4.2 Diseño

**Base de Datos:**
- 6 tablas principales: usuarios, productos, promociones, ventas, detalle_ventas, marcas
- Vistas optimizadas para reportes y catálogo
- Triggers para control de stock
- Relaciones con integridad referencial

**Casos de Uso:**
- Gestión de marcas y productos (Administrador)
- Gestión de clientes (Administrador)
- Registro y autenticación
- Gestión de promociones segmentadas (Administrador)
- Navegación de catálogo (Cliente)
- Realizar compra (Cliente)
- Dashboard con estadísticas y centro de reportes (Administrador)
- Gestión de perfil personal (Cliente)
- Exportación de reportes a PDF (Administrador)

**Arquitectura MVC:**
- **Modelo:** Clases PHP (Marca, Producto, Promocion, Venta, Usuario)
- **Vista:** Interfaces HTML5 con Bootstrap y CSS personalizado
- **Controlador:** Scripts PHP que manejan solicitudes HTTP

### 4.3 Implementación

**Tecnologías Utilizadas:**
- **Backend:** PHP 7.4+ con PDO
- **Base de Datos:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (jQuery)
- **Frameworks CSS:** Bootstrap 5.3
- **Librerías:** FontAwesome, SweetAlert2

**Estructura de Archivos:**
```
tienda-calzado/
├── config/         (Conexión BD)
├── modelo/         (Lógica de negocio y acceso a datos)
├── controlador/    (Manejo de peticiones)
├── vista/          (Interfaces de usuario: admin/cliente)
├── css/            (Estilos personalizados)
├── js/             (Lógica frontend: funciones.js, validaciones)
├── img/            (Recursos gráficos y placeholders)
├── database/       (Scripts SQL y migraciones)
└── docs/           (Documentación del proyecto)
```

**Características Implementadas:**
- Sistema de Marcas independiente
- Promociones segmentadas (por marca, género, tipo) con conteo en tiempo real
- Carrito de compras robusto con validación de stock y precios
- Manejo de imágenes resiliente con placeholders SVG
- Interfaz unificada de tarjetas de producto
- Validación de identidad ecuatoriana
- Actualización automática de stock
- Cálculo automático de precios con descuentos
- Generación de facturas en tiempo real
- Sistema de reportes administrativos con visualización dinámica
- Exportación profesional de documentos a PDF
- Interfaz de edición de perfil para el usuario final

### 4.4 Pruebas

Se realizaron pruebas exhaustivas:
- Validación de formularios
- Operaciones CRUD en todos los modelos
- Sistema de ventas con múltiples productos
- Aplicación correcta de promociones
- Control de acceso por roles
- Responsividad en diferentes dispositivos

### 4.5 Implantación

**Documentación Generada:**
- Manual de instalación
- Guía de usuario para clientes
- Guía de administrador
- Sección de ayuda integrada en el sistema
- Documentación técnica del código

**Archivos de Configuración:**
- Script SQL con estructura completa
- Datos de prueba incluidos
- Usuarios predefinidos (admin y cliente)

## 5. RESULTADOS

1. **Aplicación Web Funcional:** Sistema completo con arquitectura MVC, separación clara de responsabilidades y gestión integral de tienda de calzado

2. **Sistema de Inventario Automatizado:** Control de stock en tiempo real, actualización automática con ventas, alertas de stock bajo y prevención de sobreventa

3. **Validación de Datos y Localización:** Validador de cédulas ecuatorianas y sistema de selección obligatoria de Provincia/Ciudad con dropdowns dinámicos para una gestión logística precisa.

4. **Sistemas de Promociones Avanzadas:** Módulo completo de descuentos segmentados por marca, género o tipo, con validación en tiempo real y contador de productos afectados.

5. **Interfaz Moderna y Unificada:** Diseño consistente de tarjetas de productos en catálogo e inicio, con manejo visual de stock (etiquetas, escala de grises para agotados) y experiencia de usuario fluida.

6. **Centro de Reportes Avanzado:** Implementación de 4 reportes estratégicos (Ventas, Productos, Inventario, Clientes) con capacidad de exportación a PDF, permitiendo una gestión basada en datos.

7. **Autogestión de Perfil:** Los clientes cuentan con un espacio para visualizar y actualizar sus datos de contacto (teléfono, celular, ubicación y dirección) de forma segura.

## 6. CONCLUSIONES

1. La arquitectura MVC facilitó la escalabilidad del sistema, permitiendo agregar módulos como el de gestión de marcas y promociones segmentadas sin afectar la lógica existente.

2. La implementación de validaciones en tiempo real y feedback visual (contador de promociones, alertas de stock) mejora significativamente la experiencia del usuario administrador.

3. El sistema de manejo de imágenes con fallbacks automáticos (SVG placeholders) asegura una presentación visual robusta incluso ante errores de carga.

4. La centralización de la lógica de negocio en la base de datos (triggers, vistas) y el uso de controladores especializados para reportes garantiza la integridad de los datos financieros.

5. La integración de librerías como jsPDF permite extender las capacidades del sistema hacia la generación de documentos físicos/digitales portables.

6. La separación de roles y la implementación de permisos específicos protegen las funcionalidades sensibles del sistema.

## 7. RECOMENDACIONES

1. **Implementar módulo de recuperación de contraseña** mediante envío de emails automáticos.

2. **Integrar pasarela de pagos** para permitir transacciones reales en línea.

3. **Integrar gráficos estadísticos** interactivos en el centro de reportes para un análisis visual de tendencias más intuitivo.

4. **Optimizar la carga de imágenes** implementando lazy loading para catálogos extensos.

5. **Configurar HTTPS** para asegurar la transmisión de datos sensibles en producción.

6. **Implementar copias de seguridad automáticas** programadas desde el servidor.

7. **Añadir sistema de comentarios y valoraciones** para fomentar la interacción de los usuarios.

## 8. ANEXOS

### Anexo A: Esquema de Base de Datos
Ver archivo: `database/schema_final.sql`

### Anexo B: Manual de Instalación
Ver archivo: `docs/instalacion.md`

### Anexo C: Capturas de Pantalla
(Las capturas de pantalla se generarán durante la ejecución del sistema)

### Anexo D: Código Fuente
Todos los archivos fuente están disponibles en la carpeta del proyecto con comentarios detallados.

---

**Firma de los Integrantes:**

_______________________________  
Jeremy Carrasco

_______________________________  
Alex Chicaiza

_______________________________  
Jhonny Molina

_______________________________  
Francisco Vega

**Fecha de entrega:** Enero 20, 2026
