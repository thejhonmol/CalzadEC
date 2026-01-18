<?php
session_start();

// Verificar autenticación
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
    <title>Catálogo - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <style>
        .filter-grayscale {
            filter: grayscale(100%);
        }
        .hover-shadow {
            transition: box-shadow 0.3s ease;
        }
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body>
    <!-- Promotion Bar -->
    <div id="promo-bar-container" class="d-none">
        <div class="promo-bar">
            <span id="promo-bar-text"></span>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis-compras.php"><i class="fas fa-history"></i> Mis Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../compartido/ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span id="carrito-contador" class="badge bg-danger position-absolute top-0 start-100 translate-middle">0</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bienvenida -->
    <section class="bg-light py-4">
        <div class="container">
            <h2>Bienvenido, <?php echo htmlspecialchars($usuario['nombre_completo']); ?>! 👋</h2>
            <p class="text-muted">Explora nuestro catálogo y encuentra el calzado perfecto para ti.</p>
        </div>
    </section>

    <!-- Filtros -->
    <section class="py-3 bg-white">
        <div class="container">
            <div class="filter-section">
                <h5 class="mb-3"><i class="fas fa-filter"></i> Filtrar Productos</h5>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Por Género:</h6>
                        <button class="filter-btn active" data-filtro="genero" data-valor="">Todos</button>
                        <button class="filter-btn" data-filtro="genero" data-valor="hombre">Hombre</button>
                        <button class="filter-btn" data-filtro="genero" data-valor="mujer">Mujer</button>
                        <button class="filter-btn" data-filtro="genero" data-valor="niño">Niños</button>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Por Tipo:</h6>
                        <button class="filter-btn active" data-filtro="tipo" data-valor="">Todos</button>
                        <button class="filter-btn" data-filtro="tipo" data-valor="deportivo">Deportivo</button>
                        <button class="filter-btn" data-filtro="tipo" data-valor="no_deportivo">No Deportivo</button>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Por Marca:</h6>
                        <select id="filtro-marca" class="form-select">
                            <option value="">Todas las marcas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-2">Ordenar por:</h6>
                        <select id="filtro-ordenar" class="form-select">
                            <option value="">Sin ordenar</option>
                            <option value="precio_asc">Precio: Menor a Mayor</option>
                            <option value="precio_desc">Precio: Mayor a Menor</option>
                            <option value="nombre_asc">Nombre: A-Z</option>
                            <option value="nombre_desc">Nombre: Z-A</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Productos -->
    <section class="py-5">
        <div class="container">
            <div id="productos-contenedor" class="row">
                <div class="col-12 text-center">
                    <div class="spinner-custom mx-auto"></div>
                    <p class="mt-3 text-muted">Cargando productos...</p>
                </div>
            </div>
            <!-- Botón Mostrar Más -->
            <div id="mostrar-mas-container" class="text-center mt-4 d-none">
                <button id="btn-mostrar-mas" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Mostrar más productos
                </button>
                <p id="productos-info" class="text-muted mt-2"></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 CalzadEC - Tu tienda de calzado de confianza</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../js/funciones.js"></script>
    
    <script>
        let filtroGenero = '';
        let filtroTipo = '';
        let filtroMarca = '';
        let filtroOrdenar = '';
        
        // Variables de paginación
        const PRODUCTOS_POR_PAGINA = 15;
        let todosLosProductos = [];
        let productosVisibles = 0;
        
        document.addEventListener('DOMContentLoaded', function() {
            cargarMarcas();
            cargarProductos();
            
            // Configurar filtros de botones
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filtro = this.getAttribute('data-filtro');
                    const valor = this.getAttribute('data-valor');
                    
                    document.querySelectorAll(`[data-filtro="${filtro}"]`).forEach(b => {
                        b.classList.remove('active');
                    });
                    
                    this.classList.add('active');
                    
                    if (filtro === 'genero') {
                        filtroGenero = valor;
                    } else if (filtro === 'tipo') {
                        filtroTipo = valor;
                    }
                    
                    cargarProductos();
                });
            });
            
            // Configurar filtro de marca (dropdown)
            document.getElementById('filtro-marca').addEventListener('change', function() {
                filtroMarca = this.value;
                cargarProductos();
            });
            
            // Configurar filtro de ordenamiento (dropdown)
            document.getElementById('filtro-ordenar').addEventListener('change', function() {
                filtroOrdenar = this.value;
                cargarProductos();
            });
            
            // Configurar botón mostrar más
            document.getElementById('btn-mostrar-mas').addEventListener('click', function() {
                mostrarMasProductos();
            });

            // Cargar barra de promociones
            cargarPromociones();
        });

        // Cargar promociones activas con rotación automática
        let promocionesActivas = [];
        let indicePromocionActual = 0;
        let intervaloPromocion = null;

        function cargarPromociones() {
            fetch('../../controlador/PromocionController.php?accion=activas')
                .then(response => response.json())
                .then(data => {
                    if (data.promociones && data.promociones.length > 0) {
                        promocionesActivas = data.promociones;
                        document.getElementById('promo-bar-container').classList.remove('d-none');
                        
                        // Mostrar primera promoción
                        mostrarPromocion(0);
                        
                        // Si hay más de una promoción, iniciar rotación automática
                        if (promocionesActivas.length > 1) {
                            intervaloPromocion = setInterval(rotarPromocion, 5000);
                        }
                    }
                })
                .catch(error => console.error('Error al cargar promociones:', error));
        }

        function mostrarPromocion(indice) {
            const promo = promocionesActivas[indice];
            const promoText = document.getElementById('promo-bar-text');
            
            // Añadir animación de fade
            promoText.style.opacity = '0';
            
            setTimeout(() => {
                promoText.innerHTML = 
                    `🔥 <strong>${promo.nombre}:</strong> ${promo.descripcion} - ¡Hasta ${parseFloat(promo.porcentaje_descuento).toFixed(0)}% de descuento!`;
                promoText.style.opacity = '1';
            }, 300);
        }

        function rotarPromocion() {
            indicePromocionActual = (indicePromocionActual + 1) % promocionesActivas.length;
            mostrarPromocion(indicePromocionActual);
        }
        
        function ordenarProductos(productos) {
            if (!filtroOrdenar) return productos;
            
            return [...productos].sort((a, b) => {
                switch (filtroOrdenar) {
                    case 'precio_asc':
                        return parseFloat(a.precio_final || a.precio) - parseFloat(b.precio_final || b.precio);
                    case 'precio_desc':
                        return parseFloat(b.precio_final || b.precio) - parseFloat(a.precio_final || a.precio);
                    case 'nombre_asc':
                        return a.nombre.localeCompare(b.nombre);
                    case 'nombre_desc':
                        return b.nombre.localeCompare(a.nombre);
                    default:
                        return 0;
                }
            });
        }
        
        function cargarProductos() {
            let url = '../../controlador/ProductoController.php?accion=filtrar';
            
            if (filtroGenero) url += `&genero=${filtroGenero}`;
            if (filtroTipo) url += `&tipo=${filtroTipo}`;
            if (filtroMarca) url += `&marca=${filtroMarca}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.productos) {
                        todosLosProductos = ordenarProductos(data.productos);
                        productosVisibles = 0;
                        document.getElementById('productos-contenedor').innerHTML = '';
                        mostrarMasProductos();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('productos-contenedor').innerHTML = 
                        '<div class="col-12 text-center"><p class="text-danger">Error al cargar productos</p></div>';
                });
        }
        
        function mostrarMasProductos() {
            const contenedor = document.getElementById('productos-contenedor');
            const inicio = productosVisibles;
            const fin = Math.min(productosVisibles + PRODUCTOS_POR_PAGINA, todosLosProductos.length);
            
            for (let i = inicio; i < fin; i++) {
                const producto = todosLosProductos[i];
                contenedor.innerHTML += generarCardProducto(producto);
            }
            
            productosVisibles = fin;
            actualizarBotonMostrarMas();
        }
        
        function actualizarBotonMostrarMas() {
            const container = document.getElementById('mostrar-mas-container');
            const info = document.getElementById('productos-info');
            const total = todosLosProductos.length;
            
            if (productosVisibles < total) {
                container.classList.remove('d-none');
                const restantes = total - productosVisibles;
                info.textContent = `Mostrando ${productosVisibles} de ${total} productos (${restantes} restantes)`;
            } else {
                container.classList.add('d-none');
            }
        }
        
        function generarCardProducto(producto) {
            const stock = parseInt(producto.stock) || 0;
            const agotado = stock <= 0;
            const tienePromocion = producto.tiene_promocion && producto.porcentaje_descuento > 0;

            // Badge flotante con animación
            const badgePromo = tienePromocion 
                ? `<div class="promo-badge-floating">-${parseFloat(producto.porcentaje_descuento).toFixed(0)}%</div>` 
                : '';

            let precioHTML;
            if (tienePromocion) {
                precioHTML = `
                    <div class="mb-2">
                        <span class="text-decoration-line-through text-muted">$${parseFloat(producto.precio_original || producto.precio).toFixed(2)}</span>
                    </div>
                    <p class="card-text precio precio-promo fs-4 fw-bold">$${parseFloat(producto.precio_final).toFixed(2)}</p>
                `;
            } else {
                precioHTML = `<p class="card-text precio fs-4 fw-bold">$${parseFloat(producto.precio_final || producto.precio).toFixed(2)}</p>`;
            }
            
            let stockHTML = '';
            if (agotado) {
                stockHTML = `<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Sin stock</span>`;
            } else if (stock <= 5) {
                stockHTML = `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> ¡Últimas ${stock} unidades!</span>`;
            } else {
                stockHTML = `<span class="badge bg-success"><i class="fas fa-check-circle"></i> Disponible</span>`;
            }
            
            let imagenUrl = producto.imagen_url || '../../img/placeholder.svg';
            if (imagenUrl.startsWith('http://') || imagenUrl.startsWith('https://')) {
                // URL externa (Cloudinary)
            } else if (!imagenUrl.startsWith('/') && !imagenUrl.startsWith('../../img/')) {
                imagenUrl = '../../img/' + imagenUrl;
            }

            const agotadoOverlay = agotado
                ? `<div class="position-absolute top-50 start-50 translate-middle" 
                        style="z-index: 10; background: rgba(220, 53, 69, 0.9); 
                               color: white; padding: 10px 30px; 
                               border-radius: 8px; font-weight: bold; 
                               box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                       <i class="fas fa-times-circle"></i> AGOTADO
                   </div>`
                : '';
            
            const botonHTML = agotado 
                ? `<button class="btn btn-secondary mt-auto" disabled><i class="fas fa-ban"></i> No Disponible</button>`
                : `<button class="btn btn-primary mt-auto" onclick="carrito.agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')})">
                    <i class="fas fa-cart-plus"></i> Agregar al carrito
                   </button>`;
            
            return `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100 ${agotado ? 'opacity-75' : ''}">
                        <div class="position-relative">
                            ${badgePromo}
                            <img src="${imagenUrl}" class="card-img-top product-img ${agotado ? 'filter-grayscale' : ''}" 
                                 alt="${producto.nombre}"
                                 onerror="this.src='../../img/placeholder.svg'"
                                 style="height: 250px; object-fit: cover;">
                            ${agotadoOverlay}
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${producto.nombre}</h5>
                            <div class="mb-2">
                                <span class="badge bg-primary me-1">${producto.genero}</span>
                                <span class="badge bg-secondary">${producto.tipo === 'deportivo' ? 'Deportivo' : 'No Deportivo'}</span>
                                ${stockHTML}
                            </div>
                            <p class="text-muted mb-2"><i class="fas fa-ruler"></i> Talla: ${producto.talla}</p>
                            ${precioHTML}
                            ${botonHTML}
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Override renderizarProductos para corregir rutas en vista de cliente
        function renderizarProductos(productos) {
            const contenedor = document.getElementById('productos-contenedor');
            if (!contenedor) return;

            contenedor.innerHTML = '';
            
            if (!productos || productos.length === 0) {
                contenedor.innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted">No se encontraron productos</p>
                    </div>
                `;
                return;
            }

            productos.forEach(producto => {
                // Verificar stock
                const stock = parseInt(producto.stock) || 0;
                const agotado = stock <= 0;
                
                // Verificar si tiene promoción activa
                const tienePromocion = producto.tiene_promocion && producto.porcentaje_descuento > 0;
                
                // Calcular precio final
                let precioFinal = parseFloat(producto.precio);
                if (tienePromocion && producto.precio_final) {
                    precioFinal = parseFloat(producto.precio_final);
                }
                
                const precioHTML = tienePromocion
                    ? `<div class="mb-2">
                           <p class="mb-1"><del class="text-muted">$${parseFloat(producto.precio).toFixed(2)}</del></p>
                           <p class="text-success mb-0 fw-bold fs-5">$${precioFinal.toFixed(2)}</p>
                           <small class="text-success"><i class="fas fa-tag"></i> Ahorra ${parseFloat(producto.porcentaje_descuento).toFixed(0)}%</small>
                       </div>`
                    : `<p class="fw-bold fs-5 mb-2">$${parseFloat(producto.precio).toFixed(2)}</p>`;

                const badgeHTML = tienePromocion
                    ? `<span class="position-absolute top-0 end-0 m-2 badge bg-danger">-${parseFloat(producto.porcentaje_descuento).toFixed(0)}%</span>`
                    : '';
                
                // Overlay de agotado
                const agotadoOverlay = agotado 
                    ? `<div class="position-absolute top-50 start-50 translate-middle" 
                            style="z-index: 10; background: rgba(220, 53, 69, 0.9); 
                                   color: white; padding: 10px 30px; 
                                   border-radius: 8px; font-weight: bold; 
                                   box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                           <i class="fas fa-times-circle"></i> AGOTADO
                       </div>`
                    : '';
                
                // Ruta de imagen - usar placeholder.svg si no existe
                let imagenRuta = '../../img/placeholder.svg';
                if (producto.imagen_url && producto.imagen_url.trim() !== '') {
                    // Agregar ../../ si es ruta relativa
                    if (producto.imagen_url.startsWith('img/')) {
                        imagenRuta = '../../' + producto.imagen_url;
                    } else if (producto.imagen_url.startsWith('http')) {
                        imagenRuta = producto.imagen_url;
                    } else {
                        imagenRuta = producto.imagen_url;
                    }
                }
                
                // Botón de agregar al carrito o agotado
                const botonHTML = agotado
                    ? `<button class="btn btn-secondary w-100" disabled>
                           <i class="fas fa-ban"></i> No Disponible
                       </button>`
                    : `<button class="btn btn-primary w-100" onclick="carrito.agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')})">
                           <i class="fas fa-cart-plus"></i> Agregar al Carrito
                       </button>`;
                
                // Indicador de stock
                const stockHTML = agotado 
                    ? '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Sin stock</span>'
                    : stock < 5 
                        ? `<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> Quedan ${stock}</span>`
                        : `<span class="badge bg-success"><i class="fas fa-check-circle"></i> Disponible</span>`;

                const card = `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow ${agotado ? 'opacity-75' : ''}">
                            ${badgeHTML}
                            <div class="position-relative">
                                <img src="${imagenRuta}" 
                                     class="card-img-top ${agotado ? 'filter-grayscale' : ''}" 
                                     alt="${producto.nombre}"
                                     width="100%"
                                     height="250"
                                     style="object-fit: cover;">
                                ${agotadoOverlay}
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${producto.nombre}</h5>
                                <div class="mb-2">
                                    <span class="badge bg-primary me-1">${producto.genero}</span>
                                    <span class="badge bg-secondary">${producto.tipo === 'deportivo' ? 'Deportivo' : 'No Deportivo'}</span>
                                    ${stockHTML}
                                </div>
                                <p class="text-muted mb-2"><i class="fas fa-ruler"></i> Talla: ${producto.talla}</p>
                                ${precioHTML}
                                ${botonHTML}
                            </div>
                        </div>
                    </div>
                `;

                contenedor.innerHTML += card;
            });
        }
        
        // Función eliminada a favor de link directo

        
        function cargarMarcas() {
            fetch('../../controlador/MarcaController.php?accion=listar')
                .then(response => response.json())
                .then(data => {
                    if (data.marcas) {
                        const select = document.getElementById('filtro-marca');
                        data.marcas.forEach(marca => {
                            const option = document.createElement('option');
                            option.value = marca.id_marca;
                            option.textContent = marca.nombre_marca;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar marcas:', error));
        }
    </script>
</body>
</html>
