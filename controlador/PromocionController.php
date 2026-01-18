<?php
/**
 * Controlador: Promocion
 * Maneja las solicitudes HTTP relacionadas con promociones
 * 
 * @package TiendaCalzado\Controlador
 */

session_start();
require_once __DIR__ . '/../modelo/Promocion.php';

class PromocionController {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new Promocion();
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
            case 'activas':
                $this->obtenerActivas();
                break;
            case 'contarProductos':
                $this->contarProductos();
                break;
            case 'estadisticas':
                $this->obtenerEstadisticas();
                break;
            case 'reporteEfectividad':
                $this->reporteEfectividad();
                break;
            default:
                $this->responder(['error' => 'Acción no válida'], 400);
        }
    }
    
    /**
     * Lista todas las promociones
     */
    private function listar() {
        $promociones = $this->modelo->obtenerTodas();
        $this->responder(['promociones' => $promociones]);
    }
    
    /**
     * Obtiene promociones activas
     */
    private function obtenerActivas() {
        $promociones = $this->modelo->obtenerActivas();
        $this->responder(['promociones' => $promociones]);
    }
    
    /**
     * Obtiene una promoción por su ID
     */
    private function obtener() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de promoción requerido'], 400);
            return;
        }
        
        $promocion = $this->modelo->obtenerPorId($id);
        
        if ($promocion) {
            $this->responder(['promocion' => $promocion]);
        } else {
            $this->responder(['error' => 'Promoción no encontrada'], 404);
        }
    }
    
    /**
     * Crea una nueva promoción
     */
    private function crear() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $errores = $this->validarDatos($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        $datos = [
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'porcentaje_descuento' => floatval($_POST['porcentaje_descuento']),
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activa' => isset($_POST['activa']) && $_POST['activa'] === '1',
            'tipo_aplicacion' => $_POST['tipo_aplicacion'] ?? 'todos',
            'id_marca' => !empty($_POST['id_marca']) ? intval($_POST['id_marca']) : null,
            'genero' => !empty($_POST['genero']) ? $_POST['genero'] : null,
            'tipo' => !empty($_POST['tipo']) ? $_POST['tipo'] : null
        ];
        
        // Validar existencia de marca/género/tipo
        $errorExistencia = $this->validarExistencia($datos);
        if ($errorExistencia) {
            $this->responder(['error' => $errorExistencia], 400);
            return;
        }
        
        // Verificar promociones duplicadas
        if ($this->modelo->existePromocionDuplicada($datos)) {
            $this->responder(['error' => 'Ya existe una promoción activa con los mismos criterios y fechas superpuestas'], 400);
            return;
        }
        
        // Contar productos afectados
        $totalProductos = $this->modelo->contarProductosAfectados($datos);
        if ($totalProductos == 0) {
            $this->responder(['warning' => true, 'mensaje' => 'Esta promoción no aplicará a ningún producto actualmente', 'total_productos' => 0], 200);
            return;
        }
        
        $id = $this->modelo->insertar($datos);
        
        if ($id) {
            $this->responder([
                'success' => true, 
                'id' => $id, 
                'mensaje' => 'Promoción creada exitosamente',
                'total_productos' => $totalProductos
            ]);
        } else {
            $this->responder(['error' => 'Error al crear promoción'], 500);
        }
    }
    
    /**
     * Actualiza una promoción existente
     */
    private function actualizar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_promocion'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de promoción requerido'], 400);
            return;
        }
        
        $errores = $this->validarDatos($_POST);
        if (!empty($errores)) {
            $this->responder(['error' => 'Datos inválidos', 'detalles' => $errores], 400);
            return;
        }
        
        $datos = [
            'nombre' => trim($_POST['nombre']),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'porcentaje_descuento' => floatval($_POST['porcentaje_descuento']),
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'activa' => isset($_POST['activa']) && $_POST['activa'] === '1',
            'tipo_aplicacion' => $_POST['tipo_aplicacion'] ?? 'todos',
            'id_marca' => !empty($_POST['id_marca']) ? intval($_POST['id_marca']) : null,
            'genero' => !empty($_POST['genero']) ? $_POST['genero'] : null,
            'tipo' => !empty($_POST['tipo']) ? $_POST['tipo'] : null
        ];
        
        $resultado = $this->modelo->actualizar($id, $datos);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Promoción actualizada exitosamente']);
        } else {
            $this->responder(['error' => 'Error al actualizar promoción'], 500);
        }
    }
    
    /**
     * Elimina una promoción
     */
    private function eliminar() {
        if (!$this->esAdmin()) {
            $this->responder(['error' => 'No autorizado'], 403);
            return;
        }
        
        $id = $_POST['id_promocion'] ?? $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(['error' => 'ID de promoción requerido'], 400);
            return;
        }
        
        $resultado = $this->modelo->eliminar($id);
        
        if ($resultado) {
            $this->responder(['success' => true, 'mensaje' => 'Promoción eliminada exitosamente']);
        } else {
            $this->responder(['error' => 'Error al eliminar promoción'], 500);
        }
    }
    
    /**
     * Valida los datos de una promoción
     */
    private function validarDatos($datos) {
        $errores = [];
        
        if (empty($datos['nombre']) || trim($datos['nombre']) === '') {
            $errores[] = 'Nombre de promoción requerido';
        }
        
        if (empty($datos['porcentaje_descuento']) || !is_numeric($datos['porcentaje_descuento'])) {
            $errores[] = 'Porcentaje de descuento requerido';
        } elseif ($datos['porcentaje_descuento'] <= 0 || $datos['porcentaje_descuento'] > 100) {
            $errores[] = 'Porcentaje debe estar entre 1 y 100';
        } else {
            // Límite según rol
            $limiteDescuento = $this->obtenerLimiteDescuento();
            if ($datos['porcentaje_descuento'] > $limiteDescuento) {
                $errores[] = "Su rol solo permite descuentos hasta {$limiteDescuento}%. Contacte a un superior para descuentos mayores.";
            }
        }
        
        if (empty($datos['fecha_inicio'])) {
            $errores[] = 'Fecha de inicio requerida';
        }
        
        if (empty($datos['fecha_fin'])) {
            $errores[] = 'Fecha de fin requerida';
        }
        
        if (!empty($datos['fecha_inicio']) && !empty($datos['fecha_fin'])) {
            if (strtotime($datos['fecha_fin']) < strtotime($datos['fecha_inicio'])) {
                $errores[] = 'La fecha de fin debe ser posterior a la fecha de inicio';
            }
        }
        
        return $errores;
    }
    
    /**
     * Obtiene el límite de descuento según el rol del usuario
     */
    private function obtenerLimiteDescuento() {
        if (!isset($_SESSION['usuario'])) {
            return 0;
        }
        
        $rol = $_SESSION['usuario']['rol'];
        
        // Definir límites por rol
        $limites = [
            'superadmin' => 100,  // Sin límite
            'admin' => 50,        // Hasta 50%
            'gerente' => 30,      // Hasta 30%
            'empleado' => 15      // Hasta 15%
        ];
        
        return $limites[$rol] ?? 10; // Por defecto 10%
    }
    
    /**
     * Cuenta productos afectados por criterios dados
     */
    private function contarProductos() {
        $criterios = [
            'tipo_aplicacion' => $_POST['tipo_aplicacion'] ?? $_GET['tipo_aplicacion'] ?? 'todos',
            'id_marca' => !empty($_POST['id_marca']) ? intval($_POST['id_marca']) : (!empty($_GET['id_marca']) ? intval($_GET['id_marca']) : null),
            'genero' => $_POST['genero'] ?? $_GET['genero'] ?? null,
            'tipo' => $_POST['tipo'] ?? $_GET['tipo'] ?? null
        ];
        
        $total = $this->modelo->contarProductosAfectados($criterios);
        $this->responder(['total' => $total]);
    }
    
    /**
     * Valida existencia de marca/género/tipo
     */
    private function validarExistencia($datos) {
        // Validar marca
        if ($datos['tipo_aplicacion'] === 'marca' && !empty($datos['id_marca'])) {
            require_once __DIR__ . '/../modelo/Marca.php';
            $marcaModelo = new Marca();
            $marca = $marcaModelo->obtenerPorId($datos['id_marca']);
            if (!$marca || $marca['estado'] !== 'activo') {
                return 'La marca seleccionada no existe o está inactiva';
            }
        }
        
        // Validar género
        if ($datos['tipo_aplicacion'] === 'genero' && !empty($datos['genero'])) {
            $generosValidos = ['hombre', 'mujer', 'niño'];
            if (!in_array($datos['genero'], $generosValidos)) {
                return 'Género no válido';
            }
        }
        
        // Validar tipo
        if ($datos['tipo_aplicacion'] === 'tipo' && !empty($datos['tipo'])) {
            $tiposValidos = ['deportivo', 'no_deportivo'];
            if (!in_array($datos['tipo'], $tiposValidos)) {
                return 'Tipo de calzado no válido';
            }
        }
        
        return null;
    }
    
    /**
     * Obtiene estadísticas de promociones
     */
    private function obtenerEstadisticas() {
        $promociones = $this->modelo->obtenerTodas();
        $hoy = date('Y-m-d');
        
        $stats = [
            'activas' => 0,
            'programadas' => 0,
            'expiradas' => 0,
            'inactivas' => 0,
            'total' => count($promociones),
            'por_tipo' => [
                'todos' => 0,
                'marca' => 0,
                'genero' => 0,
                'tipo' => 0
            ],
            'top_productos' => [],
            'productos_total' => 0
        ];
        
        foreach ($promociones as $p) {
            // Clasificar por estado
            if (!$p['activa'] || $p['activa'] === '0') {
                $stats['inactivas']++;
            } elseif ($p['fecha_fin'] < $hoy) {
                $stats['expiradas']++;
            } elseif ($p['fecha_inicio'] > $hoy) {
                $stats['programadas']++;
            } else {
                $stats['activas']++;
            }
            
            // Contar por tipo
            $tipo = $p['tipo_aplicacion'] ?? 'todos';
            $stats['por_tipo'][$tipo]++;
            
            // Sumar productos
            $stats['productos_total'] += (int)($p['total_productos'] ?? 0);
        }
        
        // Top 5 promociones con más productos
        usort($promociones, function($a, $b) {
            return ((int)($b['total_productos'] ?? 0)) - ((int)($a['total_productos'] ?? 0));
        });
        $stats['top_productos'] = array_slice($promociones, 0, 5);
        
        $this->responder($stats);
    }
    
    /**
     * Obtiene reporte de efectividad de promociones
     */
    private function reporteEfectividad() {
        $idPromocion = $_GET['id'] ?? null;
        
        $reporte = $this->modelo->obtenerReporteEfectividad($idPromocion);
        
        $this->responder([
            'success' => true,
            'reportes' => $reporte
        ]);
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
if (basename($_SERVER['PHP_SELF']) === 'PromocionController.php') {
    $controller = new PromocionController();
    $controller->procesarSolicitud();
}
?>
