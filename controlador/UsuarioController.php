<?php
session_start();
require_once __DIR__ . '/../modelo/Cliente.php';

class UsuarioController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Cliente();
    }

    public function handleRequest() {
        $accion = $_GET['accion'] ?? '';

        switch ($accion) {
            case 'obtener':
                $this->obtenerPerfil();
                break;
            case 'actualizar':
                $this->actualizarPerfil();
                break;
            default:
                echo json_encode(['error' => 'Acción no válida']);
                break;
        }
    }

    private function obtenerPerfil() {
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'No autenticado']);
            return;
        }

        $id = $_SESSION['usuario']['id_usuario'];
        $datos = $this->modelo->obtenerPorId($id);

        if ($datos) {
            echo json_encode(['success' => true, 'usuario' => $datos]);
        } else {
            echo json_encode(['error' => 'No se encontró el usuario']);
        }
    }

    private function actualizarPerfil() {
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['error' => 'No autenticado']);
            return;
        }

        $id = $_SESSION['usuario']['id_usuario'];
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';

        if (empty($telefono) || empty($direccion)) {
            echo json_encode(['error' => 'Teléfono y dirección son obligatorios']);
            return;
        }

        // Obtener datos actuales para no sobreescribir nombre y email con vacíos
        // Aunque el modelo tiene un método 'actualizar' que requiere nombre y email,
        // vamos a obtener los datos actuales primero.
        $usuarioActual = $this->modelo->obtenerPorId($id);
        
        $datosNuevos = [
            'nombre_completo' => $usuarioActual['nombre_completo'],
            'email' => $usuarioActual['email'],
            'telefono' => $telefono,
            'direccion' => $direccion
        ];

        if ($this->modelo->actualizar($id, $datosNuevos)) {
            // Actualizar la sesión
            $_SESSION['usuario']['telefono'] = $telefono;
            $_SESSION['usuario']['direccion'] = $direccion;
            
            echo json_encode(['success' => true, 'mensaje' => 'Perfil actualizado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al actualizar el perfil']);
        }
    }
}

$controller = new UsuarioController();
$controller->handleRequest();
