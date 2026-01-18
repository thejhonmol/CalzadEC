<?php
/**
 * Clase de Conexión a Base de Datos
 * Implementa patrón Singleton para conexión PDO
 * Soporta configuración local (XAMPP) y Railway (variables de entorno)
 * 
 * @package TiendaCalzado
 * @author Grupo 4 - Aplicaciones Informáticas I
 */

class Conexion {
    
    // Instancia única de la conexión
    private static $instance = null;
    
    /**
     * Constructor privado para evitar instanciación directa
     */
    private function __construct() {}
    
    /**
     * Obtiene los parámetros de conexión desde variables de entorno o valores por defecto
     */
    private static function getConfig() {
        // Railway proporciona estas variables de entorno automáticamente
        // Si no existen, usa los valores por defecto para desarrollo local (XAMPP)
        return [
            'host' => getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306',
            'dbname' => getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'tienda_calzado',
            'username' => getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root',
            'password' => getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4'
        ];
    }
    
    /**
     * Obtiene la instancia única de la conexión PDO
     * 
     * @return PDO Objeto de conexión a la base de datos
     * @throws PDOException Si hay error en la conexión
     */
    public static function getConexion() {
        if (self::$instance === null) {
            try {
                $config = self::getConfig();
                
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                
                $opciones = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ];
                
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $opciones);
                
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new PDOException("No se pudo conectar a la base de datos. Por favor, intente más tarde.");
            }
        }
        
        return self::$instance;
    }
    
    /**
     * Cierra la conexión a la base de datos
     */
    public static function cerrarConexion() {
        self::$instance = null;
    }
}
?>
