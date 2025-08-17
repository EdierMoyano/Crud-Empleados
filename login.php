<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Si ya está autenticado, redirigir al dashboard
if (estaAutenticado()) {
    header("Location: index.php");
    exit();
}

// Obtener conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto usuario
$usuario = new Usuario($db);

$errores = array();
$mensaje = "";

// Procesar formulario de login
if ($_POST) { // Removed rate limit condition check

    if (empty($_POST['username'])) {
        $errores[] = "El usuario o email es obligatorio.";
    }
    if (empty($_POST['password'])) {
        $errores[] = "La contraseña es obligatoria.";
    }

    // Si no hay errores, intentar autenticar
    if (empty($errores)) {
        $username = Security::sanitizarEntrada($_POST['username']);
        $password = $_POST['password']; // No sanitizar password

        if ($usuario->autenticar($username, $password)) {
            iniciarSesion($usuario);
            regenerarSesion();
            header("Location: index.php");
            exit();
        } else {
            $errores[] = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3 class="mb-0">Gestión de Empleados</h3>
                        <p class="mb-0 mt-2">Iniciar Sesión</p>
                    </div>
                    <div class="login-body">
                        <?php if (!empty($errores)): ?> <!-- Removed rate limit error check -->
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action=""> <!-- Removed rate limit condition wrapper -->
                            <!-- Removed CSRF token hidden input -->

                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Usuario o Email
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                    placeholder="Ingrese su usuario o email"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Contraseña
                                </label>
                                <input type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="Ingrese su contraseña"
                                    required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Presentado por : <strong>Edier Santiago Moyano</strong> | <br> Para: <strong> Emtelco</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>