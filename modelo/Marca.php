<?php
/**
 * Modelo: Marca
 * Gestiona las operaciones CRUD para marcas de calzado
 * 
 * @package TiendaCalzado\Modelo
 */

require_once __DIR__ . '/../config/conexion.php';

class Marca {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
    }
    
    /**
     * Inserta una nueva marca en la base de datos
     * 
     * @param array $datos Datos de la marca
     * @return int ID de la marca insertada o false en caso de error
     */
    public function insertar($datos) {
        try {
            $sql = "INSERT INTO marcas (nombre_marca, descripcion) 
                    VALUES (:nombre_marca, :descripcion)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre_marca' => $datos['nombre_marca'],
                ':descripcion' => $datos['descripcion'] ?? null
            ]);
            
            return $this->conexion->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error al insertar marca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las marcas activas
     * 
     * @return array Lista de marcas
     */
    public function obtenerTodas() {
        try {
            $sql = "SELECT id_marca, nombre_marca, descripcion, fecha_creacion
                    FROM marcas
                    WHERE estado = 'activo'
                    ORDER BY nombre_marca ASC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener marcas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene una marca por su ID
     * 
     * @param int $id ID de la marca
     * @return array|false Datos de la marca o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id_marca, nombre_marca, descripcion, fecha_creacion, estado
                    FROM marcas
                    WHERE id_marca = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener marca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene una marca por su nombre
     * 
     * @param string $nombre Nombre de la marca
     * @return array|false Datos de la marca o false si no existe
     */
    public function obtenerPorNombre($nombre) {
        try {
            $sql = "SELECT id_marca, nombre_marca, descripcion, fecha_creacion
                    FROM marcas
                    WHERE nombre_marca = :nombre AND estado = 'activo'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener marca por nombre: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza una marca existente
     * 
     * @param int $id ID de la marca
     * @param array $datos Nuevos datos de la marca
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE marcas 
                    SET nombre_marca = :nombre_marca,
                        descripcion = :descripcion
                    WHERE id_marca = :id";
            
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nombre_marca' => $datos['nombre_marca'],
                ':descripcion' => $datos['descripcion'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar marca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina (marca como inactiva) una marca
     * 
     * @param int $id ID de la marca
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function eliminar($id) {
        try {
            $sql = "UPDATE marcas SET estado = 'inactivo' WHERE id_marca = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar marca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si una marca tiene productos asociados
     * 
     * @param int $id ID de la marca
     * @return bool True si tiene productos, false en caso contrario
     */
    public function tieneProductos($id) {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM productos 
                    WHERE id_marca = :id AND estado = 'activo'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            $resultado = $stmt->fetch();
            
            return $resultado['total'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error al verificar productos de marca: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las marcas (incluyendo inactivas) con conteo de productos
     * 
     * @return array Lista de marcas con total de productos
     */
    public function obtenerTodasConProductos() {
        try {
            $sql = "SELECT m.*, 
                           COUNT(p.id_producto) as total_productos
                    FROM marcas m
                    LEFT JOIN productos p ON m.id_marca = p.id_marca AND p.estado = 'activo'
                    GROUP BY m.id_marca
                    ORDER BY m.nombre_marca ASC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener marcas con productos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Activa una marca inactiva
     * 
     * @param int $id ID de la marca
     * @return bool True si se activó correctamente, false en caso contrario
     */
    public function activar($id) {
        try {
            $sql = "UPDATE marcas SET estado = 'activo' WHERE id_marca = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error al activar marca: " . $e->getMessage());
            return false;
        }
    }
}
?>
