<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayuda - CalzadEC</title>
    
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
                <a class="nav-link" href="../../index.php"><i class="fas fa-home"></i> Volver al Inicio</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-5"><i class="fas fa-question-circle text-primary"></i> Centro de Ayuda</h1>
        
        <!-- Sección de Preguntas Frecuentes -->
        <div class="help-section">
            <h3><i class="fas fa-book"></i> Preguntas Frecuentes</h3>
            
            <div class="accordion help-accordion" id="faqAccordion">
                <!-- Pregunta 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            ¿Cómo puedo registrarme en el sistema?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Para registrarte en CalzadEC sigue estos pasos:</p>
                            <ol>
                                <li>Haz clic en el botón "Ingresar" en la barra de navegación</li>
                                <li>En la página de Login, haz clic en "Regístrate aquí"</li>
                                <li>Completa el formulario con tus datos personales:
                                    <ul>
                                        <li>Cédula ecuatoriana (10 dígitos)</li>
                                        <li>Nombre completo</li>
                                        <li>Email válido</li>
                                        <li>Teléfono (10 dígitos)</li>
                                        <li>Dirección completa</li>
                                        <li>Contraseña (mínimo 6 caracteres)</li>
                                    </ul>
                                </li>
                                <li>Haz clic en "Registrarse" y automáticamente serás redirigido al catálogo</li>
                            </ol>
                            <p class="alert alert-info"><strong>Nota:</strong> Tu cédula debe ser válida y no estar previamente registrada en el sistema.</p>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Cómo puedo buscar productos específicos?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Puedes filtrar productos de varias maneras:</p>
                            <ul>
                                <li><strong>Por Género:</strong> Selecciona entre Hombre, Mujer o Niños en los botones de filtro</li>
                                <li><strong>Por Tipo:</strong> Elige calzado Deportivo o No Deportivo</li>
                                <li><strong>Combinación:</strong> Puedes combinar ambos filtros para resultados más específicos</li>
                            </ul>
                            <p>Los productos se actualizan automáticamente al aplicar los filtros.</p>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Cómo funciona el carrito de compras?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <h5>Agregar productos:</h5>
                            <ol>
                                <li>Navega por el catálogo y encuentra el producto que deseas</li>
                                <li>Haz clic en "Agregar al Carrito"</li>
                                <li>El contador del carrito se actualizará automáticamente</li>
                            </ol>
                            
                            <h5>Ver tu carrito:</h5>
                            <ul>
                                <li>Haz clic en el icono del carrito en la barra de navegación</li>
                                <li>Podrás ver todos los productos agregados</li>
                                <li>Puedes ajustar cantidades o eliminar productos</li>
                            </ul>
                            
                            <h5>Finalizar compra:</h5>
                            <ul>
                                <li>Verifica que los productos sean correctos</li>
                                <li>Haz clic en "Finalizar Compra"</li>
                                <li>Se generará automáticamente tu factura</li>
                            </ul>
                            
                            <p class="alert alert-warning"><strong>Importante:</strong> Debes estar registrado e iniciar sesión para realizar compras.</p>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Qué son las promociones y cómo funcionan?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Las promociones son descuentos especiales aplicados a productos seleccionados:</p>
                            <ul>
                                <li><strong>Identificación:</strong> Los productos en promoción muestran un badge con el porcentaje de descuento</li>
                                <li><strong>Precio mostrado:</strong> El precio con descuento se muestra en grande, y el precio original tachado</li>
                                <li><strong>Aplicación automática:</strong> Los descuentos se aplican automáticamente al agregar al carrito</li>
                                <li><strong>Vigencia:</strong> Cada promoción tiene fechas de inicio y fin</li>
                            </ul>
                            <p>¡Aprovecha nuestras promociones especiales para obtener el mejor precio!</p>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            ¿Cómo puedo ver mis compras anteriores?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Para ver tu historial de compras:</p>
                            <ol>
                                <li>Inicia sesión en tu cuenta</li>
                                <li>Ve a la sección "Mis Compras" en el menú de navegación</li>
                                <li>Verás una lista de todas tus compras realizadas</li>
                                <li>Puedes ver o descargar la factura de cada compra</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Pregunta 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                            ¿Qué hago si olvidé mi contraseña?
                        </button>
                    </h2>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Si olvidaste tu contraseña, por favor contacta al administrador del sistema proporcionando:</p>
                            <ul>
                                <li>Tu número de cédula</li>
                                <li>Tu email registrado</li>
                            </ul>
                            <p>El administrador podrá ayudarte a recuperar o restablecer tu contraseña.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guía de Usuario - Clientes -->
        <div class="help-section">
            <h3><i class="fas fa-user"></i> Guía para Clientes</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Navegación básica</h5>
                    <ul>
                        <li><strong>Inicio:</strong> Muestra el catálogo completo de productos</li>
                        <li><strong>Productos:</strong> Ver todos los productos disponibles</li>
                        <li><strong>Carrito:</strong> Ver productos seleccionados para compra</li>
                        <li><strong>Mis Compras:</strong> Historial de compras realizadas</li>
                        <li><strong>Ayuda:</strong> Esta sección de ayuda</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Información importante</h5>
                    <ul>
                        <li>Los precios mostrados incluyen descuentos vigentes</li>
                        <li>El stock se actualiza en tiempo real</li>
                        <li>Puedes modificar tu carrito antes de finalizar la compra</li>
                        <li>Las facturas están disponibles inmediatamente después de la compra</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Guía de Usuario - Administradores -->
        <div class="help-section">
            <h3><i class="fas fa-user-shield"></i> Guía para Administradores</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Funciones principales</h5>
                    <ul>
                        <li><strong>Dashboard:</strong> Vista general con estadísticas</li>
                        <li><strong>Gestión de Productos:</strong> Crear, editar y eliminar productos</li>
                        <li><strong>Gestión de Clientes:</strong> Ver y administrar clientes registrados</li>
                        <li><strong>Ventas:</strong> Ver historial de todas las ventas</li>
                        <li><strong>Promociones:</strong> Crear y gestionar descuentos</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Gestión de inventario</h5>
                    <ul>
                        <li>El stock se actualiza automáticamente con cada venta</li>
                        <li>Se muestran alertas de "stock bajo" (menos de 10 unidades)</li>
                        <li>Puedes actualizar manualmente el stock de cada producto</li>
                        <li>Los productos eliminados se marcan como inactivos</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div class="help-section">
            <h3><i class="fas fa-envelope"></i> Contacto y Soporte</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                        <h5>Teléfono</h5>
                        <p>+593 99 876 5432</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                        <h5>Email</h5>
                        <p>contacto@calzadec.com</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3">
                        <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                        <h5>Ubicación</h5>
                        <p>Riobamba, Ecuador</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 CalzadEC - Sistema de Gestión de Tienda de Calzado</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
