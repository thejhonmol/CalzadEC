<?php
session_start();

// Si ya hay sesión activa, redirigir
if (isset($_SESSION['usuario'])) {
    header("Location: cliente/catalogo.php");
    exit;
}

require_once '../modelo/Cliente.php';

$errores = [];
$exito = false;

// Procesar registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = trim($_POST['cedula'] ?? '');
    $nombre = trim($_POST['nombre_completo'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $provincia = trim($_POST['provincia'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validaciones
    if (strlen($cedula) != 10 || !ctype_digit($cedula)) {
        $errores[] = "La cédula debe tener 10 dígitos numéricos";
    }
    
    if (empty($nombre)) {
        $errores[] = "El nombre es requerido";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email inválido";
    }
    
    if (strlen($telefono) != 10 || !ctype_digit($telefono)) {
        $errores[] = "El teléfono debe tener 10 dígitos";
    }
    
    if (empty($provincia)) {
        $errores[] = "La provincia es requerida";
    }
    
    if (empty($ciudad)) {
        $errores[] = "La ciudad es requerida";
    }

    if (empty($direccion)) {
        $errores[] = "La dirección es requerida";
    }
    
    // Validación de contraseña segura
    $passwordValida = true;
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
        $passwordValida = false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra mayúscula";
        $passwordValida = false;
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra minúscula";
        $passwordValida = false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un número";
        $passwordValida = false;
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un carácter especial (!@#$%^&*...)";
        $passwordValida = false;
    }
    
    if ($password !== $password_confirm) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Si no hay errores, registrar
    if (empty($errores)) {
        $modelo = new Cliente();
        
        // Validar cédula ecuatoriana
        if (!$modelo->validarCedulaEcuatoriana($cedula)) {
            $errores[] = "La cédula ecuatoriana no es válida";
        } else {
            $datos = [
                'cedula' => $cedula,
                'nombre_completo' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'provincia' => $provincia,
                'ciudad' => $ciudad,
                'password' => $password,
                'rol' => 'cliente'
            ];
            
            $id = $modelo->registrar($datos);
            
            if ($id) {
                // Autologin
                $usuario = $modelo->obtenerPorId($id);
                $_SESSION['usuario'] = $usuario;
                $_SESSION['ultimo_acceso'] = time();
                
                header("Location: cliente/catalogo.php");
                exit;
            } else {
                $errores[] = "Error al registrar. La cédula o email ya pueden estar registrados.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <script src="../js/ecuador-locations.js"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2><i class="fas fa-shoe-prints text-primary"></i> CalzadEC</h2>
                            <h4>Crear Cuenta</h4>
                            <p class="text-muted">Regístrate para comenzar a comprar</p>
                        </div>
                        
                        <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cédula Ecuatoriana *</label>
                                <input type="text" class="form-control" name="cedula" maxlength="10"
                                       placeholder="10 dígitos" required
                                       value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
                                <small class="text-muted">Debe ser una cédula ecuatoriana válida</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Completo *</label>
                                <input type="text" class="form-control" name="nombre_completo"
                                       placeholder="Ej: Juan Pérez García" required
                                       value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email *</label>
                                <input type="email" class="form-control" name="email"
                                       placeholder="correo@ejemplo.com" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Teléfono *</label>
                                <input type="text" class="form-control" name="telefono" maxlength="10"
                                       placeholder="10 dígitos" required
                                       value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Provincia *</label>
                                    <select class="form-select" name="provincia" id="provincia" required>
                                        <!-- Se carga via JS -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ciudad *</label>
                                    <select class="form-select" name="ciudad" id="ciudad" required>
                                        <option value="">Seleccione una ciudad...</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dirección *</label>
                                <textarea class="form-control" name="direccion" rows="2"
                                          placeholder="Dirección completa" required><?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contraseña *</label>
                                <input type="password" class="form-control" name="password" id="password"
                                       placeholder="Mínimo 8 caracteres" required oninput="validarPassword()">
                                <div id="password-requisitos" class="mt-2">
                                    <small class="d-block" id="req-longitud"><i class="fas fa-times text-danger"></i> Mínimo 8 caracteres</small>
                                    <small class="d-block" id="req-mayuscula"><i class="fas fa-times text-danger"></i> Al menos una mayúscula (A-Z)</small>
                                    <small class="d-block" id="req-minuscula"><i class="fas fa-times text-danger"></i> Al menos una minúscula (a-z)</small>
                                    <small class="d-block" id="req-numero"><i class="fas fa-times text-danger"></i> Al menos un número (0-9)</small>
                                    <small class="d-block" id="req-especial"><i class="fas fa-times text-danger"></i> Al menos un carácter especial (!@#$%&*)</small>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" name="password_confirm" id="password_confirm"
                                       placeholder="Repita su contraseña" required oninput="validarConfirmacion()">
                                <small id="password-match" class="d-none"><i class="fas fa-times text-danger"></i> Las contraseñas no coinciden</small>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 btn-lg mb-3">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-muted mb-0">¿Ya tienes cuenta? 
                                <a href="login.php" class="fw-bold">Inicia sesión</a>
                            </p>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="../index.php" class="text-muted">
                                <i class="fas fa-arrow-left"></i> Volver al inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inicializar regiones
        document.addEventListener('DOMContentLoaded', function() {
            const provinciaSeleccionada = "<?php echo $_POST['provincia'] ?? ''; ?>";
            const ciudadSeleccionada = "<?php echo $_POST['ciudad'] ?? ''; ?>";
            cargarProvincias('provincia', 'ciudad', provinciaSeleccionada, ciudadSeleccionada);
        });

        function validarPassword() {
            const password = document.getElementById('password').value;
            
            // Longitud
            actualizarRequisito('req-longitud', password.length >= 8);
            // Mayúscula
            actualizarRequisito('req-mayuscula', /[A-Z]/.test(password));
            // Minúscula
            actualizarRequisito('req-minuscula', /[a-z]/.test(password));
            // Número
            actualizarRequisito('req-numero', /[0-9]/.test(password));
            // Especial
            actualizarRequisito('req-especial', /[!@#$%^&*(),.?":{}|<>]/.test(password));
            
            validarConfirmacion();
        }
        
        function actualizarRequisito(id, cumple) {
            const elemento = document.getElementById(id);
            const icono = elemento.querySelector('i');
            
            if (cumple) {
                icono.classList.remove('fa-times', 'text-danger');
                icono.classList.add('fa-check', 'text-success');
                elemento.classList.add('text-success');
                elemento.classList.remove('text-muted');
            } else {
                icono.classList.remove('fa-check', 'text-success');
                icono.classList.add('fa-times', 'text-danger');
                elemento.classList.remove('text-success');
            }
        }
        
        function validarConfirmacion() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
            const mensaje = document.getElementById('password-match');
            
            if (confirm.length > 0) {
                if (password === confirm) {
                    mensaje.classList.add('d-none');
                } else {
                    mensaje.classList.remove('d-none');
                }
            } else {
                mensaje.classList.add('d-none');
            }
        }
    </script>
</body>
</html>
