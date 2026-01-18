<?php
session_start();
require_once __DIR__ . '/../modelo/Venta.php';
require_once __DIR__ . '/../modelo/Producto.php';
require_once __DIR__ . '/../modelo/Cliente.php';

class ReporteController {
    private $modeloVenta;
    private $modeloProducto;
    private $modeloCliente;
    private $conexion;

    public function __construct() {
        $this->modeloVenta = new Venta();
        $this->modeloProducto = new Producto();
        $this->modeloCliente = new Cliente();
        $this->conexion = Conexion::getConexion();
    }

    public function handleRequest() {
        // Verificar admin
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            echo json_encode(['error' => 'No autorizado']);
            return;
        }

        $accion = $_GET['accion'] ?? '';

        switch ($accion) {
            case 'ventas_fecha':
                $this->reporteVentasFecha();
                break;
            case 'productos_top':
                $this->reporteProductosTop();
                break;
            case 'inventario':
                $this->reporteInventario();
                break;
            case 'clientes':
                $this->reporteClientes();
                break;
            default:
                echo json_encode(['error' => 'Acción de reporte no válida']);
                break;
        }
    }

    private function reporteVentasFecha() {
        $fechaInicio = $_GET['inicio'] ?? date('Y-m-d', strtotime('-30 days'));
        $fechaFin = $_GET['fin'] ?? date('Y-m-d');

        try {
            $sql = "SELECT v.*, u.nombre_completo as cliente, u.email
                    FROM ventas v
                    JOIN usuarios u ON v.id_usuario = u.id_usuario
                    WHERE DATE(v.fecha_venta) BETWEEN :inicio AND :fin
                    AND v.estado = 'completada'
                    ORDER BY v.fecha_venta DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':inicio' => $fechaInicio, ':fin' => $fechaFin]);
            $resultados = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $resultados]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function reporteProductosTop() {
        try {
            $sql = "SELECT p.nombre, p.codigo_producto, SUM(dv.cantidad) as total_vendido, SUM(dv.subtotal) as total_ingresos
                    FROM ventas_detalle dv
                    JOIN productos p ON dv.id_producto = p.id_producto
                    JOIN ventas v ON dv.id_venta = v.id_venta
                    WHERE v.estado = 'completada'
                    GROUP BY p.id_producto
                    ORDER BY total_vendido DESC
                    LIMIT 20";
            
            $stmt = $this->conexion->query($sql);
            $resultados = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $resultados]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function reporteInventario() {
        try {
            $sql = "SELECT p.id_producto, p.codigo_producto, p.nombre, p.talla, p.genero, p.stock, p.precio, p.estado
                    FROM productos p
                    ORDER BY p.stock ASC";
            
            $stmt = $this->conexion->query($sql);
            $resultados = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $resultados]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function reporteClientes() {
        try {
            $sql = "SELECT u.id_usuario, u.nombre_completo, u.email, u.telefono, u.fecha_registro,
                           COUNT(v.id_venta) as total_compras,
                           IFNULL(SUM(v.total), 0) as total_gastado
                    FROM usuarios u
                    LEFT JOIN ventas v ON u.id_usuario = v.id_usuario AND v.estado = 'completada'
                    WHERE u.rol = 'cliente'
                    GROUP BY u.id_usuario
                    ORDER BY total_gastado DESC";
            
            $stmt = $this->conexion->query($sql);
            $resultados = $stmt->fetchAll();

            echo json_encode(['success' => true, 'data' => $resultados]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

$controller = new ReporteController();
$controller->handleRequest();
