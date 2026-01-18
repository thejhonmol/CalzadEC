<?php
/**
 * Modelo: Promocion
 * Gestiona las operaciones CRUD para promociones
 * 
 * @package TiendaCalzado\Modelo
 */

require_once __DIR__ . '/../config/conexion.php';

class Promocion {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
    }
    
    /**
     * Inserta una nueva promoción
     * 
     * @param array $datos Datos de la promoción
     * @return int|false ID de la promoción insertada o false
     */
    public function insertar($datos) {
        try {
            $sql = "INSERT INTO promociones (nombre, descripcion, porcentaje_descuento, fecha_inicio, fecha_fin, activa, tipo_aplicacion, id_marca, genero, tipo) 
                    VALUES (:nombre, :descripcion, :porcentaje, :fecha_inicio, :fecha_fin, :activa, :tipo_aplicacion, :id_marca, :genero, :tipo)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':porcentaje' => $datos['porcentaje_descuento'],
                ':fecha_inicio' => $datos['fecha_inicio'],
                ':fecha_fin' => $datos['fecha_fin'],
                ':activa' => $datos['activa'] ?? true,
                ':tipo_aplicacion' => $datos['tipo_aplicacion'] ?? 'todos',
                ':id_marca' => $datos['id_marca'] ?? null,
                ':genero' => $datos['genero'] ?? null,
                ':tipo' => $datos['tipo'] ?? null
            ]);
            
            return $this->conexion->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error al insertar promoción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las promociones
     * 
     * @return array Lista de promociones
     */
    public function obtenerTodas() {
        try {
            $sql = "SELECT p.*, m.nombre_marca,
                    (SELECT COUNT(*) FROM productos pr 
                     WHERE pr.estado = 'activo' AND (
                         p.tipo_aplicacion = 'todos' OR
                         (p.tipo_aplicacion = 'marca' AND pr.id_marca = p.id_marca) OR
                         (p.tipo_aplicacion = 'genero' AND pr.genero = p.genero) OR
                         (p.tipo_aplicacion = 'tipo' AND pr.tipo = p.tipo)
                     )) as total_productos
                    FROM promociones p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    ORDER BY p.fecha_creacion DESC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener promociones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene promociones activas vigentes
     * 
     * @return array Lista de promociones activas
     */
    public function obtenerActivas() {
        try {
            $sql = "SELECT * FROM promociones 
                    WHERE activa = TRUE 
                    AND CURRENT_DATE BETWEEN fecha_inicio AND fecha_fin
                    ORDER BY porcentaje_descuento DESC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener promociones activas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualiza una promoción
     * 
     * @param int $id ID de la promoción
     * @param array $datos Nuevos datos
     * @return bool True si se actualizó, false en caso contrario
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE promociones 
                    SET nombre = :nombre,
                        descripcion = :descripcion,
                        porcentaje_descuento = :porcentaje,
                        fecha_inicio = :fecha_inicio,
                        fecha_fin = :fecha_fin,
                        activa = :activa,
                        tipo_aplicacion = :tipo_aplicacion,
                        id_marca = :id_marca,
                        genero = :genero,
                        tipo = :tipo
                    WHERE id_promocion = :id";
            
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nombre' => $datos['nombre'],
                ':descripcion' => $datos['descripcion'],
                ':porcentaje' => $datos['porcentaje_descuento'],
                ':fecha_inicio' => $datos['fecha_inicio'],
                ':fecha_fin' => $datos['fecha_fin'],
                ':activa' => $datos['activa'],
                ':tipo_aplicacion' => $datos['tipo_aplicacion'] ?? 'todos',
                ':id_marca' => $datos['id_marca'] ?? null,
                ':genero' => $datos['genero'] ?? null,
                ':tipo' => $datos['tipo'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar promoción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene una promoción por su ID
     * 
     * @param int $id ID de la promoción
     * @return array|false Datos de la promoción o false
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM promociones WHERE id_promocion = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener promoción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina una promoción
     * 
     * @param int $id ID de la promoción
     * @return bool True si se eliminó, false en caso contrario
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM promociones WHERE id_promocion = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar promoción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cuenta productos afectados por una promoción
     * 
     * @param array $criterios Criterios de la promoción
     * @return int Cantidad de productos
     */
    public function contarProductosAfectados($criterios) {
        try {
            $sql = "SELECT COUNT(*) as total FROM productos WHERE estado = 'activo'";
            $params = [];
            
            if ($criterios['tipo_aplicacion'] === 'marca' && !empty($criterios['id_marca'])) {
                $sql .= " AND id_marca = :id_marca";
                $params[':id_marca'] = $criterios['id_marca'];
            } elseif ($criterios['tipo_aplicacion'] === 'genero' && !empty($criterios['genero'])) {
                $sql .= " AND genero = :genero";
                $params[':genero'] = $criterios['genero'];
            } elseif ($criterios['tipo_aplicacion'] === 'tipo' && !empty($criterios['tipo'])) {
                $sql .= " AND tipo = :tipo";
                $params[':tipo'] = $criterios['tipo'];
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch();
            return (int)$resultado['total'];
            
        } catch (PDOException $e) {
            error_log("Error al contar productos: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Verifica si existe una promoción duplicada activa
     * 
     * @param array $datos Datos de la promoción
     * @param int|null $idExcluir ID a excluir (para edición)
     * @return bool True si hay duplicado
     */
    public function existePromocionDuplicada($datos, $idExcluir = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM promociones 
                    WHERE activa = 1 
                    AND tipo_aplicacion = :tipo_aplicacion
                    AND (
                        (fecha_inicio BETWEEN :fecha_inicio AND :fecha_fin) OR
                        (fecha_fin BETWEEN :fecha_inicio AND :fecha_fin) OR
                        (:fecha_inicio BETWEEN fecha_inicio AND fecha_fin) OR
                        (:fecha_fin BETWEEN fecha_inicio AND fecha_fin)
                    )";
            
            $params = [
                ':tipo_aplicacion' => $datos['tipo_aplicacion'],
                ':fecha_inicio' => $datos['fecha_inicio'],
                ':fecha_fin' => $datos['fecha_fin']
            ];
            
            // Agregar condiciones específicas según tipo
            if ($datos['tipo_aplicacion'] === 'marca' && !empty($datos['id_marca'])) {
                $sql .= " AND id_marca = :id_marca";
                $params[':id_marca'] = $datos['id_marca'];
            } elseif ($datos['tipo_aplicacion'] === 'genero' && !empty($datos['genero'])) {
                $sql .= " AND genero = :genero";
                $params[':genero'] = $datos['genero'];
            } elseif ($datos['tipo_aplicacion'] === 'tipo' && !empty($datos['tipo'])) {
                $sql .= " AND tipo = :tipo";
                $params[':tipo'] = $datos['tipo'];
            }
            
            if ($idExcluir) {
                $sql .= " AND id_promocion != :id_excluir";
                $params[':id_excluir'] = $idExcluir;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch();
            
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar duplicados: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene la mejor promoción para un producto
     * 
     * @param int $idProducto ID del producto
     * @return array|false Datos de la mejor promoción o false
     */
    public function obtenerMejorPromocion($idProducto) {
        try {
            $sql = "SELECT pr.* 
                    FROM promociones pr
                    LEFT JOIN productos p ON (
                        pr.tipo_aplicacion = 'todos' OR
                        (pr.tipo_aplicacion = 'marca' AND pr.id_marca = p.id_marca) OR
                        (pr.tipo_aplicacion = 'genero' AND pr.genero = p.genero) OR
                        (pr.tipo_aplicacion = 'tipo' AND pr.tipo = p.tipo)
                    )
                    WHERE p.id_producto = :id_producto
                    AND pr.activa = 1
                    AND CURRENT_DATE BETWEEN pr.fecha_inicio AND pr.fecha_fin
                    ORDER BY pr.porcentaje_descuento DESC
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_producto' => $idProducto]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener mejor promoción: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene reporte de efectividad de una promoción
     * 
     * @param int|null $idPromocion ID de promoción específica o null para todas
     * @return array Datos del reporte
     */
    public function obtenerReporteEfectividad($idPromocion = null) {
        try {
            $sql = "SELECT 
                        p.id_promocion,
                        p.nombre,
                        p.porcentaje_descuento,
                        p.tipo_aplicacion,
                        COUNT(DISTINCT vd.id_venta) as total_ventas,
                        COUNT(vd.id_venta_detalle) as items_vendidos,
                        SUM(vd.cantidad) as productos_vendidos,
                        SUM(vd.descuento_aplicado) as descuento_total_otorgado,
                        SUM(vd.subtotal) as revenue_generado,
                        AVG(vd.descuento_aplicado) as descuento_promedio
                    FROM promociones p
                    LEFT JOIN ventas_detalle vd ON vd.id_promocion = p.id_promocion
                    WHERE 1=1";
            
            $params = [];
            
            if ($idPromocion) {
                $sql .= " AND p.id_promocion = :id_promocion";
                $params[':id_promocion'] = $idPromocion;
            }
            
            $sql .= " GROUP BY p.id_promocion
                      ORDER BY total_ventas DESC, descuento_total_otorgado DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            
            $resultados = $stmt->fetchAll();
            
            // Calcular métricas adicionales
            foreach ($resultados as &$resultado) {
                $resultado['tiene_ventas'] = ($resultado['total_ventas'] > 0);
                $resultado['roi'] = $this->calcularROI($resultado);
            }
            
            return $resultados;
            
        } catch (PDOException $e) {
            error_log("Error al obtener reporte: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calcula el ROI de una promoción
     * ROI = ((Revenue - Descuento) / Descuento) * 100
     */
    private function calcularROI($datos) {
        if ($datos['descuento_total_otorgado'] == 0) {
            return 0;
        }
        
        $roi = (($datos['revenue_generado'] - $datos['descuento_total_otorgado']) / 
                $datos['descuento_total_otorgado']) * 100;
        
        return round($roi, 2);
    }
}
?>
