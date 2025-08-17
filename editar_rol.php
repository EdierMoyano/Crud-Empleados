<?php
require_once 'includes/auth.php';
requerirAutenticacion();

require_once 'config/database.php';
require_once 'models/Rol.php';

// Obtener conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto rol
$rol = new Rol($db);

$errores = array();

// Verificar si se proporcionó ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: roles.php");
    exit();
}

$rol->id = $_GET['id'];

// Obtener datos del rol
if(!$rol->leerUno()) {
    header("Location: roles.php");
    exit();
}

$departamentos_existentes = $rol->obtenerDepartamentos();

// Procesar formulario
if($_POST) {
    // Validar campos obligatorios
    if(empty($_POST['nombre_cargo'])) {
        $errores[] = "El nombre del cargo es obligatorio.";
    } elseif($rol->nombreCargoExiste($_POST['nombre_cargo'], $rol->id)) {
        $errores[] = "Ya existe otro rol con ese nombre.";
    }
    if(empty($_POST['departamento'])) {
        $errores[] = "El departamento es obligatorio.";
    }
    if(empty($_POST['descripcion'])) {
        $errores[] = "La descripción es obligatoria.";
    }

    // Si no hay errores, actualizar rol
    if(empty($errores)) {
        $rol->nombre_cargo = $_POST['nombre_cargo'];
        $rol->departamento = $_POST['departamento'];
        $rol->descripcion = $_POST['descripcion'];
        $rol->activo = isset($_POST['activo']) ? 1 : 0;

        if($rol->actualizar()) {
            header("Location: roles.php?mensaje=Rol actualizado correctamente");
            exit();
        } else {
            $errores[] = "Error al actualizar el rol.";
        }
    }
}

include_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-briefcase me-2"></i>Editar Rol
                </h4>
            </div>
            <div class="card-body">
                <?php if(!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errores as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nombre_cargo" class="form-label">Nombre del Cargo *</label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre_cargo" 
                               name="nombre_cargo" 
                               value="<?php echo isset($_POST['nombre_cargo']) ? htmlspecialchars($_POST['nombre_cargo']) : htmlspecialchars($rol->nombre_cargo); ?>"
                               placeholder="Ej: Desarrollador Full Stack"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="departamento" class="form-label">Departamento *</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" 
                                       class="form-control" 
                                       id="departamento" 
                                       name="departamento" 
                                       value="<?php echo isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : htmlspecialchars($rol->departamento); ?>"
                                       placeholder="Ej: Desarrollo"
                                       list="departamentos_existentes"
                                       required>
                                <datalist id="departamentos_existentes">
                                    <?php foreach($departamentos_existentes as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>">
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <small class="form-text text-muted">
                                    Puede seleccionar uno existente o crear uno nuevo
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea class="form-control" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="4"
                                  placeholder="Describa las responsabilidades y habilidades requeridas para este cargo..."
                                  required><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : htmlspecialchars($rol->descripcion); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="activo" 
                                   name="activo" 
                                   <?php echo (isset($_POST['activo']) ? $_POST['activo'] : $rol->activo) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">
                                Rol activo
                            </label>
                            <small class="form-text text-muted d-block">
                                Los roles inactivos no aparecerán en los formularios de empleados
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="roles.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
