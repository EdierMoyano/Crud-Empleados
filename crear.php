<?php
require_once 'includes/auth.php';
requerirAutenticacion();

require_once 'config/database.php';
require_once 'models/Empleado.php';
require_once 'includes/security.php';

// Obtener conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto empleado
$empleado = new Empleado($db);

$roles = $empleado->obtenerRoles();

$errores = array();
$mensaje = "";

// Procesar formulario
if($_POST) {
    $nombre_completo = Security::sanitizarEntrada($_POST['nombre_completo']);
    $rol_id = Security::sanitizarEntrada($_POST['rol_id'], 'int');
    $correo_electronico = Security::sanitizarEntrada($_POST['correo_electronico'], 'email');
    $telefono = Security::sanitizarEntrada($_POST['telefono']);
    $fecha_ingreso = Security::sanitizarEntrada($_POST['fecha_ingreso']);
    $salario = !empty($_POST['salario']) ? Security::sanitizarEntrada($_POST['salario'], 'float') : null;
    $notas = Security::sanitizarEntrada($_POST['notas']);

    // Validar campos obligatorios
    if(empty($nombre_completo)) {
        $errores[] = "El nombre completo es obligatorio.";
    }
    if(empty($rol_id)) {
        $errores[] = "El cargo es obligatorio.";
    }
    if(empty($correo_electronico)) {
        $errores[] = "El correo electrónico es obligatorio.";
    } elseif(!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    } elseif($empleado->emailExiste($correo_electronico)) {
        $errores[] = "El correo electrónico ya está registrado.";
    }
    if(empty($fecha_ingreso)) {
        $errores[] = "La fecha de ingreso es obligatoria.";
    }
    if($salario !== null && !is_numeric($salario)) {
        $errores[] = "El salario debe ser un número válido.";
    }

    // Si no hay errores, crear empleado
    if(empty($errores)) {
        $empleado->nombre_completo = $nombre_completo;
        $empleado->rol_id = $rol_id;
        $empleado->correo_electronico = $correo_electronico;
        $empleado->telefono = $telefono;
        $empleado->fecha_ingreso = $fecha_ingreso;
        $empleado->salario = $salario;
        $empleado->notas = $notas;
        $empleado->creado_por = obtenerUsuarioActual()['id'];

        if($empleado->crear()) {
            header("Location: index.php?mensaje=Empleado creado correctamente");
            exit();
        } else {
            $errores[] = "Error al crear el empleado.";
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
                    <i class="fas fa-user-plus me-2"></i>Nuevo Empleado
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

                <form method="POST" action="" id="empleadoForm">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre_completo" 
                                       name="nombre_completo" 
                                       value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rol_id" class="form-label">Cargo *</label>
                                <select class="form-control" id="rol_id" name="rol_id" required>
                                    <option value="">Seleccione un cargo</option>
                                    <?php 
                                    $departamento_actual = '';
                                    foreach($roles as $rol): 
                                        if($departamento_actual != $rol['departamento']):
                                            if($departamento_actual != '') echo '</optgroup>';
                                            echo '<optgroup label="' . htmlspecialchars($rol['departamento']) . '">';
                                            $departamento_actual = $rol['departamento'];
                                        endif;
                                    ?>
                                        <option value="<?php echo $rol['id']; ?>" 
                                                <?php echo (isset($_POST['rol_id']) && $_POST['rol_id'] == $rol['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['nombre_cargo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if($departamento_actual != '') echo '</optgroup>'; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo_electronico" class="form-label">Correo Electrónico *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="correo_electronico" 
                                       name="correo_electronico" 
                                       value="<?php echo isset($_POST['correo_electronico']) ? htmlspecialchars($_POST['correo_electronico']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>"
                                       placeholder="+34 600 123 456">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso *</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_ingreso" 
                                       name="fecha_ingreso" 
                                       value="<?php echo isset($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : ''; ?>"
                                       required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salario" class="form-label">Salario Anual</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="salario" 
                                           name="salario" 
                                           value="<?php echo isset($_POST['salario']) ? $_POST['salario'] : ''; ?>"
                                           step="0.01"
                                           min="0"
                                           placeholder="45000.00">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control" 
                                  id="notas" 
                                  name="notas" 
                                  rows="3"
                                  placeholder="Información adicional sobre el empleado..."><?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : ''; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Guardar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>

<!-- Adding JavaScript validations for all form fields -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('empleadoForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Expresiones regulares para validación
    const regexNombre = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/;
    const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const regexTelefono = /^[\+]?[0-9\s\-$$$$]{9,15}$/;
    const regexSalario = /^\d+(\.\d{1,2})?$/;
    
    // Función para mostrar error
    function mostrarError(campo, mensaje) {
        campo.classList.add('is-invalid');
        campo.classList.remove('is-valid');
        const feedback = campo.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = mensaje;
        }
    }
    
    // Función para mostrar éxito
    function mostrarExito(campo) {
        campo.classList.add('is-valid');
        campo.classList.remove('is-invalid');
    }
    
    // Validar nombre completo
    const nombreCompleto = document.getElementById('nombre_completo');
    nombreCompleto.addEventListener('blur', function() {
        if (!this.value.trim()) {
            mostrarError(this, 'El nombre completo es obligatorio');
        } else if (!regexNombre.test(this.value.trim())) {
            mostrarError(this, 'El nombre solo debe contener letras y espacios (2-50 caracteres)');
        } else {
            mostrarExito(this);
        }
    });
    
    // Validar cargo
    const rolId = document.getElementById('rol_id');
    rolId.addEventListener('change', function() {
        if (!this.value) {
            mostrarError(this, 'Debe seleccionar un cargo');
        } else {
            mostrarExito(this);
        }
    });
    
    // Validar correo electrónico
    const correoElectronico = document.getElementById('correo_electronico');
    correoElectronico.addEventListener('blur', function() {
        if (!this.value.trim()) {
            mostrarError(this, 'El correo electrónico es obligatorio');
        } else if (!regexEmail.test(this.value.trim())) {
            mostrarError(this, 'El formato del correo electrónico no es válido');
        } else {
            mostrarExito(this);
        }
    });
    
    // Validar teléfono
    const telefono = document.getElementById('telefono');
    telefono.addEventListener('blur', function() {
        if (this.value.trim() && !regexTelefono.test(this.value.trim())) {
            mostrarError(this, 'El teléfono debe tener entre 9-15 dígitos');
        } else if (this.value.trim()) {
            mostrarExito(this);
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Validar fecha de ingreso
    const fechaIngreso = document.getElementById('fecha_ingreso');
    fechaIngreso.addEventListener('change', function() {
        if (!this.value) {
            mostrarError(this, 'La fecha de ingreso es obligatoria');
       
            } else {
                mostrarExito(this);
            }
        }
    );
    
    // Validar salario
    const salario = document.getElementById('salario');
    salario.addEventListener('blur', function() {
        if (this.value.trim() && !regexSalario.test(this.value.trim())) {
            mostrarError(this, 'El salario debe ser un número válido (ej: 45000.00)');
        } else if (this.value.trim() && parseFloat(this.value) < 0) {
            mostrarError(this, 'El salario no puede ser negativo');
        } else if (this.value.trim()) {
            mostrarExito(this);
        } else {
            this.classList.remove('is-valid', 'is-invalid');
        }
    });
    
    // Validar formulario antes de enviar
    form.addEventListener('submit', function(e) {
        let esValido = true;
        
        // Validar campos obligatorios
        if (!nombreCompleto.value.trim() || !regexNombre.test(nombreCompleto.value.trim())) {
            mostrarError(nombreCompleto, 'Nombre completo inválido');
            esValido = false;
        }
        
        if (!rolId.value) {
            mostrarError(rolId, 'Debe seleccionar un cargo');
            esValido = false;
        }
        
        if (!correoElectronico.value.trim() || !regexEmail.test(correoElectronico.value.trim())) {
            mostrarError(correoElectronico, 'Correo electrónico inválido');
            esValido = false;
        }
        
        if (!fechaIngreso.value) {
            mostrarError(fechaIngreso, 'La fecha de ingreso es obligatoria');
            esValido = false;
        };
        
        // Validar campos opcionales si tienen valor
        if (telefono.value.trim() && !regexTelefono.test(telefono.value.trim())) {
            mostrarError(telefono, 'Teléfono inválido');
            esValido = false;
        }
        
        if (salario.value.trim() && (!regexSalario.test(salario.value.trim()) || parseFloat(salario.value) < 0)) {
            mostrarError(salario, 'Salario inválido');
            esValido = false;
        }
        
        if (!esValido) {
            e.preventDefault();
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Corregir errores';
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Empleado';
            }, 3000);
        }
    });
});
</script>
