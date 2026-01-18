<?php
session_start();

// Procesar login directamente en PHP si viene por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    require_once '../modelo/Cliente.php';
    
    $respuesta = ['success' => false];
    
    if ($_POST['accion'] === 'login') {
        $credencial = $_POST['credencial'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($credencial) && !empty($password)) {
            $modelo = new Cliente();
            $usuario = $modelo->autenticar($credencial, $password);
            
            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['ultimo_acceso'] = time();
                
                // Redirigir según rol
                $redireccion = $usuario['rol'] === 'admin' 
                    ? 'admin/dashboard.php' 
                    : 'cliente/catalogo.php';
                
                header("Location: $redireccion");
                exit;
            } else {
                $error_login = "Credenciales inválidas. Verifique su email/cédula y contraseña.";
            }
        } else {
            $error_login = "Por favor complete todos los campos.";
        }
    }
}

// Si ya hay sesión activa, redirigir
if (isset($_SESSION['usuario'])) {
    $redireccion = $_SESSION['usuario']['rol'] === 'admin' ? 'admin/dashboard.php' : 'cliente/catalogo.php';
    header("Location: $redireccion");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CalzadEC</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2><i class="fas fa-shoe-prints text-primary"></i> CalzadEC</h2>
                            <h4>Iniciar Sesión</h4>
                            <p class="text-muted">Ingresa con tu cédula o email</p>
                        </div>
                        
                        <?php if (isset($error_login)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_login); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="accion" value="login">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cédula o Email</label>
                                <input type="text" class="form-control form-control-lg" name="credencial" 
                                       placeholder="correo@dominio.com" required
                                       value="<?php echo htmlspecialchars($_POST['credencial'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Contraseña</label>
                                <input type="password" class="form-control form-control-lg" name="password" 
                                       placeholder="Ingrese su contraseña" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                                <i class="fas fa-sign-in-alt"></i> Ingresar
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-muted mb-2">¿No tienes cuenta?</p>
                            <a href="registro.php" class="btn btn-outline-success">
                                <i class="fas fa-user-plus"></i> Crear Cuenta
                            </a>
                        </div>
                        
                        <div class="text-center mt-4">
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
</body>
</html>
