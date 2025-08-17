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

$departamentos_existentes = $rol->obtenerDepartamentos();

$errores = array();
$mensaje = "";

// Procesar formulario
if($_POST) {
    // Validar campos obligatorios
    if(empty($_POST['nombre_cargo'])) {
        $errores[] = "El nombre del cargo es obligatorio.";
    } elseif($rol->nombreCargoExiste($_POST['nombre_cargo'])) {
        $errores[] = "Ya existe un rol con ese nombre.";
    }
    if(empty($_POST['departamento'])) {
        $errores[] = "El departamento es obligatorio.";
    }
    if(empty($_POST['descripcion'])) {
        $errores[] = "La descripción es obligatoria.";
    }

    // Si no hay errores, crear rol
    if(empty($errores)) {
        $rol->nombre_cargo = $_POST['nombre_cargo'];
        $rol->departamento = $_POST['departamento'];
        $rol->descripcion = $_POST['descripcion'];

        if($rol->crear()) {
            header("Location: roles.php?mensaje=Rol creado correctamente");
            exit();
        } else {
            $errores[] = "Error al crear el rol.";
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
                    <i class="fas fa-briefcase me-2"></i>Nuevo Rol
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
                               value="<?php echo isset($_POST['nombre_cargo']) ? htmlspecialchars($_POST['nombre_cargo']) : ''; ?>"
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
                                       value="<?php echo isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : ''; ?>"
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
                                  required><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="roles.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
