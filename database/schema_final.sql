-- =============================================
-- Base de Datos: Tienda de Calzado
-- Arquitectura: MVC
-- Autor: Grupo 4 - Aplicaciones Informáticas I
-- Versión: FINAL - Incluye Sistema de Promociones Segmentadas
-- Fecha Creación: 2026-01-20
-- Última Actualización: 2026-01-17
-- =============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tienda_calzado CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tienda_calzado;

-- =============================================
-- Tabla: usuarios
-- Descripción: Almacena información de administradores y clientes
-- =============================================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) NOT NULL UNIQUE,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(10) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
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
-- Descripción: Catálogo de marcas de calzado
-- =============================================
CREATE TABLE marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    INDEX idx_nombre_marca (nombre_marca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: promociones
-- Descripción: Gestiona descuentos y ofertas especiales segmentadas
-- Actualización: Incluye filtros por marca, género y tipo
-- =============================================
CREATE TABLE promociones (
    id_promocion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    porcentaje_descuento DECIMAL(5,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activa BOOLEAN NOT NULL DEFAULT TRUE,
    tipo_aplicacion ENUM('todos', 'marca', 'genero', 'tipo') NOT NULL DEFAULT 'todos' 
        COMMENT 'Tipo de aplicación: todos (general), marca (por marca), genero (por género), tipo (deportivo/no_deportivo)',
    id_marca INT NULL COMMENT 'ID de marca cuando tipo_aplicacion = marca',
    genero ENUM('hombre', 'mujer', 'niño') NULL COMMENT 'Género cuando tipo_aplicacion = genero',
    tipo ENUM('deportivo', 'no_deportivo') NULL COMMENT 'Tipo de calzado cuando tipo_aplicacion = tipo',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (porcentaje_descuento > 0 AND porcentaje_descuento <= 100),
    CHECK (fecha_fin >= fecha_inicio),
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_promocion_tipo_aplicacion (tipo_aplicacion),
    INDEX idx_promocion_marca (id_marca),
    INDEX idx_promocion_genero (genero),
    INDEX idx_promocion_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: productos
-- Descripción: Catálogo de calzado con clasificaciones y stock
-- =============================================
CREATE TABLE productos (
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
    CHECK (precio > 0),
    CHECK (stock >= 0),
    CHECK (talla > 0),
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (promocion_id) REFERENCES promociones(id_promocion) ON DELETE SET NULL,
    INDEX idx_codigo (codigo_producto),
    INDEX idx_genero (genero),
    INDEX idx_tipo (tipo),
    INDEX idx_talla (talla),
    INDEX idx_producto_marca (id_marca),
    INDEX idx_producto_busqueda (genero, tipo, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: ventas
-- Descripción: Registro de transacciones de venta
-- =============================================
CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_venta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'completada', 'cancelada') NOT NULL DEFAULT 'completada',
    CHECK (subtotal >= 0),
    CHECK (descuento >= 0),
    CHECK (total >= 0),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    INDEX idx_usuario (id_usuario),
    INDEX idx_fecha (fecha_venta),
    INDEX idx_estado (estado),
    INDEX idx_venta_fecha_usuario (fecha_venta, id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabla: ventas_detalle  
-- Descripción: Items específicos de cada venta con tracking de promociones
-- Actualización: Incluye campos para reportes de efectividad
-- =============================================
CREATE TABLE ventas_detalle (
    id_venta_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    id_promocion INT NULL COMMENT 'ID de la promoción aplicada en esta venta (si aplica)',
    descuento_aplicado DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Monto del descuento aplicado en pesos',
    precio_original DECIMAL(10,2) NULL COMMENT 'Precio original antes del descuento',
    CHECK (cantidad > 0),
    CHECK (precio_unitario > 0),
    CHECK (subtotal >= 0),
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT,
    FOREIGN KEY (id_promocion) REFERENCES promociones(id_promocion) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_venta (id_venta),
    INDEX idx_producto (id_producto),
    INDEX idx_venta_detalle_promocion (id_promocion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Datos de Prueba
-- =============================================

-- Insertar usuario administrador
-- Password: admin123
INSERT INTO usuarios (cedula, nombre_completo, email, telefono, direccion, password, rol) VALUES
('1234567890', 'Administrador Sistema', 'admin@tiendacalzado.com', '0987654321', 'Riobamba, Ecuador', '$2y$10$ZSSBzrvQt4nRGsSAUb9aG.sWjd99FfcnJcQ0b5MQlybCpvIvWmB/K', 'admin');

-- Insertar cliente de prueba
-- Password: cliente123
INSERT INTO usuarios (cedula, nombre_completo, email, telefono, direccion, password, rol) VALUES
('0987654321', 'Juan Pérez García', 'juan.perez@email.com', '0998765432', 'Av. Daniel León Borja, Riobamba', '$2y$10$klvm6IcIiqveYfjblfNF1OlHrhYcsCfsN4k5uLmCbWBfHvJJJErIu', 'cliente');

-- Insertar marcas de ejemplo
INSERT INTO marcas (nombre_marca, descripcion) VALUES
('Nike', 'Marca deportiva líder mundial en calzado y ropa deportiva'),
('Adidas', 'Marca alemana de calzado, ropa y accesorios deportivos'),
('Puma', 'Empresa alemana de artículos deportivos'),
('Reebok', 'Marca de calzado y ropa deportiva'),
('Clarks', 'Marca británica especializada en calzado casual y formal'),
('Timberland', 'Marca estadounidense de calzado outdoor y casual'),
('Steve Madden', 'Marca de calzado de moda'),
('Crocs', 'Marca de calzado casual y cómodo'),
('Guess', 'Marca de moda y accesorios'),
('Bata', 'Marca de calzado para toda la familia'),
('Sin Marca', 'Marca genérica para productos sin marca definida');

-- Insertar promociones de ejemplo con filtros
INSERT INTO promociones (nombre, descripcion, porcentaje_descuento, fecha_inicio, fecha_fin, activa, tipo_aplicacion, id_marca, genero, tipo) VALUES
('Descuento General Año Nuevo', 'Promoción especial de inicio de año en todos los productos', 15.00, '2026-01-01', '2026-01-31', TRUE, 'todos', NULL, NULL, NULL),
('Oferta Nike Exclusiva', 'Descuento especial en toda la línea Nike', 25.00, '2026-01-15', '2026-02-28', TRUE, 'marca', 1, NULL, NULL),
('Promo Calzado Deportivo', 'Descuento en todo el calzado deportivo', 20.00, '2026-01-10', '2026-02-10', TRUE, 'tipo', NULL, NULL, 'deportivo'),
('Especial Mujer', 'Promoción exclusiva para calzado de mujer', 18.00, '2026-01-01', '2026-03-08', TRUE, 'genero', NULL, 'mujer', NULL);

-- Insertar productos de ejemplo
INSERT INTO productos (codigo_producto, nombre, descripcion, id_marca, genero, tipo, talla, precio, stock, promocion_id, imagen_url) VALUES
-- Calzado deportivo para hombre
('CAL-H-DEP-001', 'Zapatillas Nike Air Max', 'Zapatillas deportivas para running', 1, 'hombre', 'deportivo', 42.0, 89.99, 15, NULL, 'img/nike-air-max.jpg'),
('CAL-H-DEP-002', 'Adidas Ultraboost', 'Calzado deportivo de alto rendimiento', 2, 'hombre', 'deportivo', 43.0, 120.00, 10, NULL, 'img/adidas-ultraboost.jpg'),
('CAL-H-DEP-003', 'Puma RS-X', 'Zapatillas deportivas estilo urbano', 3, 'hombre', 'deportivo', 41.0, 75.50, 20, NULL, 'img/puma-rsx.jpg'),

-- Calzado no deportivo para hombre
('CAL-H-NODEP-001', 'Zapatos Formales Oxford', 'Zapatos elegantes para ocasiones especiales', 5, 'hombre', 'no_deportivo', 42.0, 65.00, 12, NULL, 'img/oxford-negro.jpg'),
('CAL-H-NODEP-002', 'Mocasines de Cuero', 'Calzado casual de cuero genuino', 6, 'hombre', 'no_deportivo', 43.0, 55.00, 18, NULL, 'img/mocasines.jpg'),

-- Calzado deportivo para mujer
('CAL-M-DEP-001', 'Nike Air Zoom Pegasus', 'Zapatillas para correr ligeras', 1, 'mujer', 'deportivo', 38.0, 95.00, 14, NULL, 'img/nike-pegasus.jpg'),
('CAL-M-DEP-002', 'Reebok Classic', 'Zapatillas deportivas clásicas', 4, 'mujer', 'deportivo', 37.0, 60.00, 25, NULL, 'img/reebok-classic.jpg'),

-- Calzado no deportivo para mujer
('CAL-M-NODEP-001', 'Tacones Elegantes', 'Tacones altos para eventos formales', 7, 'mujer', 'no_deportivo', 37.0, 70.00, 10, NULL, 'img/tacones-negro.jpg'),
('CAL-M-NODEP-002', 'Sandalias de Verano', 'Sandalias cómodas y frescas', 8, 'mujer', 'no_deportivo', 38.0, 35.00, 30, NULL, 'img/sandalias.jpg'),
('CAL-M-NODEP-003', 'Botas de Cuero', 'Botas largas de cuero sintético', 9, 'mujer', 'no_deportivo', 39.0, 85.00, 8, NULL, 'img/botas-cuero.jpg'),

-- Calzado deportivo para niños
('CAL-N-DEP-001', 'Zapatillas Infantiles Nike', 'Zapatillas deportivas para niños', 1, 'niño', 'deportivo', 32.0, 45.00, 20, NULL, 'img/nike-infantil.jpg'),
('CAL-N-DEP-002', 'Adidas Kids Running', 'Calzado deportivo infantil', 2, 'niño', 'deportivo', 30.0, 40.00, 15, NULL, 'img/adidas-kids.jpg'),

-- Calzado no deportivo para niños
('CAL-N-NODEP-001', 'Zapatos Escolares', 'Zapatos formales para uso escolar', 10, 'niño', 'no_deportivo', 33.0, 30.00, 25, NULL, 'img/escolares.jpg'),
('CAL-N-NODEP-002', 'Sandalia Infantil', 'Sandalias cómodas para niños', 8, 'niño', 'no_deportivo', 31.0, 25.00, 18, NULL, 'img/sandalia-infantil.jpg');

-- =============================================
-- Vistas útiles para reportes
-- =============================================

-- Vista de productos con promoción activa (versión mejorada)
CREATE VIEW vista_productos_promocion AS
SELECT 
    p.id_producto,
    p.codigo_producto,
    p.nombre,
    m.nombre_marca,
    p.precio,
    p.stock,
    p.genero,
    p.tipo,
    pr.nombre AS promocion,
    pr.porcentaje_descuento,
    pr.tipo_aplicacion,
    ROUND(p.precio - (p.precio * pr.porcentaje_descuento / 100), 2) AS precio_con_descuento
FROM productos p
INNER JOIN marcas m ON p.id_marca = m.id_marca
LEFT JOIN promociones pr ON 
    (pr.tipo_aplicacion = 'todos' AND pr.activa = TRUE)
    OR (pr.tipo_aplicacion = 'marca' AND pr.id_marca = p.id_marca AND pr.activa = TRUE)
    OR (pr.tipo_aplicacion = 'genero' AND pr.genero = p.genero AND pr.activa = TRUE)
    OR (pr.tipo_aplicacion = 'tipo' AND pr.tipo = p.tipo AND pr.activa = TRUE)
WHERE p.estado = 'activo'
    AND (pr.id_promocion IS NULL OR (CURRENT_DATE BETWEEN pr.fecha_inicio AND pr.fecha_fin));

-- Vista de ventas con detalles de cliente
CREATE VIEW vista_ventas_detalladas AS
SELECT 
    v.id_venta,
    v.fecha_venta,
    u.nombre_completo AS cliente,
    u.cedula,
    v.subtotal,
    v.descuento,
    v.total,
    v.estado
FROM ventas v
INNER JOIN usuarios u ON v.id_usuario = u.id_usuario;

-- Vista de productos con stock bajo (menos de 10 unidades)
CREATE VIEW vista_stock_bajo AS
SELECT 
    p.codigo_producto,
    p.nombre,
    m.nombre_marca,
    p.genero,
    p.tipo,
    p.talla,
    p.stock
FROM productos p
INNER JOIN marcas m ON p.id_marca = m.id_marca
WHERE p.stock < 10 AND p.estado = 'activo'
ORDER BY p.stock ASC;

-- =============================================
-- Triggers
-- =============================================

DELIMITER //

-- Trigger para actualizar stock después de una venta
CREATE TRIGGER tr_actualizar_stock_venta
AFTER INSERT ON ventas_detalle
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET stock = stock - NEW.cantidad
    WHERE id_producto = NEW.id_producto;
END //

-- Trigger para validar stock antes de insertar detalle de venta
CREATE TRIGGER tr_validar_stock
BEFORE INSERT ON ventas_detalle
FOR EACH ROW
BEGIN
    DECLARE v_stock_actual INT;
    
    SELECT stock INTO v_stock_actual
    FROM productos
    WHERE id_producto = NEW.id_producto;
    
    IF v_stock_actual < NEW.cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente para completar la venta';
    END IF;
END //

DELIMITER ;

-- =============================================
-- Permisos y seguridad (Comentado para ajustar en producción)
-- =============================================

-- Crear usuario para la aplicación (cambiar password en producción)
-- CREATE USER 'tienda_app'@'localhost' IDENTIFIED BY 'password_seguro_aqui';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON tienda_calzado.* TO 'tienda_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =============================================
-- FIN DEL SCRIPT
-- =============================================
