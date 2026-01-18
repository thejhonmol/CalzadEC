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
    <title>Mi Perfil - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <script src="../../js/ecuador-locations.js"></script>
</head>
<body>
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
                        <a class="nav-link" href="catalogo.php"><i class="fas fa-store"></i> Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis-compras.php"><i class="fas fa-history"></i> Mis Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="carrito.php">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span id="carrito-contador" class="badge bg-danger position-absolute top-0 start-100 translate-middle">0</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../../controlador/AuthController.php?accion=logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-id-card"></i> Mi Perfil</h4>
                        <button id="btn-editar" class="btn btn-light btn-sm">
                            <i class="fas fa-edit"></i> Editar Datos
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <form id="form-perfil">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Cédula</label>
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($usuario['cedula']); ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Email</label>
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small">Nombre Completo</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small d-block">Teléfono</label>
                                <div id="view-telefono" class="fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['usuario']['telefono']); ?></div>
                                <input type="text" id="edit-telefono" class="form-control d-none" name="telefono" value="<?php echo htmlspecialchars($_SESSION['usuario']['telefono']); ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small d-block">Provincia</label>
                                    <div id="view-provincia" class="fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['usuario']['provincia'] ?? 'No definida'); ?></div>
                                    <select id="edit-provincia" class="form-select d-none" name="provincia"></select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small d-block">Ciudad</label>
                                    <div id="view-ciudad" class="fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['usuario']['ciudad'] ?? 'No definida'); ?></div>
                                    <select id="edit-ciudad" class="form-select d-none" name="ciudad">
                                        <option value="">Seleccione una ciudad...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted small d-block">Dirección</label>
                                <div id="view-direccion" class="fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['usuario']['direccion']); ?></div>
                                <textarea id="edit-direccion" class="form-control d-none" name="direccion" rows="2"><?php echo htmlspecialchars($_SESSION['usuario']['direccion']); ?></textarea>
                            </div>

                            <div id="botones-edicion" class="d-none text-end">
                                <button type="button" id="btn-cancelar" class="btn btn-secondary me-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" id="btn-guardar" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-perfil');
            const btnEditar = document.getElementById('btn-editar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const btnGuardar = document.getElementById('btn-guardar');
            const botonesEdicion = document.getElementById('botones-edicion');

            btnEditar.addEventListener('click', function() {
                $('#edit-telefono, #edit-provincia, #edit-ciudad, #edit-direccion').removeClass('d-none');
                $('#view-telefono, #view-provincia, #view-ciudad, #view-direccion').addClass('d-none');
                $(this).addClass('d-none');
                $('#botones-edicion').removeClass('d-none');
                
                // Cargar localizaciones
                cargarProvincias('edit-provincia', 'edit-ciudad', 
                    "<?php echo $_SESSION['usuario']['provincia'] ?? ''; ?>", 
                    "<?php echo $_SESSION['usuario']['ciudad'] ?? ''; ?>");
            });

            btnCancelar.addEventListener('click', function() {
                $('#edit-telefono, #edit-provincia, #edit-ciudad, #edit-direccion').addClass('d-none');
                $('#view-telefono, #view-provincia, #view-ciudad, #view-direccion').removeClass('d-none');
                $('#botones-edicion').addClass('d-none');
                $('#btn-editar').removeClass('d-none');

                // Restore original values
                $('#edit-telefono').val("<?php echo htmlspecialchars($_SESSION['usuario']['telefono']); ?>");
                $('#edit-direccion').val(`<?php echo addslashes(htmlspecialchars($_SESSION['usuario']['direccion'])); ?>`);
                // For select fields, re-initialize or set selected option if needed
                // For simplicity, we just hide them and let the next edit click re-populate
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('telefono', $('#edit-telefono').val());
                formData.append('provincia', $('#edit-provincia').val());
                formData.append('ciudad', $('#edit-ciudad').val());
                formData.append('direccion', $('#edit-direccion').val());
                
                fetch('../../controlador/UsuarioController.php?accion=actualizar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.mensaje,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Update displayed values
                            $('#view-telefono').text($('#edit-telefono').val());
                            $('#view-provincia').text($('#edit-provincia option:selected').text());
                            $('#view-ciudad').text($('#edit-ciudad option:selected').text());
                            $('#view-direccion').text($('#edit-direccion').val());
                            
                            // Simulate cancel click to switch back to view mode
                            btnCancelar.click();
                            // Reload to update session data and ensure consistency
                            location.reload(); 
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud'
                    });
                });
            });
        });
    </script>
</body>
</html>
