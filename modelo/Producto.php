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
            // 1. Obtener productos básicos con marca
            $sql = "SELECT p.*, m.nombre_marca, p.precio as precio_original 
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    WHERE p.estado = 'activo'
                    ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->conexion->query($sql);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($productos)) return [];
            
            return $this->aplicarPromocionesLote($productos);
            
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
            $sql = "SELECT p.*, m.nombre_marca, p.precio as precio_original 
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    WHERE p.id_producto = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) return false;
            
            $productos = $this->aplicarPromocionesLote([$producto]);
            return $productos[0];
            
        } catch (PDOException $e) {
            error_log("Error al obtener producto: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene productos filtrados por categoría
     */
    public function obtenerPorCategoria($genero = null, $tipo = null, $marca = null) {
        try {
            $sql = "SELECT p.*, m.nombre_marca, p.precio as precio_original 
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
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
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($productos)) return [];
            
            return $this->aplicarPromocionesLote($productos);
            
        } catch (PDOException $e) {
            error_log("Error al obtener productos por categoría: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene solo productos con promoción activa
     * 
     * @return array Lista de productos con descuento
     */
    public function obtenerConPromocion() {
        try {
            // Obtener todos los productos con promociones aplicadas
            $productos = $this->obtenerTodos();
            
            // Filtrar solo los que tienen promoción activa
            $productosConPromocion = array_filter($productos, function($p) {
                return isset($p['tiene_promocion']) && $p['tiene_promocion'] === true && 
                       isset($p['porcentaje_descuento']) && $p['porcentaje_descuento'] > 0;
            });
            
            return array_values($productosConPromocion);
            
        } catch (Exception $e) {
            error_log("Error al obtener productos con promoción: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Aplica las promociones vigentes a una lista de productos en memoria
     * Esto evita duplicados por JOIN y asegura elegir el MEJOR descuento.
     */
    private function aplicarPromocionesLote($productos) {
        try {
            // Obtener todas las promociones activas y vigentes
            $sql = "SELECT * FROM promociones 
                    WHERE activa = 1 
                    AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_fin
                    ORDER BY porcentaje_descuento DESC";
            $stmt = $this->conexion->query($sql);
            $promociones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($productos as &$p) {
                $mejorPromo = null;
                $maxDescuento = 0;
                
                foreach ($promociones as $promo) {
                    $aplica = false;
                    
                    switch ($promo['tipo_aplicacion']) {
                        case 'todos':
                            $aplica = true;
                            break;
                        case 'marca':
                            $aplica = ($promo['id_marca'] == $p['id_marca']);
                            break;
                        case 'genero':
                            $aplica = ($promo['genero'] == $p['genero']);
                            break;
                        case 'tipo':
                            $aplica = ($promo['tipo'] == $p['tipo']);
                            break;
                    }
                    
                    if ($aplica && $promo['porcentaje_descuento'] > $maxDescuento) {
                        $maxDescuento = $promo['porcentaje_descuento'];
                        $mejorPromo = $promo;
                    }
                }
                
                if ($mejorPromo) {
                    $p['tiene_promocion'] = 1;
                    $p['nombre_promocion'] = $mejorPromo['nombre'];
                    $p['porcentaje_descuento'] = $maxDescuento;
                    $p['precio_final'] = round($p['precio'] * (1 - ($maxDescuento / 100)), 2);
                } else {
                    $p['tiene_promocion'] = 0;
                    $p['nombre_promocion'] = null;
                    $p['porcentaje_descuento'] = 0;
                    $p['precio_final'] = $p['precio'];
                }
            }
            
            return $productos;
        } catch (Exception $e) {
            error_log("Error al aplicar promociones en lote: " . $e->getMessage());
            return $productos;
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
            $sql = "SELECT id_producto, nombre, stock, talla, genero 
                    FROM productos 
                    WHERE stock < 10 AND estado = 'activo'
                    ORDER BY stock ASC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error al obtener stock bajo: " . $e->getMessage());
            return [];
        }
    }
}
?>
