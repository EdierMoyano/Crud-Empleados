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

$errores = array();

// Verificar si se proporcionó ID
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$empleado->id = $_GET['id'];

// Obtener datos del empleado
if(!$empleado->leerUno()) {
    header("Location: index.php");
    exit();
}

$roles = $empleado->obtenerRoles();

// Procesar formulario
if($_POST) {
    // Validar campos obligatorios
    if(empty($_POST['nombre_completo'])) {
        $errores[] = "El nombre completo es obligatorio.";
    }
    if(empty($_POST['rol_id'])) {
        $errores[] = "El cargo es obligatorio.";
    }
    if(empty($_POST['correo_electronico'])) {
        $errores[] = "El correo electrónico es obligatorio.";
    } elseif(!filter_var($_POST['correo_electronico'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del correo electrónico no es válido.";
    } elseif($empleado->emailExiste($_POST['correo_electronico'], $empleado->id)) {
        $errores[] = "El correo electrónico ya está registrado por otro empleado.";
    }
    if(empty($_POST['fecha_ingreso'])) {
        $errores[] = "La fecha de ingreso es obligatoria.";
    }
    if(!empty($_POST['salario']) && !is_numeric($_POST['salario'])) {
        $errores[] = "El salario debe ser un número válido.";
    }

    // Si no hay errores, actualizar empleado
    if(empty($errores)) {
        $empleado->nombre_completo = $_POST['nombre_completo'];
        $empleado->rol_id = $_POST['rol_id'];
        $empleado->correo_electronico = $_POST['correo_electronico'];
        $empleado->telefono = $_POST['telefono'];
        $empleado->fecha_ingreso = $_POST['fecha_ingreso'];
        $empleado->fecha_salida = !empty($_POST['fecha_salida']) ? $_POST['fecha_salida'] : null;
        $empleado->salario = !empty($_POST['salario']) ? $_POST['salario'] : null;
        $empleado->notas = $_POST['notas'];

        if($empleado->actualizar()) {
            header("Location: index.php?mensaje=Empleado actualizado correctamente");
            exit();
        } else {
            $errores[] = "Error al actualizar el empleado.";
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
                    <i class="fas fa-user-edit me-2"></i>Editar Empleado
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
                                       value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : htmlspecialchars($empleado->nombre_completo); ?>"
                                       required>
                                <!-- Added validation feedback -->
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Changed cargo input to rol_id dropdown -->
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
                                        $selected = (isset($_POST['rol_id']) ? $_POST['rol_id'] : $empleado->rol_id) == $rol['id'] ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $rol['id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($rol['nombre_cargo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if($departamento_actual != '') echo '</optgroup>'; ?>
                                </select>
                                <!-- Added validation feedback -->
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
                                       value="<?php echo isset($_POST['correo_electronico']) ? htmlspecialchars($_POST['correo_electronico']) : htmlspecialchars($empleado->correo_electronico); ?>"
                                       required>
                                <!-- Added validation feedback -->
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Added telefono field -->
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : htmlspecialchars($empleado->telefono); ?>"
                                       placeholder="+34 600 123 456">
                                <!-- Added validation feedback -->
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
                                       value="<?php echo isset($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : $empleado->fecha_ingreso; ?>"
                                       required>
                                <!-- Added validation feedback -->
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Added fecha_salida field -->
                            <div class="mb-3">
                                <label for="fecha_salida" class="form-label">Fecha de Salida</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_salida" 
                                       name="fecha_salida" 
                                       value="<?php echo isset($_POST['fecha_salida']) ? $_POST['fecha_salida'] : $empleado->fecha_salida; ?>">
                                <small class="form-text text-muted">Dejar vacío si el empleado sigue activo</small>
                                <!-- Added validation feedback -->
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Added salario field -->
                            <div class="mb-3">
                                <label for="salario" class="form-label">Salario Anual</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="salario" 
                                           name="salario" 
                                           value="<?php echo isset($_POST['salario']) ? $_POST['salario'] : $empleado->salario; ?>"
                                           step="0.01"
                                           min="0"
                                           placeholder="45000.00">
                                </div>
                                <!-- Added validation feedback -->
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Added notas field -->
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control" 
                                  id="notas" 
                                  name="notas" 
                                  rows="3"
                                  placeholder="Información adicional sobre el empleado..."><?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : htmlspecialchars($empleado->notas); ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Actualizar Empleado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Added JavaScript validation script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('empleadoForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validation patterns
    const patterns = {
        nombre: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        telefono: /^[\+]?[0-9\s\-$$$$]{9,15}$/,
        salario: /^\d+(\.\d{1,2})?$/
    };
    
    // Validation messages
    const messages = {
        nombre: 'El nombre debe contener solo letras y espacios (2-50 caracteres)',
        email: 'Ingrese un correo electrónico válido',
        telefono: 'Ingrese un número de teléfono válido (9-15 dígitos)',
        salario: 'El salario debe ser un número positivo con máximo 2 decimales',
        required: 'Este campo es obligatorio',
        fecha: 'Seleccione una fecha válida',
        fechaSalida: 'La fecha de salida debe ser posterior a la fecha de ingreso'
    };
    
    // Validate field function
    function validateField(field, pattern, message) {
        const value = field.value.trim();
        const isValid = pattern ? pattern.test(value) : value !== '';
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            field.nextElementSibling.textContent = '';
            return true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            field.nextElementSibling.textContent = message;
            return false;
        }
    }
    
    // Real-time validation
    document.getElementById('nombre_completo').addEventListener('input', function() {
        validateField(this, patterns.nombre, messages.nombre);
    });
    
    document.getElementById('correo_electronico').addEventListener('input', function() {
        validateField(this, patterns.email, messages.email);
    });
    
    document.getElementById('telefono').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            validateField(this, patterns.telefono, messages.telefono);
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            this.nextElementSibling.textContent = '';
        }
    });
    
    document.getElementById('salario').addEventListener('input', function() {
        if (this.value.trim() !== '') {
            validateField(this, patterns.salario, messages.salario);
        } else {
            this.classList.remove('is-invalid', 'is-valid');
            this.nextElementSibling.textContent = '';
        }
    });
    
    document.getElementById('fecha_ingreso').addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            this.nextElementSibling.textContent = '';
            // Si hay fecha de salida, la revisamos también
            validateFechaSalida();
        }
    });
    
    document.getElementById('fecha_salida').addEventListener('change', function() {
        validateFechaSalida();
    });
    
    function validateFechaSalida() {
        const fechaIngreso = document.getElementById('fecha_ingreso').value;
        const fechaSalida = document.getElementById('fecha_salida').value;
        
        if (fechaSalida && fechaIngreso && fechaSalida <= fechaIngreso) {
            document.getElementById('fecha_salida').classList.add('is-invalid');
            document.getElementById('fecha_salida').nextElementSibling.nextElementSibling.textContent = messages.fechaSalida;
        } else if (fechaSalida) {
            document.getElementById('fecha_salida').classList.remove('is-invalid');
            document.getElementById('fecha_salida').classList.add('is-valid');
            document.getElementById('fecha_salida').nextElementSibling.nextElementSibling.textContent = '';
        } else {
            document.getElementById('fecha_salida').classList.remove('is-invalid', 'is-valid');
            document.getElementById('fecha_salida').nextElementSibling.nextElementSibling.textContent = '';
        }
    }
    
    document.getElementById('rol_id').addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            this.nextElementSibling.textContent = '';
        } else {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = messages.required;
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isFormValid = true;
        
        // Validate required fields
        const nombreCompleto = document.getElementById('nombre_completo');
        const correoElectronico = document.getElementById('correo_electronico');
        const rolId = document.getElementById('rol_id');
        const fechaIngreso = document.getElementById('fecha_ingreso');
        const telefono = document.getElementById('telefono');
        const salario = document.getElementById('salario');
        
        // Validate nombre completo
        if (!validateField(nombreCompleto, patterns.nombre, messages.nombre)) {
            isFormValid = false;
        }
        
        // Validate email
        if (!validateField(correoElectronico, patterns.email, messages.email)) {
            isFormValid = false;
        }
        
        // Validate rol
        if (!rolId.value) {
            rolId.classList.add('is-invalid');
            rolId.nextElementSibling.textContent = messages.required;
            isFormValid = false;
        }
        
        // Validate fecha ingreso (solo que no esté vacía)
        if (!fechaIngreso.value) {
            fechaIngreso.classList.add('is-invalid');
            fechaIngreso.nextElementSibling.textContent = messages.required;
            isFormValid = false;
        } else {
            fechaIngreso.classList.remove('is-invalid');
            fechaIngreso.classList.add('is-valid');
            fechaIngreso.nextElementSibling.textContent = '';
        }
        
        // Validate optional fields if they have values
        if (telefono.value.trim() && !patterns.telefono.test(telefono.value.trim())) {
            validateField(telefono, patterns.telefono, messages.telefono);
            isFormValid = false;
        }
        
        if (salario.value.trim() && !patterns.salario.test(salario.value.trim())) {
            validateField(salario, patterns.salario, messages.salario);
            isFormValid = false;
        }
        
        // Validate fecha salida
        validateFechaSalida();
        if (document.getElementById('fecha_salida').classList.contains('is-invalid')) {
            isFormValid = false;
        }
        
        if (!isFormValid) {
            e.preventDefault();
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Corregir errores';
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Empleado';
            }, 3000);
        }
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>
