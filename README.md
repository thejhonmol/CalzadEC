# 🥾 CalzadEC - Sistema de Gestión de Tienda de Calzado

Sistema web completo para la gestión de una tienda de calzado, desarrollado con arquitectura **MVC en PHP** y **MySQL**.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat&logo=bootstrap&logoColor=white)

---

## ✨ Características Principales

### 🛒 Para Clientes
- Catálogo de productos con filtros por género, tipo y marca
- **Carrusel de Ofertas Destacadas** con navegación interactiva
- **Barra de promociones** con rotación automática cada 5 segundos
- Carrito de compras persistente con cálculo en tiempo real
- Historial de compras y facturas digitales
- Gestión de perfil personal (teléfono, ubicación, dirección)

### ⚙️ Para Administradores
- Panel de control completo con acceso rápido desde navbar
- Gestión CRUD de productos, marcas y promociones
- Sistema de promociones segmentadas (por marca, género o tipo)
- Reportes dinámicos con exportación a PDF
- Control de inventario con alertas de stock bajo
- Visualización de ventas y estadísticas

### 🔐 Seguridad
- Autenticación por roles (Admin/Cliente)
- Validación de cédula ecuatoriana
- Contraseñas hasheadas con bcrypt
- Protección contra SQL injection (PDO prepared statements)

---

## 🚀 Instalación Rápida

### Desarrollo Local (XAMPP)

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/thejhonmol/CalzadEC.git
   cd CalzadEC
   ```

2. **Mover a XAMPP:**
   ```bash
   # Copiar a C:/xampp/htdocs/grupal
   ```

3. **Importar base de datos:**
   - Abrir phpMyAdmin
   - Importar `database/schema_railway.sql`

4. **Acceder:**
   - URL: `http://localhost/grupal`

### Credenciales de Prueba
| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@tiendacalzado.com | admin123 |
| Cliente | juan.perez@email.com | cliente123 |

---

## 🌐 Despliegue en Railway

1. Subir repositorio a GitHub
2. Crear proyecto en [railway.app](https://railway.app)
3. Agregar servicio MySQL
4. Importar `database/schema_railway.sql` en la pestaña Data → Query
5. Generar dominio en Settings → Networking

> Ver guía completa en [`docs/despliegue-railway.md`](docs/despliegue-railway.md)

---

## 📁 Estructura del Proyecto

```
CalzadEC/
├── config/          # Configuración de BD
├── controlador/     # Controladores MVC
├── modelo/          # Modelos de datos
├── vista/
│   ├── admin/       # Panel administrativo
│   ├── cliente/     # Área de clientes
│   └── compartido/  # Vistas compartidas
├── css/             # Estilos (estilos.css, carousel.css)
├── js/              # JavaScript
├── img/             # Imágenes
├── database/        # Scripts SQL
├── docs/            # Documentación
└── index.php        # Punto de entrada
```

---

## 📚 Documentación

| Documento | Descripción |
|-----------|-------------|
| [Objetivos](docs/objetivos.md) | Objetivos del proyecto |
| [Resultados](docs/resultados.md) | Resultados obtenidos |
| [Casos de Uso](docs/casos-de-uso.md) | Diagramas y flujos |
| [Metodología](docs/metodologia.md) | Proceso de desarrollo |
| [Instalación](docs/instalacion.md) | Guía detallada |
| [Despliegue Railway](docs/despliegue-railway.md) | Deploy en la nube |
| [Informe Final](docs/INFORME_FINAL.md) | Documento completo |

---

## 🛠️ Tecnologías

- **Backend:** PHP 8.x, MySQL 8.x
- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Frameworks:** Bootstrap 5.3, FontAwesome 6.4
- **Librerías:** SweetAlert2, jsPDF
- **Arquitectura:** MVC (Modelo-Vista-Controlador)

---

## 👥 Autores

Proyecto desarrollado para el curso de Programación Web.

---

## 📄 Licencia

Este proyecto es de uso académico.
