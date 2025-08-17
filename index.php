<?php
require_once 'includes/auth.php';
requerirAutenticacion();

require_once 'config/database.php';
require_once 'models/Empleado.php';

// Obtener conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto empleado
$empleado = new Empleado($db);

$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;

$stmt = $empleado->leerConPaginacion($busqueda, $pagina_actual, $por_pagina);
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_empleados = $empleado->contarEmpleados($busqueda);
$total_paginas = ceil($total_empleados / $por_pagina);

// Manejar eliminación
if(isset($_GET['eliminar'])) {
    $empleado->id = $_GET['eliminar'];
    if($empleado->eliminar()) {
        $mensaje = "Empleado eliminado correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al eliminar el empleado.";
        $tipo_mensaje = "danger";
    }
    $stmt = $empleado->leerConPaginacion($busqueda, $pagina_actual, $por_pagina);
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_empleados = $empleado->contarEmpleados($busqueda);
    $total_paginas = ceil($total_empleados / $por_pagina);
}

include_once 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Lista de Empleados</h1>
            <a href="crear.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nuevo Empleado
            </a>
        </div>

        <?php if(isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Added search form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   name="busqueda" 
                                   placeholder="Buscar por nombre o correo electrónico..." 
                                   value="<?php echo htmlspecialchars($busqueda); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                        <?php if(!empty($busqueda)): ?>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Added results summary -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        Mostrando <?php echo count($empleados); ?> de <?php echo $total_empleados; ?> empleados
                        <?php if(!empty($busqueda)): ?>
                            para "<?php echo htmlspecialchars($busqueda); ?>"
                        <?php endif; ?>
                    </small>
                </div>

                <?php if(count($empleados) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Cargo</th>
                                    <th>Departamento</th>
                                    <th>Correo Electrónico</th>
                                    <th>Teléfono</th>
                                    <th>Fecha de Ingreso</th>
                                    <th>Salario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($empleados as $emp): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($emp['id']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['nombre_completo']); ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($emp['nombre_cargo']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($emp['departamento']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['correo_electronico']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['telefono'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($emp['fecha_ingreso'])); ?></td>
                                        <td>
                                            <?php if($emp['salario']): ?>
                                                $<?php echo number_format($emp['salario'], 2); ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="editar.php?id=<?php echo $emp['id']; ?>" 
                                               class="btn btn-sm btn-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="historial.php?id=<?php echo $emp['id']; ?>" 
                                               class="btn btn-sm btn-info btn-action"
                                               title="Ver historial de cambios">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <a href="?eliminar=<?php echo $emp['id']; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>&pagina=<?php echo $pagina_actual; ?>" 
                                               class="btn btn-sm btn-danger btn-action"
                                               onclick="return confirm('¿Está seguro de eliminar este empleado?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Added pagination controls -->
                    <?php if($total_paginas > 1): ?>
                        <nav aria-label="Paginación de empleados" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if($pagina_actual > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                            <i class="fas fa-chevron-left"></i> Anterior
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $inicio = max(1, $pagina_actual - 2);
                                $fin = min($total_paginas, $pagina_actual + 2);
                                
                                for($i = $inicio; $i <= $fin; $i++):
                                ?>
                                    <li class="page-item <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if($pagina_actual < $total_paginas): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?><?php echo !empty($busqueda) ? '&busqueda=' . urlencode($busqueda) : ''; ?>">
                                            Siguiente <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <?php if(!empty($busqueda)): ?>
                            <h5 class="text-muted">No se encontraron empleados</h5>
                            <p class="text-muted">No hay empleados que coincidan con "<?php echo htmlspecialchars($busqueda); ?>"</p>
                            <a href="index.php" class="btn btn-outline-primary">Ver todos los empleados</a>
                        <?php else: ?>
                            <h5 class="text-muted">No hay empleados registrados</h5>
                            <p class="text-muted">Comience agregando un nuevo empleado.</p>
                            <a href="crear.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Agregar Primer Empleado
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
