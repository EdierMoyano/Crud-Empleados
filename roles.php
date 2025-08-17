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

// Leer roles
$stmt = $rol->leer();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar desactivación
if(isset($_GET['desactivar'])) {
    $rol->id = $_GET['desactivar'];
    if($rol->desactivar()) {
        $mensaje = "Rol desactivado correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "No se puede desactivar el rol porque está siendo usado por empleados activos.";
        $tipo_mensaje = "warning";
    }
    // Recargar la lista
    $stmt = $rol->leer();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include_once 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Gestión de Roles y Cargos</h1>
            <a href="crear_rol.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Rol
            </a>
        </div>

        <?php if(isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php if(count($roles) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cargo</th>
                                    <th>Departamento</th>
                                    <th>Descripción</th>
                                    <th>Empleados</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($roles as $r): ?>
                                    <tr class="<?php echo $r['activo'] ? '' : 'table-secondary'; ?>">
                                        <td><?php echo htmlspecialchars($r['id']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($r['nombre_cargo']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo htmlspecialchars($r['departamento']); ?></span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars(substr($r['descripcion'], 0, 80)); ?>
                                                <?php if(strlen($r['descripcion']) > 80) echo '...'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $r['empleados_count'] > 0 ? 'success' : 'secondary'; ?>">
                                                <?php echo $r['empleados_count']; ?> empleado<?php echo $r['empleados_count'] != 1 ? 's' : ''; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($r['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="editar_rol.php?id=<?php echo $r['id']; ?>" 
                                               class="btn btn-sm btn-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($r['activo'] && $r['empleados_count'] == 0): ?>
                                                <a href="?desactivar=<?php echo $r['id']; ?>" 
                                                   class="btn btn-sm btn-danger btn-action"
                                                   onclick="return confirm('¿Está seguro de desactivar este rol?')">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php elseif($r['empleados_count'] > 0): ?>
                                                <button class="btn btn-sm btn-secondary btn-action" 
                                                        title="No se puede desactivar: tiene empleados asignados"
                                                        disabled>
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay roles registrados</h5>
                        <p class="text-muted">Comience agregando un nuevo rol.</p>
                        <a href="crear_rol.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Agregar Primer Rol
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
