<?php
/**
 * Modelo: Venta
 * Gestiona las operaciones de ventas y generación de facturas
 * 
 * @package TiendaCalzado\Modelo
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/Producto.php';

class Venta {
    
    private $conexion;
    private $modeloProducto;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
        $this->modeloProducto = new Producto();
    }
    
    /**
     * Registra una nueva venta con sus detalles
     * 
     * @param int $idUsuario ID del usuario que realiza la compra
     * @param array $productos Array de productos con estructura: ['id_producto' => cantidad]
     * @return int|false ID de la venta o false en caso de error
     */
    public function registrarVenta($idUsuario, $productos) {
        try {
            $this->conexion->beginTransaction();
            
            $subtotal = 0;
            $descuentoTotal = 0;
            $detalles = [];
            
            // Validar stock y calcular totales
            foreach ($productos as $idProducto => $cantidad) {
                $producto = $this->modeloProducto->obtenerPorId($idProducto);
                
                if (!$producto) {
                    throw new Exception("Producto no encontrado: ID $idProducto");
                }
                
                if ($producto['stock'] < $cantidad) {
                    throw new Exception("Stock insuficiente para: {$producto['nombre']}. Disponible: {$producto['stock']}");
                }
                
                $precioUnitario = floatval($producto['precio_final']);
                $precioOriginal = floatval($producto['precio']);
                $subtotalProducto = $precioUnitario * $cantidad;
                $descuentoProducto = ($precioOriginal - $precioUnitario) * $cantidad;
                
                $detalles[] = [
                    'id_producto' => $idProducto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotalProducto
                ];
                
                $subtotal += $precioOriginal * $cantidad;
                $descuentoTotal += $descuentoProducto;
            }
            
            $total = $subtotal - $descuentoTotal;
            
            // Insertar venta
            $sqlVenta = "INSERT INTO ventas (id_usuario, subtotal, descuento, total, estado) 
                         VALUES (:id_usuario, :subtotal, :descuento, :total, 'completada')";
            
            $stmt = $this->conexion->prepare($sqlVenta);
            $stmt->execute([
                ':id_usuario' => $idUsuario,
                ':subtotal' => $subtotal,
                ':descuento' => $descuentoTotal,
                ':total' => $total
            ]);
            
            $idVenta = $this->conexion->lastInsertId();
            
            // Insertar detalles de venta y actualizar stock
            $sqlDetalle = "INSERT INTO ventas_detalle (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                           VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)";
            
            $stmtDetalle = $this->conexion->prepare($sqlDetalle);
            
            foreach ($detalles as $detalle) {
                $stmtDetalle->execute([
                    ':id_venta' => $idVenta,
                    ':id_producto' => $detalle['id_producto'],
                    ':cantidad' => $detalle['cantidad'],
                    ':precio_unitario' => $detalle['precio_unitario'],
                    ':subtotal' => $detalle['subtotal']
                ]);
                
                // Actualizar stock
                $this->modeloProducto->reducirStock($detalle['id_producto'], $detalle['cantidad']);
            }
            
            $this->conexion->commit();
            return $idVenta;
            
        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error al registrar venta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las ventas
     * 
     * @return array Lista de ventas
     */
    public function obtenerVentas() {
        try {
            $sql = "SELECT * FROM vista_ventas_detalladas ORDER BY fecha_venta DESC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener ventas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene las ventas de un usuario específico
     * 
     * @param int $idUsuario ID del usuario
     * @return array Lista de ventas del usuario
     */
    public function obtenerPorUsuario($idUsuario) {
        try {
            $sql = "SELECT * FROM ventas WHERE id_usuario = :id_usuario ORDER BY fecha_venta DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener ventas por usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene una venta por su ID con todos los detalles
     * 
     * @param int $idVenta ID de la venta
     * @return array|false Datos completos de la venta o false
     */
    public function obtenerVentaCompleta($idVenta) {
        try {
            // Obtener datos de la venta
            $sqlVenta = "SELECT v.*, u.nombre_completo, u.cedula, u.email, u.telefono, u.direccion
                         FROM ventas v
                         INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                         WHERE v.id_venta = :id";
            
            $stmt = $this->conexion->prepare($sqlVenta);
            $stmt->execute([':id' => $idVenta]);
            $venta = $stmt->fetch();
            
            if (!$venta) {
                return false;
            }
            
            // Obtener detalles de la venta
            $sqlDetalles = "SELECT dv.*, p.nombre AS nombre_producto, p.codigo_producto, p.genero, p.tipo, p.talla
                            FROM ventas_detalle dv
                            INNER JOIN productos p ON dv.id_producto = p.id_producto
                            WHERE dv.id_venta = :id";
            
            $stmt = $this->conexion->prepare($sqlDetalles);
            $stmt->execute([':id' => $idVenta]);
            $venta['detalles'] = $stmt->fetchAll();
            
            return $venta;
            
        } catch (PDOException $e) {
            error_log("Error al obtener venta completa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Genera una factura en formato array
     * 
     * @param int $idVenta ID de la venta
     * @return array|false Datos de la factura o false
     */
    public function generarFactura($idVenta) {
        return $this->obtenerVentaCompleta($idVenta);
    }
    
    /**
     * Actualiza el estado de una venta
     * 
     * @param int $id ID de la venta
     * @param string $estado Nuevo estado (pendiente, completada, cancelada)
     * @return bool True si se actualizó, false en caso contrario
     */
    public function actualizarEstado($id, $estado) {
        try {
            $sql = "UPDATE ventas SET estado = :estado WHERE id_venta = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':estado' => $estado
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar estado de venta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene estadísticas de ventas
     * 
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total de ventas hoy
            $sql = "SELECT COUNT(*) as total, IFNULL(SUM(total), 0) as monto
                    FROM ventas 
                    WHERE DATE(fecha_venta) = CURDATE() AND estado = 'completada'";
            $stmt = $this->conexion->query($sql);
            $stats['hoy'] = $stmt->fetch();
            
            // Total de ventas este mes
            $sql = "SELECT COUNT(*) as total, IFNULL(SUM(total), 0) as monto
                    FROM ventas 
                    WHERE MONTH(fecha_venta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_venta) = YEAR(CURDATE())
                    AND estado = 'completada'";
            $stmt = $this->conexion->query($sql);
            $stats['mes'] = $stmt->fetch();
            
            // Productos más vendidos
            $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido, SUM(dv.subtotal) as ingresos
                    FROM ventas_detalle dv
                    INNER JOIN productos p ON dv.id_producto = p.id_producto
                    INNER JOIN ventas v ON dv.id_venta = v.id_venta
                    WHERE v.estado = 'completada'
                    GROUP BY p.id_producto
                    ORDER BY total_vendido DESC
                    LIMIT 5";
            $stmt = $this->conexion->query($sql);
            $stats['productos_top'] = $stmt->fetchAll();
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
}
?>
