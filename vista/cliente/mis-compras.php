<?php
session_start();

if (!isset($_SESSION['usuario'])) {
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
    <title>Mis Compras - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
                <a class="nav-link active" href="mis-compras.php"><i class="fas fa-history"></i> Mis Compras</a>
                <a class="nav-link" href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-history"></i> Mi Historial de Compras</h2>
        
        <div id="compras-contenedor">
            <div class="text-center">
                <div class="spinner-custom mx-auto"></div>
                <p class="mt-3 text-muted">Cargando compras...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cargarCompras();
        });
        
        function cargarCompras() {
            fetch('../../controlador/VentaController.php?accion=mis-compras')
                .then(response => response.json())
                .then(data => {
                    if (data.compras) {
                        mostrarCompras(data.compras);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('compras-contenedor').innerHTML = 
                        '<div class="alert alert-danger">Error al cargar las compras</div>';
                });
        }
        
        function mostrarCompras(compras) {
            const contenedor = document.getElementById('compras-contenedor');
            
            if (compras.length === 0) {
                contenedor.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-5x text-muted mb-3"></i>
                        <h4>No tienes compras registradas</h4>
                        <p class="text-muted">¡Comienza a comprar en nuestro catálogo!</p>
                        <a href="catalogo.php" class="btn btn-primary-custom">Ver Catálogo</a>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="table-custom"><table class="table"><thead><tr>';
            html += '<th>Fecha</th><th>Subtotal</th><th>Descuento</th><th>Total</th><th>Estado</th><th>Acción</th>';
            html += '</tr></thead><tbody>';
            
            compras.forEach(compra => {
                const fecha = new Date(compra.fecha_venta).toLocaleDateString('es-EC');
                const estadoBadge = compra.estado === 'completada' ? 'success' : 'warning';
                
                html += `
                    <tr>
                        <td>${fecha}</td>
                        <td>$${parseFloat(compra.subtotal).toFixed(2)}</td>
                        <td class="text-success">$${parseFloat(compra.descuento).toFixed(2)}</td>
                        <td><strong>$${parseFloat(compra.total).toFixed(2)}</strong></td>
                        <td><span class="badge bg-${estadoBadge}">${compra.estado}</span></td>
                        <td>
                            <a href="factura.php?id=${compra.id_venta}" class="btn btn-sm btn-primary-custom">
                                <i class="fas fa-file-invoice"></i> Ver Factura
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            contenedor.innerHTML = html;
        }
    </script>
</body>
</html>
