<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Calzado - Inicio</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/estilos.css">
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
    <link rel="stylesheet" href="css/estilos.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shoe-prints"></i> CalzadEC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="fas fa-home"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#productos"><i class="fas fa-store"></i> Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vista/compartido/ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a>
                    </li>
                    <?php if (!isset($_SESSION['usuario'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="vista/login.php"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $_SESSION['usuario']['rol'] === 'admin' ? 'vista/admin/dashboard.php' : 'vista/cliente/perfil.php'; ?>">
                            <i class="fas fa-user"></i> Mi Perfil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
                    </li>
                    <?php endif; ?>
                    <?php if(!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="vista/cliente/carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span id="carrito-contador" class="badge bg-danger position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Promotion Bar -->
    <div id="promo-bar-container" class="d-none">
        <div class="promo-bar">
            <span id="promo-bar-text"></span>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); color: white; padding: 4rem 0;">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Bienvenido a CalzadEC</h1>
            <p class="lead mb-4">El mejor calzado para toda la familia. Calidad, estilo y comodidad al mejor precio.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#productos" class="btn btn-light btn-lg"><i class="fas fa-shopping-bag"></i> Ver Productos</a>
                <?php if (!isset($_SESSION['usuario'])): ?>
                <a href="vista/login.php" class="btn btn-outline-light btn-lg"><i class="fas fa-user"></i> Crear Cuenta</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Filtros -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="filter-section">
                <h4 class="mb-3"><i class="fas fa-filter"></i> Filtrar Productos</h4>
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
    <section id="productos" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nuestros Productos</h2>
            <div id="productos-contenedor" class="row">
                <!-- Los productos se cargan dinámicamente -->
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

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card">
                        <i class="fas fa-truck card-icon mb-3"></i>
                        <h4>Envío Rápido</h4>
                        <p class="text-muted">Entrega a domicilio en todo Ecuador</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card success">
                        <i class="fas fa-shield-alt card-icon mb-3" style="color: var(--secondary-color);"></i>
                        <h4>Compra Segura</h4>
                        <p class="text-muted">Pago seguro y garantizado</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="dashboard-card warning">
                        <i class="fas fa-tags card-icon mb-3" style="color: var(--accent-color);"></i>
                        <h4>Mejores Precios</h4>
                        <p class="text-muted">Promociones y descuentos especiales</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
    // Configuración de la tienda
    $tienda = ['nombre' => 'CalzadEC', 'slogan' => 'Tu tienda de confianza para calzado de calidad'];
    $contacto = ['direccion' => 'Riobamba, Ecuador', 'telefono' => '+593 99 876 5432', 'email' => 'contacto@calzadec.com', 'horario' => 'Lunes a Viernes: 9:00 - 18:00'];
    $propietario = ['nombre' => 'CalzadEC', 'anio' => date('Y'), 'representante' => 'Todos los derechos reservados'];
    $redes = ['facebook' => 'https://facebook.com/calzadec', 'instagram' => 'https://instagram.com/calzadec', 'whatsapp' => '+593998765432'];
    ?>
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-shoe-prints"></i> <?php echo htmlspecialchars($tienda['nombre']); ?></h5>
                    <p><?php echo htmlspecialchars($tienda['slogan']); ?></p>
                    <?php if (!empty($redes)): ?>
                    <div class="footer-social mt-3">
                        <?php if (!empty($redes['facebook'])): ?>
                        <a href="<?php echo $redes['facebook']; ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($redes['instagram'])): ?>
                        <a href="<?php echo $redes['instagram']; ?>" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($redes['whatsapp'])): ?>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $redes['whatsapp']); ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="#productos"><i class="fas fa-store"></i> Productos</a></li>
                        <li><a href="vista/compartido/ayuda.php"><i class="fas fa-question-circle"></i> Ayuda</a></li>
                        <li><a href="vista/login.php"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contacto</h5>
                    <div class="footer-contact">
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($contacto['direccion']); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($contacto['telefono']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($contacto['email']); ?></p>
                        <?php if (!empty($contacto['horario'])): ?>
                        <p><i class="fas fa-clock"></i> <?php echo htmlspecialchars($contacto['horario']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p class="mb-0">&copy; <?php echo $propietario['anio']; ?> <?php echo htmlspecialchars($tienda['nombre']); ?> - <?php echo htmlspecialchars($propietario['nombre']); ?> | <?php echo htmlspecialchars($propietario['representante'] ?? ''); ?></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <?php
    // Detectar rol del usuario para JavaScript
    $usuarioRol = isset($_SESSION['usuario']) ? $_SESSION['usuario']['rol'] : 'guest';
    ?>
    <script>
        // Variable global para verificar rol de usuario
        window.usuarioRol = '<?php echo $usuarioRol; ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/funciones.js"></script>
    
    <script>
        // Variables de filtro
        let filtroGenero = '';
        let filtroTipo = '';
        let filtroMarca = '';
        let filtroOrdenar = '';
        
        // Variables de paginación
        const PRODUCTOS_POR_PAGINA = 15;
        let todosLosProductos = [];
        let productosVisibles = 0;
        
        // Cargar productos y marcas al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarMarcas();
            cargarProductos();
            
            // Configurar filtros de botones
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filtro = this.getAttribute('data-filtro');
                    const valor = this.getAttribute('data-valor');
                    
                    // Remover clase active de botones del mismo filtro
                    document.querySelectorAll(`[data-filtro="${filtro}"]`).forEach(b => {
                        b.classList.remove('active');
                    });
                    
                    // Agregar clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Actualizar variable de filtro
                    if (filtro === 'genero') {
                        filtroGenero = valor;
                    } else if (filtro === 'tipo') {
                        filtroTipo = valor;
                    }
                    
                    // Recargar productos
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

        // Cargar promociones activas
        function cargarPromociones() {
            fetch('controlador/PromocionController.php?accion=activas')
                .then(response => response.json())
                .then(data => {
                    if (data.promociones && data.promociones.length > 0) {
                        const promo = data.promociones[0];
                        document.getElementById('promo-bar-text').innerHTML = 
                            `🔥 <strong>${promo.nombre}:</strong> ${promo.descripcion} - ¡Hasta ${parseFloat(promo.porcentaje_descuento).toFixed(0)}% de descuento!`;
                        document.getElementById('promo-bar-container').classList.remove('d-none');
                    }
                })
                .catch(error => console.error('Error al cargar promociones:', error));
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
        
        function cargarProductos(resetear = true) {
            let url = 'controlador/ProductoController.php?accion=filtrar';
            
            if (filtroGenero) {
                url += `&genero=${filtroGenero}`;
            }
            
            if (filtroTipo) {
                url += `&tipo=${filtroTipo}`;
            }
            
            if (filtroMarca) {
                url += `&marca=${filtroMarca}`;
            }
            
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
                    console.error('Error al cargar productos:', error);
                    document.getElementById('productos-contenedor').innerHTML = `
                        <div class="col-12 text-center">
                            <p class="text-danger">Error al cargar productos. Por favor, recargue la página.</p>
                        </div>
                    `;
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
                if (total > 0) {
                    info.textContent = `Mostrando todos los ${total} productos`;
                }
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
            
            let imagenUrl = producto.imagen_url || 'img/placeholder.svg';
            if (imagenUrl.startsWith('http://') || imagenUrl.startsWith('https://')) {
                // URL externa (Cloudinary)
            } else if (!imagenUrl.startsWith('/') && !imagenUrl.startsWith('img/')) {
                imagenUrl = 'img/' + imagenUrl;
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
            
            let botonHTML;
            if (agotado) {
                botonHTML = `<button class="btn btn-secondary mt-auto" disabled>
                                <i class="fas fa-ban"></i> No Disponible
                            </button>`;
            } else if (window.usuarioRol === 'admin') {
                botonHTML = `<button class="btn btn-secondary mt-auto" disabled title="Administradores no pueden comprar">
                                <i class="fas fa-lock"></i> No disponible
                            </button>`;
            } else if (window.usuarioRol === 'cliente') {
                botonHTML = `<button class="btn btn-primary mt-auto" onclick="carrito.agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')})">
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>`;
            } else {
                botonHTML = `<a href="vista/login.php" class="btn btn-primary mt-auto">
                                <i class="fas fa-shopping-cart"></i> Comprar
                            </a>`;
            }
            
            return `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100 ${agotado ? 'opacity-75' : ''}">
                        <div class="position-relative">
                            ${badgePromo}
                            <img src="${imagenUrl}" class="card-img-top product-img ${agotado ? 'filter-grayscale' : ''}" 
                                 alt="${producto.nombre}"
                                 onerror="this.src='img/placeholder.svg'"
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
        
        function cargarMarcas() {
            fetch('controlador/MarcaController.php?accion=listar')
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
