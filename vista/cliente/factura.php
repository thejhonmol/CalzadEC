<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$idVenta = $_GET['id'] ?? null;

if (!$idVenta) {
    header('Location: mis-compras.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <style>
        .factura-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .factura-header {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .factura-logo {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        .factura-numero {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-section h6 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 15px;
        }
        .tabla-productos {
            border-radius: 10px;
            overflow: hidden;
        }
        .tabla-productos thead {
            background: var(--primary-color);
            color: white;
        }
        .totales-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
        }
        .total-final {
            font-size: 1.5rem;
            color: var(--secondary-color);
            font-weight: bold;
        }
        @media print {
            .no-print { display: none !important; }
            .factura-container { box-shadow: none; }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom no-print">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
                <a class="nav-link" href="mis-compras.php"><i class="fas fa-history"></i> Mis Compras</a>
                <a class="nav-link" href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="factura-container" id="factura-content">
            <!-- Contenido se carga dinámicamente -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3">Cargando factura...</p>
            </div>
        </div>
        
        <div class="text-center mt-4 no-print">
            <button class="btn btn-primary-custom me-2" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir Factura
            </button>
            <a href="mis-compras.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Mis Compras
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        const idVenta = <?php echo json_encode($idVenta); ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            cargarFactura();
        });
        
        function cargarFactura() {
            fetch(`../../controlador/VentaController.php?accion=factura&id=${idVenta}`)
                .then(response => response.json())
                .then(data => {
                    if (data.factura) {
                        mostrarFactura(data.factura);
                    } else {
                        mostrarError(data.error || 'No se pudo cargar la factura');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarError('Error al cargar la factura');
                });
        }
        
        function mostrarFactura(factura) {
            const fechaVenta = new Date(factura.fecha_venta);
            const fechaFormateada = fechaVenta.toLocaleDateString('es-EC', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            let productosHTML = '';
            factura.detalles.forEach(item => {
                productosHTML += `
                    <tr>
                        <td>${item.codigo_producto}</td>
                        <td>
                            <strong>${item.nombre_producto}</strong><br>
                            <small class="text-muted">${item.genero} - ${item.tipo} - Talla ${item.talla}</small>
                        </td>
                        <td class="text-center">${item.cantidad}</td>
                        <td class="text-end">$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                        <td class="text-end"><strong>$${parseFloat(item.subtotal).toFixed(2)}</strong></td>
                    </tr>
                `;
            });
            
            const html = `
                <div class="factura-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="factura-logo">
                                <i class="fas fa-shoe-prints"></i> CalzadEC
                            </div>
                            <p class="text-muted mb-0">Tu tienda de calzado de confianza</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="factura-numero">
                                <i class="fas fa-file-invoice"></i> Factura #${String(factura.id_venta).padStart(6, '0')}
                            </span>
                            <p class="text-muted mt-2 mb-0">${fechaFormateada}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6><i class="fas fa-store"></i> Datos de la Tienda</h6>
                            <p class="mb-1"><strong>CalzadEC</strong></p>
                            <p class="mb-1"><i class="fas fa-map-marker-alt"></i> Riobamba, Ecuador</p>
                            <p class="mb-1"><i class="fas fa-phone"></i> (03) 123-4567</p>
                            <p class="mb-0"><i class="fas fa-envelope"></i> ventas@calzadec.com</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6><i class="fas fa-user"></i> Datos del Cliente</h6>
                            <p class="mb-1"><strong>${factura.nombre_completo}</strong></p>
                            <p class="mb-1"><i class="fas fa-id-card"></i> Cédula: ${factura.cedula}</p>
                            <p class="mb-1"><i class="fas fa-envelope"></i> ${factura.email}</p>
                            <p class="mb-1"><i class="fas fa-phone"></i> ${factura.telefono}</p>
                            <p class="mb-0"><i class="fas fa-map-marker-alt"></i> ${factura.direccion}</p>
                        </div>
                    </div>
                </div>
                
                <h5 class="mb-3"><i class="fas fa-shopping-bag"></i> Detalle de Productos</h5>
                <div class="table-responsive tabla-productos mb-4">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">P. Unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${productosHTML}
                        </tbody>
                    </table>
                </div>
                
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="totales-section">
                            <div class="d-flex justify-content-between mb-2">
                                <span>DESCUENTO APLICADO:</span>
                                <span>$${parseFloat(factura.descuento).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>SUBTOTAL 0%:</span>
                                <span>$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>SUBTOTAL SIN IMPUESTOS:</span>
                                <span>$${(parseFloat(factura.total) / 1.15).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>15% IVA:</span>
                                <span>$${(parseFloat(factura.total) - (parseFloat(factura.total) / 1.15)).toFixed(2)}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="total-final">TOTAL:</span>
                                <span class="total-final">$${parseFloat(factura.total).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 pt-4 border-top">
                    <p class="text-muted mb-1"><i class="fas fa-check-circle text-success"></i> Compra procesada exitosamente</p>
                    <small class="text-muted">¡Gracias por tu compra! Esperamos verte pronto.</small>
                </div>
            `;
            
            document.getElementById('factura-content').innerHTML = html;
        }
        
        function mostrarError(mensaje) {
            document.getElementById('factura-content').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                    <h4>${mensaje}</h4>
                    <a href="mis-compras.php" class="btn btn-primary-custom mt-3">
                        <i class="fas fa-arrow-left"></i> Volver a Mis Compras
                    </a>
                </div>
            `;
        }
    </script>
</body>
</html>
