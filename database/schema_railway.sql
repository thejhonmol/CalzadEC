-- =============================================
-- Base de Datos: Tienda de Calzado (Railway Version)
-- Arquitectura: MVC
-- Autor: Grupo 4 - Aplicaciones Informáticas I
-- Versión: FINAL - Incluye Sistema de Promociones Segmentadas
-- =============================================

-- Nota: En Railway la base de datos 'railway' ya existe, no necesitamos crearla

-- =============================================
-- Tabla: usuarios
-- Descripción: Almacena información de administradores y clientes
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(10) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    provincia VARCHAR(50) NOT NULL,
    ciudad VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    INDEX idx_cedula (cedula),
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: marcas
-- =============================================
CREATE TABLE IF NOT EXISTS marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    INDEX idx_nombre_marca (nombre_marca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: promociones
-- =============================================
CREATE TABLE IF NOT EXISTS promociones (
    id_promocion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    porcentaje_descuento DECIMAL(5,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activa BOOLEAN NOT NULL DEFAULT TRUE,
    tipo_aplicacion ENUM('todos', 'marca', 'genero', 'tipo') NOT NULL DEFAULT 'todos',
    id_marca INT NULL,
    genero ENUM('hombre', 'mujer', 'niño') NULL,
    tipo ENUM('deportivo', 'no_deportivo') NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_promocion_tipo_aplicacion (tipo_aplicacion),
    INDEX idx_promocion_marca (id_marca),
    INDEX idx_promocion_genero (genero),
    INDEX idx_promocion_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: productos
-- =============================================
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_marca INT NOT NULL,
    genero ENUM('hombre', 'mujer', 'niño') NOT NULL,
    tipo ENUM('deportivo', 'no_deportivo') NOT NULL,
    talla DECIMAL(4,1) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    promocion_id INT NULL,
    imagen_url VARCHAR(255) NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    INDEX idx_codigo (codigo_producto),
    INDEX idx_genero (genero),
    INDEX idx_tipo (tipo),
    INDEX idx_talla (talla),
    INDEX idx_producto_marca (id_marca),
    INDEX idx_producto_busqueda (genero, tipo, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: ventas
-- =============================================
CREATE TABLE IF NOT EXISTS ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_venta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') NOT NULL DEFAULT 'completada',
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_venta),
    INDEX idx_estado (estado),
    INDEX idx_venta_fecha_usuario (fecha_venta, id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: ventas_detalle
-- =============================================
CREATE TABLE IF NOT EXISTS ventas_detalle (
    id_venta_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    id_promocion INT NULL,
    descuento_aplicado DECIMAL(10,2) DEFAULT 0.00,
    precio_original DECIMAL(10,2) NULL,
    INDEX idx_venta (id_venta),
    INDEX idx_producto (id_producto),
    INDEX idx_venta_detalle_promocion (id_promocion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Foreign Keys (después de crear todas las tablas)
-- =============================================
ALTER TABLE promociones ADD CONSTRAINT fk_promocion_marca FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE productos ADD CONSTRAINT fk_producto_marca FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE productos ADD CONSTRAINT fk_producto_promocion FOREIGN KEY (promocion_id) REFERENCES promociones(id_promocion) ON DELETE SET NULL;
ALTER TABLE ventas ADD CONSTRAINT fk_venta_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT;
ALTER TABLE ventas_detalle ADD CONSTRAINT fk_detalle_venta FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE;
ALTER TABLE ventas_detalle ADD CONSTRAINT fk_detalle_producto FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT;
ALTER TABLE ventas_detalle ADD CONSTRAINT fk_detalle_promocion FOREIGN KEY (id_promocion) REFERENCES promociones(id_promocion) ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- Datos de Prueba
-- =============================================

-- Usuario administrador (Password: admin123)
INSERT INTO usuarios (cedula, nombre_completo, email, telefono, direccion, provincia, ciudad, password, rol) VALUES
('1234567890', 'Administrador Sistema', 'admin@tiendacalzado.com', '0987654321', 'Riobamba, Ecuador', 'Chimborazo', 'Riobamba', '$2y$10$ZSSBzrvQt4nRGsSAUb9aG.sWjd99FfcnJcQ0b5MQlybCpvIvWmB/K', 'admin');

-- Cliente de prueba (Password: cliente123)
INSERT INTO usuarios (cedula, nombre_completo, email, telefono, direccion, provincia, ciudad, password, rol) VALUES
('0987654321', 'Juan Pérez García', 'juan.perez@email.com', '0998765432', 'Av. Daniel León Borja, Riobamba', 'Chimborazo', 'Riobamba', '$2y$10$klvm6IcIiqveYfjblfNF1OlHrhYcsCfsN4k5uLmCbWBfHvJJJErIu', 'cliente');

-- Marcas
INSERT INTO marcas (nombre_marca, descripcion) VALUES
('Nike', 'Marca deportiva líder mundial'),
('Adidas', 'Marca alemana de calzado deportivo'),
('Puma', 'Empresa alemana de artículos deportivos'),
('Reebok', 'Marca de calzado deportivo'),
('Clarks', 'Marca británica de calzado casual'),
('Timberland', 'Marca de calzado outdoor'),
('Steve Madden', 'Marca de calzado de moda'),
('Crocs', 'Marca de calzado casual'),
('Guess', 'Marca de moda'),
('Bata', 'Marca de calzado familiar'),
('Sin Marca', 'Marca genérica');

-- Productos de ejemplo
INSERT INTO productos (codigo_producto, nombre, descripcion, id_marca, genero, tipo, talla, precio, stock, imagen_url) VALUES
('CAL-H-DEP-001', 'Zapatillas Nike Air Max', 'Zapatillas deportivas para running', 1, 'hombre', 'deportivo', 42.0, 89.99, 15, 'img/placeholder.svg'),
('CAL-H-DEP-002', 'Adidas Ultraboost', 'Calzado deportivo de alto rendimiento', 2, 'hombre', 'deportivo', 43.0, 120.00, 10, 'img/placeholder.svg'),
('CAL-H-DEP-003', 'Puma RS-X', 'Zapatillas deportivas estilo urbano', 3, 'hombre', 'deportivo', 41.0, 75.50, 20, 'img/placeholder.svg'),
('CAL-H-NODEP-001', 'Zapatos Formales Oxford', 'Zapatos elegantes', 5, 'hombre', 'no_deportivo', 42.0, 65.00, 12, 'img/placeholder.svg'),
('CAL-M-DEP-001', 'Nike Air Zoom Pegasus', 'Zapatillas para correr', 1, 'mujer', 'deportivo', 38.0, 95.00, 14, 'img/placeholder.svg'),
('CAL-M-DEP-002', 'Reebok Classic', 'Zapatillas clásicas', 4, 'mujer', 'deportivo', 37.0, 60.00, 25, 'img/placeholder.svg'),
('CAL-M-NODEP-001', 'Tacones Elegantes', 'Tacones altos para eventos', 7, 'mujer', 'no_deportivo', 37.0, 70.00, 10, 'img/placeholder.svg'),
('CAL-N-DEP-001', 'Zapatillas Infantiles Nike', 'Zapatillas deportivas para niños', 1, 'niño', 'deportivo', 32.0, 45.00, 20, 'img/placeholder.svg'),
('CAL-N-NODEP-001', 'Zapatos Escolares', 'Zapatos formales para uso escolar', 10, 'niño', 'no_deportivo', 33.0, 30.00, 25, 'img/placeholder.svg');

-- =============================================
-- FIN DEL SCRIPT
-- =============================================
