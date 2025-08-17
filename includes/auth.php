<?php
session_start();
require_once 'includes/security.php';

// Función para verificar si el usuario está autenticado
function estaAutenticado() {
    if (!Security::verificarTimeoutSesion()) {
        return false;
    }
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Función para requerir autenticación
function requerirAutenticacion() {
    if (!estaAutenticado()) {
        header("Location: login.php");
        exit();
    }
}

function verificarRol($rol_requerido = 'admin') {
    if (!estaAutenticado()) {
        header("Location: login.php");
        exit();
    }
    
    // Por ahora todos los usuarios son admin, pero se puede expandir
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rol_requerido) {
        header("Location: login.php?error=sin_permisos");
        exit();
    }
}

// Función para obtener datos del usuario actual
function obtenerUsuarioActual() {
    if (estaAutenticado()) {
        return [
            'id' => $_SESSION['usuario_id'],
            'username' => $_SESSION['username'],
            'nombre_completo' => $_SESSION['nombre_completo'],
            'email' => $_SESSION['email']
        ];
    }
    return null;
}

// Función para iniciar sesión
function iniciarSesion($usuario) {
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario->id;
    $_SESSION['username'] = $usuario->username;
    $_SESSION['nombre_completo'] = $usuario->nombre_completo;
    $_SESSION['email'] = $usuario->email;
    $_SESSION['rol'] = 'admin'; // Added role to session
    $_SESSION['tiempo_login'] = time();
    $_SESSION['ultima_actividad'] = time();
}

// Función para cerrar sesión
function cerrarSesion() {
    session_unset();
    session_destroy();
}

// Función para regenerar ID de sesión (seguridad)
function regenerarSesion() {
    session_regenerate_id(true);
}
?>
