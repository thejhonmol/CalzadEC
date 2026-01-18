<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit;
}

// Bloquear acceso a administradores
if ($_SESSION['usuario']['rol'] === 'admin') {
    header('Location: ../../index.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - CalzadEC</title>
    
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
                <a class="nav-link" href="mis-compras.php"><i class="fas fa-history"></i> Mis Compras</a>
                <a class="nav-link active" href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Mi Carrito de Compras</h2>
        
        <div class="row">
            <div class="col-lg-8">
                <div id="carrito-items">
                    <!-- Los items se cargan dinámicamente -->
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="mb-4">Resumen de Compra</h4>
                    <div class="cart-summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="cart-summary-row">
                        <span>Descuentos:</span>
                        <span id="descuento" class="text-success">$0.00</span>
                    </div>
                    <div class="cart-summary-row border-top pt-3">
                        <strong>Total:</strong>
                        <strong class="cart-summary-total" id="total">$0.00</strong>
                    </div>
                    <button class="btn btn-success-custom w-100 mt-3" onclick="finalizarCompra()">
                        <i class="fas fa-check"></i> Finalizar Compra
                    </button>
                    <a href="catalogo.php" class="btn btn-outline-primary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Seguir Comprando
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/funciones.js"></script>
    
    <script>
        function actualizarCarritoUI(items) {
            const contenedor = document.getElementById('carrito-items');
            
            if (items.length === 0) {
                contenedor.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                        <h4>Tu carrito está vacío</h4>
                        <p class="text-muted">¡Agrega algunos productos para comenzar!</p>
                        <a href="catalogo.php" class="btn btn-primary-custom">Ver Catálogo</a>
                    </div>
                `;
                document.getElementById('subtotal').textContent = '$0.00';
                document.getElementById('descuento').textContent = '$0.00';
                document.getElementById('total').textContent = '$0.00';
                return;
            }
            
            let html = '';
            let subtotal = 0;
            
            items.forEach(item => {
                const precioTotal = item.precio * item.cantidad;
                subtotal += precioTotal;
                
                // Ruta de imagen - usar placeholder.svg si no existe
                let imagenRuta = '../../img/placeholder.svg';
                if (item.imagen_url && item.imagen_url.trim() !== '') {
                    // Si empieza con img/, agregar ../../
                    if (item.imagen_url.startsWith('img/')) {
                        imagenRuta = '../../' + item.imagen_url;
                    } 
                    // Si es URL completa, usar tal cual
                    else if (item.imagen_url.startsWith('http')) {
                        imagenRuta = item.imagen_url;
                    }
                    // Cualquier otro caso
                    else {
                        imagenRuta = item.imagen_url;
                    }
                }
                
                html += `
                    <div class="cart-item">
                        <img src="${imagenRuta}" 
                             alt="${item.nombre}" 
                             width="80" 
                             height="80" 
                             style="object-fit: cover; flex-shrink: 0;">
                        <div class="flex-grow-1">
                            <h5 class="mb-1">${item.nombre}</h5>
                            <p class="text-muted mb-2">Precio: $${item.precio}</p>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary btn-sm" onclick="carrito.actualizarCantidad(${item.id_producto}, ${item.cantidad - 1})">-</button>
                                <input type="number" class="form-control form-control-sm text-center" value="${item.cantidad}" readonly>
                                <button class="btn btn-outline-secondary btn-sm" onclick="carrito.actualizarCantidad(${item.id_producto}, ${item.cantidad + 1})">+</button>
                            </div>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-2">$${precioTotal.toFixed(2)}</h5>
                            <button class="btn btn-danger-custom btn-sm" onclick="carrito.eliminarProducto(${item.id_producto})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            contenedor.innerHTML = html;
            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('descuento').textContent = '$0.00';
            document.getElementById('total').textContent = `$${subtotal.toFixed(2)}`;
        }
        
        function finalizarCompra() {
            if (carrito.items.length === 0) {
                Swal.fire('Carrito vacío', 'Agrega productos antes de finalizar', 'warning');
                return;
            }
            
            const productos = carrito.items.map(item => ({
                id_producto: item.id_producto,
                cantidad: item.cantidad
            }));
            
            const formData = new FormData();
            formData.append('accion', 'crear');
            formData.append('productos', JSON.stringify(productos));
            
            fetch('../../controlador/VentaController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Compra exitosa!',
                        text: 'Tu pedido ha sido procesado correctamente',
                        confirmButtonText: 'Ver Factura'
                    }).then(() => {
                        carrito.vaciarCarrito();
                        window.location.href = `factura.php?id=${data.id_venta}`;
                    });
                } else {
                    Swal.fire('Error', data.error || 'No se pudo procesar la compra', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error al procesar la compra', 'error');
            });
        }
        
        // Cargar carrito al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            actualizarCarritoUI(carrito.items);
        });
    </script>
</body>
</html>
