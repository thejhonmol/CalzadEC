<?php
/**
 * Modelo: Producto
 * Gestiona las operaciones CRUD para productos de calzado
 * 
 * @package TiendaCalzado\Modelo
 */

require_once __DIR__ . '/../config/conexion.php';

class Producto {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
    }
    
    /**
     * Inserta un nuevo producto en la base de datos
     * 
     * @param array $datos Datos del producto
     * @return int ID del producto insertado o false en caso de error
     */
    public function insertar($datos) {
        try {
            $sql = "INSERT INTO productos (codigo_producto, nombre, descripcion, id_marca, genero, tipo, talla, precio, stock, promocion_id, imagen_url) 
                    VALUES (:codigo, :nombre, :descripcion, :id_marca, :genero, :tipo, :talla, :precio, :stock, :promocion_id, :imagen_url)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':codigo' => $datos['codigo_producto'],
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':id_marca' => $datos['id_marca'],
                ':genero' => $datos['genero'],
                ':tipo' => $datos['tipo'],
                ':talla' => $datos['talla'],
                ':precio' => $datos['precio'],
                ':stock' => $datos['stock'],
                ':promocion_id' => $datos['promocion_id'] ?? null,
                ':imagen_url' => $datos['imagen_url'] ?? null
            ]);
            
            return $this->conexion->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error al insertar producto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todos los productos activos
     * 
     * @return array Lista de productos
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT p.*, m.nombre_marca, pr.nombre AS nombre_promocion, pr.porcentaje_descuento,
                           ROUND(p.precio - (p.precio * IFNULL(pr.porcentaje_descuento, 0) / 100), 2) AS precio_final
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    LEFT JOIN promociones pr ON p.promocion_id = pr.id_promocion 
                        AND pr.activa = TRUE 
                        AND CURRENT_DATE BETWEEN pr.fecha_inicio AND pr.fecha_fin
                    WHERE p.estado = 'activo'
                    ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un producto por su ID
     * 
     * @param int $id ID del producto
     * @return array|false Datos del producto o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT p.*, m.nombre_marca, pr.nombre AS nombre_promocion, pr.porcentaje_descuento,
                           ROUND(p.precio - (p.precio * IFNULL(pr.porcentaje_descuento, 0) / 100), 2) AS precio_final
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    LEFT JOIN promociones pr ON p.promocion_id = pr.id_promocion 
                        AND pr.activa = TRUE 
                        AND CURRENT_DATE BETWEEN pr.fecha_inicio AND pr.fecha_fin
                    WHERE p.id_producto = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener producto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene productos filtrados por categoría
     * 
     * @param string $genero Género del calzado (hombre, mujer, niño) o null para todos
     * @param string $tipo Tipo de calzado (deportivo, no_deportivo) o null para todos
     * @param int $marca ID de la marca o null para todas
     * @return array Lista de productos filtrados
     */
    public function obtenerPorCategoria($genero = null, $tipo = null, $marca = null) {
        try {
            $sql = "SELECT p.*, m.nombre_marca, pr.nombre AS nombre_promocion, pr.porcentaje_descuento,
                           ROUND(p.precio - (p.precio * IFNULL(pr.porcentaje_descuento, 0) / 100), 2) AS precio_final
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    LEFT JOIN promociones pr ON p.promocion_id = pr.id_promocion 
                        AND pr.activa = TRUE 
                        AND CURRENT_DATE BETWEEN pr.fecha_inicio AND pr.fecha_fin
                    WHERE p.estado = 'activo'";
            
            $params = [];
            
            if ($genero !== null) {
                $sql .= " AND p.genero = :genero";
                $params[':genero'] = $genero;
            }
            
            if ($tipo !== null) {
                $sql .= " AND p.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }
            
            if ($marca !== null) {
                $sql .= " AND p.id_marca = :marca";
                $params[':marca'] = $marca;
            }
            
            $sql .= " ORDER BY p.nombre ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener productos por categoría: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualiza un producto existente
     * 
     * @param int $id ID del producto
     * @param array $datos Nuevos datos del producto
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE productos 
                    SET codigo_producto = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        id_marca = :id_marca,
                        genero = :genero,
                        tipo = :tipo,
                        talla = :talla,
                        precio = :precio,
                        stock = :stock,
                        promocion_id = :promocion_id,
                        imagen_url = :imagen_url
                    WHERE id_producto = :id";
            
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':codigo' => $datos['codigo_producto'],
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':id_marca' => $datos['id_marca'],
                ':genero' => $datos['genero'],
                ':tipo' => $datos['tipo'],
                ':talla' => $datos['talla'],
                ':precio' => $datos['precio'],
                ':stock' => $datos['stock'],
                ':promocion_id' => $datos['promocion_id'] ?? null,
                ':imagen_url' => $datos['imagen_url'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina (marca como inactivo) un producto
     * 
     * @param int $id ID del producto
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function eliminar($id) {
        try {
            $sql = "UPDATE productos SET estado = 'inactivo' WHERE id_producto = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza el stock de un producto
     * 
     * @param int $id ID del producto
     * @param int $cantidad Nueva cantidad de stock
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function actualizarStock($id, $cantidad) {
        try {
            $sql = "UPDATE productos SET stock = :cantidad WHERE id_producto = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':cantidad' => $cantidad
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reduce el stock de un producto (usado en ventas)
     * 
     * @param int $id ID del producto
     * @param int $cantidad Cantidad a reducir
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function reducirStock($id, $cantidad) {
        try {
            $sql = "UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id AND stock >= :cantidad_check";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':cantidad' => $cantidad,
                ':cantidad_check' => $cantidad
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al reducir stock: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene productos con stock bajo (menos de 10 unidades)
     * 
     * @return array Lista de productos con stock bajo
     */
    public function obtenerStockBajo() {
        try {
            $sql = "SELECT * FROM vista_stock_bajo";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener stock bajo: " . $e->getMessage());
            return [];
        }
    }
}
?>
