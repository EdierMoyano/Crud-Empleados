<?php
require_once 'includes/auth.php';
$usuario_actual = obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
        .table th {
            background-color: #495057;
            color: white;
            border-color: #495057;
        }
        .btn-action {
            margin: 0 2px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-users me-2"></i>
                Gestión de Empleados
            </a>
            
            <!-- Added navigation menu for role management -->
            <?php if($usuario_actual): ?>
            <div class="navbar-nav me-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" id="navbarMenuDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bars me-1"></i>
                        Menú
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php">
                            <i class="fas fa-users me-2"></i>Empleados
                        </a></li>
                        <li><a class="dropdown-item" href="roles.php">
                            <i class="fas fa-briefcase me-2"></i>Roles y Cargos
                        </a></li>
                    </ul>
                </div>
            </div>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($usuario_actual['nombre_completo']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">
                            <i class="fas fa-at me-1"></i><?php echo htmlspecialchars($usuario_actual['username']); ?>
                        </h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container mt-4">
