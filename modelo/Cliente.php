<?php
/**
 * Modelo: Cliente (Usuario)
 * Gestiona las operaciones CRUD para clientes y usuarios del sistema
 * 
 * @package TiendaCalzado\Modelo
 */

require_once __DIR__ . '/../config/conexion.php';

class Cliente {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = Conexion::getConexion();
    }
    
    /**
     * Valida una cédula ecuatoriana
     * 
     * @param string $cedula Número de cédula a validar
     * @return bool True si es válida, false en caso contrario
     */
    public function validarCedulaEcuatoriana($cedula) {
        // Verificar que tenga 10 dígitos
        if (strlen($cedula) != 10) {
            return false;
        }
        
        // Verificar que solo contenga números
        if (!ctype_digit($cedula)) {
            return false;
        }
        
        // Verificar que los primeros dos dígitos correspondan a una provincia (01-24)
        $provincia = intval(substr($cedula, 0, 2));
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }
        
        // Algoritmo de validación del dígito verificador
        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;
        
        for ($i = 0; $i < 9; $i++) {
            $valor = intval($cedula[$i]) * $coeficientes[$i];
            if ($valor > 9) {
                $valor -= 9;
            }
            $suma += $valor;
        }
        
        $digitoVerificador = (10 - ($suma % 10)) % 10;
        
        return $digitoVerificador == intval($cedula[9]);
    }
    
    /**
     * Registra un nuevo cliente
     * 
     * @param array $datos Datos del cliente
     * @return int|false ID del cliente insertado o false en caso de error
     */
    public function registrar($datos) {
        try {
            // Validar cédula
            if (!$this->validarCedulaEcuatoriana($datos['cedula'])) {
                throw new Exception("Cédula ecuatoriana no válida");
            }
            
            // Verificar si la cédula ya está registrada
            if ($this->existeCedula($datos['cedula'])) {
                throw new Exception("La cédula ya está registrada");
            }
            
            // Verificar si el email ya está registrado
            if ($this->existeEmail($datos['email'])) {
                throw new Exception("El email ya está registrado");
            }
            
            // Hash del password
            $passwordHash = password_hash($datos['password'], PASSWORD_BCRYPT);
            
            $sql = "INSERT INTO usuarios (cedula, nombre_completo, email, telefono, direccion, provincia, ciudad, password, rol) 
                    VALUES (:cedula, :nombre, :email, :telefono, :direccion, :provincia, :ciudad, :password, :rol)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':cedula' => $datos['cedula'],
                ':nombre' => $datos['nombre_completo'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':direccion' => $datos['direccion'],
                ':provincia' => $datos['provincia'],
                ':ciudad' => $datos['ciudad'],
                ':password' => $passwordHash,
                ':rol' => $datos['rol'] ?? 'cliente'
            ]);
            
            return $this->conexion->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Error al registrar cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si una cédula ya está registrada
     * 
     * @param string $cedula Número de cédula
     * @return bool True si existe, false en caso contrario
     */
    private function existeCedula($cedula) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE cedula = :cedula";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verifica si un email ya está registrado
     * 
     * @param string $email Email a verificar
     * @return bool True si existe, false en caso contrario
     */
    private function existeEmail($email) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtiene todos los clientes activos
     * 
     * @return array Lista de clientes
     */
    public function obtenerTodos() {
        try {
            $sql = "SELECT id_usuario, cedula, nombre_completo, email, telefono, direccion, provincia, ciudad, rol, fecha_registro, estado 
                    FROM usuarios 
                    WHERE estado = 'activo' 
                    ORDER BY fecha_registro DESC";
            
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un cliente por su ID
     * 
     * @param int $id ID del cliente
     * @return array|false Datos del cliente o false si no existe
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT id_usuario, cedula, nombre_completo, email, telefono, direccion, provincia, ciudad, rol, fecha_registro, estado 
                    FROM usuarios 
                    WHERE id_usuario = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene un cliente por su cédula
     * 
     * @param string $cedula Cédula del cliente
     * @return array|false Datos del cliente o false si no existe
     */
    public function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT * FROM usuarios WHERE cedula = :cedula";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':cedula' => $cedula]);
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Error al obtener cliente por cédula: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza los datos de un cliente
     * 
     * @param int $id ID del cliente
     * @param array $datos Nuevos datos del cliente
     * @return bool True si se actualizó correctamente, false en caso contrario
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE usuarios 
                    SET nombre_completo = :nombre,
                        email = :email,
                        telefono = :telefono,
                        direccion = :direccion,
                        provincia = :provincia,
                        ciudad = :ciudad
                    WHERE id_usuario = :id";
            
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':nombre' => $datos['nombre_completo'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':direccion' => $datos['direccion'],
                ':provincia' => $datos['provincia'],
                ':ciudad' => $datos['ciudad']
            ]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina (marca como inactivo) un cliente
     * 
     * @param int $id ID del cliente
     * @return bool True si se eliminó correctamente, false en caso contrario
     */
    public function eliminar($id) {
        try {
            $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id_usuario = :id";
            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Error al eliminar cliente: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Autentica un usuario
     * 
     * @param string $credencial Cédula o email
     * @param string $password Contraseña
     * @return array|false Datos del usuario o false si la autenticación falla
     */
    public function autenticar($credencial, $password) {
        try {
            $sql = "SELECT * FROM usuarios 
                    WHERE (cedula = :cedula OR email = :email) 
                    AND estado = 'activo'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':cedula' => $credencial,
                ':email' => $credencial
            ]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // No devolver el password
                unset($usuario['password']);
                return $usuario;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Error al autenticar: " . $e->getMessage());
            return false;
        }
    }
}
?>
