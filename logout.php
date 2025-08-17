<?php
require_once 'includes/auth.php';

// Cerrar sesión
cerrarSesion();

// Redirigir al login
header("Location: login.php?mensaje=Sesión cerrada correctamente");
exit();
?>
