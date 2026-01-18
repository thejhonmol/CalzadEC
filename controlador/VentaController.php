<?php
/**
 * Controlador: Venta
 * Maneja las operaciones de ventas y carritos de compra
 * 
 * @package TiendaCalzado\Controlador
 */

session_start();
require_once __DIR__ . '/../modelo/Venta.php';

class VentaController {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new Venta();
    }
    
    /**
     * Procesa las solicitudes
     */
    public function procesarSolicitud() {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';
        
        switch ($accion) {
            case 'crear':
                $this->crearVenta();
                break;
            case 'listar':
                $this->listarVentas();
                break;
            case 'mis-compras':
                $this->misCompras();
                break;
            case 'factura':
                $this->obtenerFactura();
                break;
            case 'estadisticas':
                $this->obtenerEstadisticas();
                break;
            default:
                $this->responder(['error' => 'Acción no válida'], 400);
        }
    }
    
    /**
     * Crea una nueva venta
     */
    private function crearVenta() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario'])) {
            $this->responder(['error' => 'No autenticado'], 401);
            return;
        }
        
        // Obtener productos del carrito
        $productos = json_decode($_POST['productos'] ?? '[]', true);
        
        if (empty($productos)) {
            $this->responder(['error' => 'Carrito vacío'], 400);
            return;
        }
        
        // Convertir array a formato esperado por el modelo
        $productosFormateados = [];
        foreach ($productos as $item) {
            $productosFormateados[$item['id_producto']] = $item['cantidad'];
        }
        
        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $idVenta = $this->modelo->registrarVenta($idUsuario, $productosFormateados);
        
        if ($idVenta) {
            // Limpiar carrito de la sesión
            unset($_SESSION['carrito']);
            
            $this->responder([
                'success' => true,
                'id_venta' => $idVenta,
                'mensaje' => 'Compra realizada exitosamente'
            ]);
        } else {
            $this->responder(['error' => 'Error al procesar la venta. Verifique el stock disponible.'], 500);
        }
    }
    
    /**
     * Lista todas las ventas (solo admin)
     */
    private function listarVentas() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $ventas = $this->modelo->obtenerVentas();
        $this->responder(['ventas' => $ventas]);
    }
    
    /**
     * Obtiene las compras del usuario actual
     */
    private function misCompras() {
        if (!isset($_SESSION['usuario'])) {
            $this->responder(['error' => 'No autenticado'], 401);
            return;
        }
        
        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $compras = $this->modelo->obtenerPorUsuario($idUsuario);
        $this->responder(['compras' => $compras]);
    }
    
    /**
     * Obtiene la factura de una venta
     */
    private function obtenerFactura() {
        if (!isset($_SESSION['usuario'])) {
            $this->responder(['error' => 'No autenticado'], 401);
            return;
        }
        
        $idVenta = $_GET['id'] ?? null;
        if (!$idVenta) {
            $this->responder(['error' => 'ID de venta requerido'], 400);
            return;
        }
        
        $factura = $this->modelo->generarFactura($idVenta);
        
        if (!$factura) {
            $this->responder(['error' => 'Venta no encontrada'], 404);
            return;
        }
        
        // Verificar que el usuario sea el propietario o admin
        if ($factura['id_usuario'] != $_SESSION['usuario']['id_usuario'] && !$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $this->responder(['factura' => $factura]);
    }
    
    /**
     * Obtiene estadísticas de ventas (solo admin)
     */
    private function obtenerEstadisticas() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $stats = $this->modelo->obtenerEstadisticas();
        $this->responder(['estadisticas' => $stats]);
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
if (basename($_SERVER['PHP_SELF']) === 'VentaController.php') {
    $controller = new VentaController();
    $controller->procesarSolicitud();
}
?>
