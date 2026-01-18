<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CalzadEC Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a class="nav-link" href="productos.php"><i class="fas fa-box"></i> Productos</a>
                <a class="nav-link" href="marcas.php"><i class="fas fa-tags"></i> Marcas</a>
                <a class="nav-link" href="promociones.php"><i class="fas fa-percent"></i> Promociones</a>
                <a class="nav-link" href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Ver Sitio</a>
                <a class="nav-link" href="../../controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <h2 class="mb-4">Panel de Administración</h2>
        <p class="text-muted">Bienvenido, <?php echo htmlspecialchars($usuario['nombre_completo']); ?></p>
        
        <!-- Estadísticas -->
        <div class="row mb-4" id="estadisticas">
            <div class="col-md-3">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-label">Ventas Hoy</p>
                            <h3 class="card-value" id="ventas-hoy">0</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-label">Ingresos Hoy</p>
                            <h3 class="card-value" id="ingresos-hoy">$0</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-3x" style="color: var(--secondary-color);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-label">Ventas del Mes</p>
                            <h3 class="card-value" id="ventas-mes">0</h3>
                        </div>
                        <i class="fas fa-chart-line fa-3x" style="color: var(--accent-color);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-label">Stock Bajo</p>
                            <h3 class="card-value" id="stock-bajo">0</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-trophy"></i> Productos Más Vendidos</h5>
                    </div>
                    <div class="card-body">
                        <div id="productos-top">
                            <p class="text-center text-muted">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle"></i> Stock Bajo</h5>
                    </div>
                    <div class="card-body">
                        <div id="productos-stock-bajo">
                            <p class="text-center text-muted">Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
            cargarStockBajo();
        });
        
        function cargarEstadisticas() {
            fetch('../../controlador/VentaController.php?accion=estadisticas')
                .then(response => response.json())
                .then(data => {
                    if (data.estadisticas) {
                        const stats = data.estadisticas;
                        
                        document.getElementById('ventas-hoy').textContent = stats.hoy.total || 0;
                        document.getElementById('ingresos-hoy').textContent = '$' + (parseFloat(stats.hoy.monto) || 0).toFixed(2);
                        document.getElementById('ventas-mes').textContent = stats.mes.total || 0;
                        document.getElementById('stock-bajo').textContent = stats.stock_bajo_count || 0;
                        
                        // Productos más vendidos
                        if (stats.productos_top && stats.productos_top.length > 0) {
                            let html = '<ul class="list-group">';
                            stats.productos_top.forEach((prod, index) => {
                                html += `
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>${index + 1}. ${prod.nombre}</span>
                                        <strong>${prod.total_vendido} vendidos</strong>
                                    </li>
                                `;
                            });
                            html += '</ul>';
                            document.getElementById('productos-top').innerHTML = html;
                        } else {
                            document.getElementById('productos-top').innerHTML = '<p class="text-muted">No hay datos disponibles</p>';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        function cargarStockBajo() {
            fetch('../../controlador/ProductoController.php?accion=stock-bajo')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('stock-bajo').textContent = data.total || 0;
                    
                    if (data.productos && data.productos.length > 0) {
                        let html = '<ul class="list-group">';
                        data.productos.forEach(prod => {
                            html += `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${prod.nombre}</strong><br>
                                        <small class="text-muted">Talla ${prod.talla} - ${prod.genero}</small>
                                    </div>
                                    <span class="badge bg-danger">${prod.stock} unidades</span>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        document.getElementById('productos-stock-bajo').innerHTML = html;
                    } else {
                        document.getElementById('productos-stock-bajo').innerHTML = 
                            '<p class="text-muted text-center"><i class="fas fa-check-circle text-success"></i> Todos los productos tienen stock suficiente</p>';
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Función eliminada a favor de link directo

    </script>
</body>
</html>
