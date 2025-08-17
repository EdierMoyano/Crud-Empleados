<?php
require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'models/Empleado.php';

// Verificar que se proporcione un ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$empleado = new Empleado($db);
$empleado->id = $_GET['id'];

// Verificar que el empleado existe
if(!$empleado->leerUno()) {
    header("Location: index.php");
    exit();
}

// Obtener historial
$historial = $empleado->obtenerHistorial();

$page_title = "Historial de Cambios - " . $empleado->nombre_completo;
include_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history"></i> 
                        Historial de Cambios - <?php echo htmlspecialchars($empleado->nombre_completo); ?>
                    </h4>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <?php if(empty($historial)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No hay cambios registrados para este empleado.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Campo Modificado</th>
                                        <th>Valor Anterior</th>
                                        <th>Valor Nuevo</th>
                                        <th>Modificado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($historial as $cambio): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i:s', strtotime($cambio['fecha_cambio'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php 
                                                    $campos = [
                                                        'nombre_completo' => 'Nombre Completo',
                                                        'rol_id' => 'Cargo',
                                                        'correo_electronico' => 'Email',
                                                        'telefono' => 'Teléfono',
                                                        'fecha_ingreso' => 'Fecha Ingreso',
                                                        'fecha_salida' => 'Fecha Salida',
                                                        'salario' => 'Salario',
                                                        'notas' => 'Notas',
                                                        'activo' => 'Estado'
                                                    ];
                                                    echo $campos[$cambio['campo_modificado']] ?? $cambio['campo_modificado'];
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($cambio['campo_modificado'] == 'salario'): ?>
                                                    <span class="text-danger">
                                                        $<?php echo number_format($cambio['valor_anterior'], 2); ?>
                                                    </span>
                                                <?php elseif($cambio['campo_modificado'] == 'activo'): ?>
                                                    <span class="badge <?php echo $cambio['valor_anterior'] == '1' ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $cambio['valor_anterior'] == '1' ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($cambio['valor_anterior'] ?: 'Sin valor'); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($cambio['campo_modificado'] == 'salario'): ?>
                                                    <span class="text-success">
                                                        $<?php echo number_format($cambio['valor_nuevo'], 2); ?>
                                                    </span>
                                                <?php elseif($cambio['campo_modificado'] == 'activo'): ?>
                                                    <span class="badge <?php echo $cambio['valor_nuevo'] == '1' ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $cambio['valor_nuevo'] == '1' ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <?php echo htmlspecialchars($cambio['valor_nuevo'] ?: 'Sin valor'); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($cambio['usuario_nombre']); ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
