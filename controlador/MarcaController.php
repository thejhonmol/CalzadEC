<?php
/**
 * Controlador: Marca
 * Maneja las solicitudes HTTP relacionadas con marcas
 * 
 * @package TiendaCalzado\Controlador
 */

session_start();
require_once __DIR__ . '/../modelo/Marca.php';

class MarcaController {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new Marca();
    }
    
    /**
     * Procesa las solicitudes
     */
    public function procesarSolicitud() {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';
        
        switch ($accion) {
            case 'listar':
                $this->listar();
                break;
            case 'listarTodas':
                $this->listarTodas();
                break;
            case 'obtener':
                $this->obtener();
                break;
            case 'crear':
                $this->crear();
                break;
            case 'actualizar':
                $this->actualizar();
                break;
            case 'eliminar':
                $this->eliminar();
                break;
            case 'activar':
                $this->activar();
                break;
            default:
                $this->responder(['error' => 'Acción no válida'], 400);
        }
    }
    
    /**
     * Lista todas las marcas
     */
    private function listar() {
        $marcas = $this->modelo->obtenerTodas();
        $this->responder(['marcas' => $marcas]);
    }
    
    /**
     * Obtiene una marca por su ID
     */
    private function obtener() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de marca requerido'], 400);
            return;
        }
        
        $marca = $this->modelo->obtenerPorId($id);
        
        if ($marca) {
            $this->responder(['marca' => $marca]);
        } else {
            $this->responder(['error' => 'Marca no encontrada'], 404);
        }
    }
    
    /**
     * Crea una nueva marca
     */
    private function crear() {
        // Verificar permisos de administrador
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        // Validar datos
        $errores = $this->validarDatosMarca($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        // Verificar si la marca ya existe
        $marcaExistente = $this->modelo->obtenerPorNombre($_POST['nombre_marca']);
        if ($marcaExistente) {
            $this->responder(['error' => 'Ya existe una marca con ese nombre'], 400);
            return;
        }
        
        $datos = [
            'nombre_marca' => trim($_POST['nombre_marca']),
            'descripcion' => trim($_POST['descripcion'] ?? '')
        ];
        
        $id = $this->modelo->insertar($datos);
        
        if ($id) {
            // Obtener la marca recién creada
            $marcaNueva = $this->modelo->obtenerPorId($id);
            $this->responder([
                'success' => true, 
                'id' => $id, 
                'marca' => $marcaNueva,
                'mensaje' => 'Marca creada exitosamente'
            ]);
        } else {
            $this->responder(['error' => 'Error al crear marca'], 500);
        }
    }
    
    /**
     * Actualiza una marca existente
     */
    private function actualizar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_marca'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de marca requerido'], 400);
            return;
        }
        
        $errores = $this->validarDatosMarca($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        $datos = [
            'nombre_marca' => trim($_POST['nombre_marca']),
            'descripcion' => trim($_POST['descripcion'] ?? '')
        ];
        
        $resultado = $this->modelo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Marca actualizada exitosamente']);
        } else {
            $this->responder(['error' => 'Error al actualizar marca'], 500);
        }
    }
    
    /**
     * Elimina una marca
     */
    private function eliminar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_marca'] ?? $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de marca requerido'], 400);
            return;
        }
        
        // Verificar si la marca tiene productos asociados
        if ($this->modelo->tieneProductos($id)) {
            $this->responder(['error' => 'No se puede eliminar la marca porque tiene productos asociados'], 400);
            return;
        }
        
        $resultado = $this->modelo->eliminar($id);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Marca eliminada exitosamente']);
        } else {
            $this->responder(['error' => 'Error al eliminar marca'], 500);
        }
    }
    
    /**
     * Lista todas las marcas (incluyendo inactivas) con conteo de productos
     */
    private function listarTodas() {
        $marcas = $this->modelo->obtenerTodasConProductos();
        $this->responder(['marcas' => $marcas]);
    }
    
    /**
     * Activa una marca inactiva
     */
    private function activar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_marca'] ?? $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de marca requerido'], 400);
            return;
        }
        
        $resultado = $this->modelo->activar($id);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Marca activada exitosamente']);
        } else {
            $this->responder(['error' => 'Error al activar marca'], 500);
        }
    }
    
    /**
     * Valida los datos de una marca
     */
    private function validarDatosMarca($datos) {
        $errores = [];
        
        if (empty($datos['nombre_marca']) || trim($datos['nombre_marca']) === '') {
            $errores[] = 'Nombre de marca requerido';
        } elseif (strlen(trim($datos['nombre_marca'])) > 100) {
            $errores[] = 'El nombre de marca no puede tener más de 100 caracteres';
        }
        
        return $errores;
    }
    
    /**
     * Verifica si el usuario actual es administrador
     */
    private function esAdmin() {
        return isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'admin';
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
if (basename($_SERVER['PHP_SELF']) === 'MarcaController.php') {
    $controller = new MarcaController();
    $controller->procesarSolicitud();
}
?>
