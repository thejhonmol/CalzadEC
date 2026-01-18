<?php
/**
 * Controlador: Producto
 * Maneja las solicitudes HTTP relacionadas con productos
 * 
 * @package TiendaCalzado\Controlador
 */

session_start();
require_once __DIR__ . '/../modelo/Producto.php';

class ProductoController {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new Producto();
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
            case 'filtrar':
                $this->filtrar();
                break;
            case 'stock-bajo':
                $this->stockBajo();
                break;
            default:
                $this->responder(['error' => 'Acción no válida'], 400);
        }
    }
    
    /**
     * Lista todos los productos
     */
    private function listar() {
        $productos = $this->modelo->obtenerTodos();
        $this->responder(['productos' => $productos]);
    }
    
    /**
     * Crea un nuevo producto
     */
    private function crear() {
        // Verificar permisos de administrador
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        // Validar datos
        $errores = $this->validarDatosProducto($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        $datos = [
            'codigo_producto' => $_POST['codigo_producto'],
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion'] ?? '',
            'id_marca' => $_POST['id_marca'],
            'genero' => $_POST['genero'],
            'tipo' => $_POST['tipo'],
            'talla' => $_POST['talla'],
            'precio' => $_POST['precio'],
            'stock' => $_POST['stock'],
            'promocion_id' => !empty($_POST['promocion_id']) ? $_POST['promocion_id'] : null,
            'imagen_url' => $_POST['imagen_url'] ?? null
        ];
        
        $id = $this->modelo->insertar($datos);
        
        if ($id) {
            $this->responder(['success' => true, 'id' => $id, 'mensaje' => 'Producto creado exitosamente']);
        } else {
            $this->responder(['error' => 'Error al crear producto'], 500);
        }
    }
    
    /**
     * Actualiza un producto existente
     */
    private function actualizar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_producto'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de producto requerido'], 400);
            return;
        }
        
        $errores = $this->validarDatosProducto($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        $datos = [
            'codigo_producto' => $_POST['codigo_producto'],
            'nombre' => $_POST['nombre'],
            'descripcion' => $_POST['descripcion'] ?? '',
            'id_marca' => $_POST['id_marca'],
            'genero' => $_POST['genero'],
            'tipo' => $_POST['tipo'],
            'talla' => $_POST['talla'],
            'precio' => $_POST['precio'],
            'stock' => $_POST['stock'],
            'promocion_id' => !empty($_POST['promocion_id']) ? $_POST['promocion_id'] : null,
            'imagen_url' => $_POST['imagen_url'] ?? null
        ];
        
        $resultado = $this->modelo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Producto actualizado exitosamente']);
        } else {
            $this->responder(['error' => 'Error al actualizar producto'], 500);
        }
    }
    
    /**
     * Elimina un producto
     */
    private function eliminar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_producto'] ?? $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de producto requerido'], 400);
            return;
        }
        
        $resultado = $this->modelo->eliminar($id);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Producto eliminado exitosamente']);
        } else {
            $this->responder(['error' => 'Error al eliminar producto'], 500);
        }
    }
    
    /**
     * Filtra productos por categoría
     */
    private function filtrar() {
        $genero = $_GET['genero'] ?? null;
        $tipo = $_GET['tipo'] ?? null;
        $marca = $_GET['marca'] ?? null;
        
        $productos = $this->modelo->obtenerPorCategoria($genero, $tipo, $marca);
        $this->responder(['productos' => $productos]);
    }
    
    /**
     * Obtiene un producto por su ID
     */
    private function obtener() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de producto requerido'], 400);
            return;
        }
        
        $producto = $this->modelo->obtenerPorId($id);
        
        if ($producto) {
            $this->responder(['producto' => $producto]);
        } else {
            $this->responder(['error' => 'Producto no encontrado'], 404);
        }
    }
    
    /**
     * Obtiene productos con stock bajo
     */
    private function stockBajo() {
        $productos = $this->modelo->obtenerStockBajo();
        $this->responder([
            'productos' => $productos,
            'total' => count($productos)
        ]);
    }
    
    /**
     * Valida los datos de un producto
     */
    private function validarDatosProducto($datos) {
        $errores = [];
        
        if (empty($datos['codigo_producto'])) {
            $errores[] = 'Código de producto requerido';
        }
        
        if (empty($datos['nombre'])) {
            $errores[] = 'Nombre del producto requerido';
        }
        
        if (empty($datos['id_marca']) || !is_numeric($datos['id_marca'])) {
            $errores[] = 'Marca requerida';
        }
        
        if (!in_array($datos['genero'] ?? '', ['hombre', 'mujer', 'niño'])) {
            $errores[] = 'Género inválido';
        }
        
        if (!in_array($datos['tipo'] ?? '', ['deportivo', 'no_deportivo'])) {
            $errores[] = 'Tipo inválido';
        }
        
        if (empty($datos['talla']) || !is_numeric($datos['talla']) || $datos['talla'] <= 0) {
            $errores[] = 'Talla inválida';
        }
        
        if (empty($datos['precio']) || !is_numeric($datos['precio']) || $datos['precio'] <= 0) {
            $errores[] = 'Precio inválido';
        }
        
        if (!isset($datos['stock']) || !is_numeric($datos['stock']) || $datos['stock'] < 0) {
            $errores[] = 'Stock inválido';
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
if (basename($_SERVER['PHP_SELF']) === 'ProductoController.php') {
    $controller = new ProductoController();
    $controller->procesarSolicitud();
}
?>
