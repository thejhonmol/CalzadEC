<?php
/**
 * Controlador: Autenticación
 * Maneja login, logout y registro de usuarios
 * 
 * @package TiendaCalzado\Controlador
 */

session_start();
require_once __DIR__ . '/../modelo/Cliente.php';

class AuthController {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new Cliente();
    }
    
    /**
     * Procesa login de usuario
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responder(['error' => 'Método no permitido'], 405);
            return;
        }
        
        $credencial = $_POST['credencial'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($credencial) || empty($password)) {
            $this->responder(['error' => 'Credenciales incompletas'], 400);
            return;
        }
        
        $usuario = $this->modelo->autenticar($credencial, $password);
        
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['ultimo_acceso'] = time();
            
            // Ruta absoluta desde la raíz del proyecto
            $baseUrl = dirname(dirname($_SERVER['PHP_SELF']));
            $redireccion = $usuario['rol'] === 'admin' 
                ? $baseUrl . '/vista/admin/dashboard.php' 
                : $baseUrl . '/vista/cliente/catalogo.php';
            
            $this->responder([
                'success' => true,
                'mensaje' => 'Login exitoso',
                'usuario' => [
                    'nombre' => $usuario['nombre_completo'],
                    'rol' => $usuario['rol']
                ],
                'redireccion' => $redireccion
            ]);
        } else {
            $this->responder(['error' => 'Credenciales inválidas'], 401);
        }
    }
    
    /**
     * Procesa registro de nuevo usuario
     */
    public function registro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responder(['error' => 'Método no permitido'], 405);
            return;
        }
        
        // Validar datos
        $errores = $this->validarDatosRegistro($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        // Validar cédula
        if (!$this->modelo->validarCedulaEcuatoriana($_POST['cedula'])) {
            $this->responder(['error' => 'Cédula ecuatoriana inválida'], 400);
            return;
        }
        
        $datos = [
            'cedula' => $_POST['cedula'],
            'nombre_completo' => $_POST['nombre_completo'],
            'email' => $_POST['email'],
            'telefono' => $_POST['telefono'],
            'direccion' => $_POST['direccion'],
            'password' => $_POST['password'],
            'rol' => 'cliente' // Los nuevos registros siempre son clientes
        ];
        
        $id = $this->modelo->registrar($datos);
        
        if ($id) {
            // Autologin después del registro
            $usuario = $this->modelo->obtenerPorId($id);
            $_SESSION['usuario'] = $usuario;
            $_SESSION['ultimo_acceso'] = time();
            
            // Ruta absoluta desde la raíz del proyecto
            $baseUrl = dirname(dirname($_SERVER['PHP_SELF']));
            
            $this->responder([
                'success' => true,
                'mensaje' => 'Registro exitoso',
                'redireccion' => $baseUrl . '/vista/cliente/catalogo.php'
            ]);
        } else {
            $this->responder(['error' => 'Error al registrar usuario. Verifique que la cédula y email no estén registrados.'], 500);
        }
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_unset();
        session_destroy();
        
        // Redirigir al inicio después de cerrar sesión
        $baseUrl = dirname(dirname($_SERVER['PHP_SELF']));
        header("Location: " . $baseUrl . "/index.php");
        exit;
    }
    
    /**
     * Verifica si hay una sesión activa
     */
    public function verificarSesion() {
        if (isset($_SESSION['usuario'])) {
            $this->responder([
                'autenticado' => true,
                'usuario' => [
                    'id' => $_SESSION['usuario']['id_usuario'],
                    'nombre' => $_SESSION['usuario']['nombre_completo'],
                    'rol' => $_SESSION['usuario']['rol']
                ]
            ]);
        } else {
            $this->responder(['autenticado' => false]);
        }
    }
    
    /**
     * Valida los datos de registro
     */
    private function validarDatosRegistro($datos) {
        $errores = [];
        
        if (empty($datos['cedula']) || strlen($datos['cedula']) != 10) {
            $errores[] = 'Cédula debe tener 10 dígitos';
        }
        
        if (empty($datos['nombre_completo'])) {
            $errores[] = 'Nombre completo requerido';
        }
        
        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Email inválido';
        }
        
        if (empty($datos['telefono']) || strlen($datos['telefono']) != 10) {
            $errores[] = 'Teléfono debe tener 10 dígitos';
        }
        
        if (empty($datos['direccion'])) {
            $errores[] = 'Dirección requerida';
        }
        
        if (empty($datos['password']) || strlen($datos['password']) < 6) {
            $errores[] = 'Contraseña debe tener al menos 6 caracteres';
        }
        
        if ($datos['password'] !== ($datos['password_confirm'] ?? '')) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        return $errores;
    }
    
    /**
     * Envía una respuesta JSON
     */
    private function responder($datos, $codigo = 200) {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }
}

// Procesar solicitud si se llama directamente
if (basename($_SERVER['PHP_SELF']) === 'AuthController.php') {
    $controller = new AuthController();
    $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'verificar';
    
    switch ($accion) {
        case 'login':
            $controller->login();
            break;
        case 'registro':
            $controller->registro();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'verificar':
            $controller->verificarSesion();
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
    }
}
?>
