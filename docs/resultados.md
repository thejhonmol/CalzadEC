# Resultados del Proyecto - Tienda de Calzado MVC

## Resultados Obtenidos

1. **Aplicación Web Funcional con Arquitectura MVC**
   - Se desarrollo exitosamente una aplicación web completa siguiendo el patrón Modelo-Vista-Controlador, con separación clara de responsabilidades entre las capas de presentación, lógica de negocio y acceso a datos. El sistema permite una gestión integral de una tienda de calzado, incluyendo productos, clientes, ventas y promociones, con interfaces diferenciadas para administradores y clientes.

2. **Sistema de Gestión de Inventario Automatizado**
   - Se implementó un sistema robusto de control de stock que se actualiza automáticamente al realizar ventas, previene la sobreventa mediante validaciones en tiempo real, genera alertas cuando el inventario es bajo (menos de 10 unidades), y mantiene un historial completo de todas las transacciones. El sistema incluye triggers en la base de datos que garantizan la integridad de los datos de inventario.

3. **Validación de Datos y Localización Ecuatoriana**
   - Se desarrolló un validador de cédulas ecuatorianas que verifica el formato de 10 dígitos y códigos provinciales. Además, se implementó un sistema de recolección de datos geográficos (Provincia y Ciudad) mediante dropdowns dinámicos integrados con una base de datos de regiones de Ecuador, garantizando que todos los registros de usuarios tengan información de ubicación estructurada y válida.

4. **Sistema de Promociones Segmentadas y Facturación**
   - Se creó un módulo avanzado de promociones que permite definir descuentos por **Marca, Género o Tipo**, con un contador en tiempo real de productos afectados. Las promociones se aplican automáticamente en el catálogo (badges, precio anterior/nuevo) y en el carrito de compras. El sistema genera facturas digitales instantáneas con el cálculo detallado de los descuentos aplicados.

5. **Interfaz de Usuario Unificada y Resiliente**
   - Se implementó un diseño de tarjetas de producto consistente entre la página de inicio y el catálogo, con manejo visual de stock (etiquetas "Pocas unidades", "Agotado") y efectos de interacción. Se incorporó un sistema de **placeholders SVG** para asegurar que la interfaz nunca se rompa, incluso si faltan imágenes de productos, garantizando una experiencia de usuario fluida y profesional.

6. **Gestión Integral de Marcas**
   - Se desarrolló un módulo completo para la gestión de marcas de calzado, permitiendo al administrador crear y administrar el catálogo de marcas, las cuales se vinculan dinámicamente tanto a los productos como a las reglas de promociones.

7. **Sistema de Reportes y Exportación PDF**
   - Se implementó un centro de reportes administrativos con 4 tipos de informes: Ventas por Fecha, Top Productos, Inventario y Resumen de Clientes. Estos reportes incluyen visualización dinámica en tablas y la capacidad de exportación profesional a documentos PDF mediante la librería jsPDF, facilitando la auditoría y análisis del negocio.

8. **Centro de Autogestión de Perfil**
   - Se desarrolló un módulo de perfil para clientes que permite visualizar sus datos informativos y editar de forma segura su número de teléfono, ubicación (provincia y ciudad) y dirección de domicilio. El sistema actualiza automáticamente la sesión del usuario para reflejar los cambios de inmediato sin necesidad de re-autenticación.

9. **Sistema Visual de Promociones Destacadas**
   - Se implementó una experiencia de usuario enriquecida para promociones que incluye: una barra superior con **rotación automática cada 5 segundos** entre promociones activas, una sección "Ofertas Destacadas" con **carrusel horizontal interactivo** y flechas de navegación, badges con nombre de promoción y porcentaje de descuento en cada tarjeta de producto, y un botón de **acceso rápido al Panel Admin** visible únicamente para administradores en la barra de navegación.

## Características Técnicas Destacadas

- **Base de Datos:** MySQL con 5 tablas relacionales, vistas optimizadas, triggers para integridad de datos, e índices para mejor rendimiento
- **Seguridad:** Uso de PDO con prepared statements para prevenir SQL injection, contraseñas hasheadas con bcrypt, validación tanto en cliente como en servidor, y control de sesiones y permisos por roles
- **Arquitectura:** Patrón MVC implementado correctamente, separación de responsabilidades, código reutilizable y mantenible, y uso de patrón Singleton para conexión a base de datos
