<?php
class Empleado {
    private $conn;
    private $table_name = "empleados";

    public $id;
    public $nombre_completo;
    public $rol_id;
    public $correo_electronico;
    public $telefono;
    public $fecha_ingreso;
    public $fecha_salida;
    public $salario;
    public $activo;
    public $notas;
    public $creado_por;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear empleado
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre_completo=:nombre_completo, 
                      rol_id=:rol_id, 
                      correo_electronico=:correo_electronico, 
                      telefono=:telefono,
                      fecha_ingreso=:fecha_ingreso,
                      salario=:salario,
                      notas=:notas,
                      creado_por=:creado_por";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->correo_electronico = htmlspecialchars(strip_tags($this->correo_electronico));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->fecha_ingreso = htmlspecialchars(strip_tags($this->fecha_ingreso));
        $this->notas = htmlspecialchars(strip_tags($this->notas));

        // Vincular valores
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":rol_id", $this->rol_id);
        $stmt->bindParam(":correo_electronico", $this->correo_electronico);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":fecha_ingreso", $this->fecha_ingreso);
        $stmt->bindParam(":salario", $this->salario);
        $stmt->bindParam(":notas", $this->notas);
        $stmt->bindParam(":creado_por", $this->creado_por);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todos los empleados (ordenados por fecha de ingreso descendente)
    public function leer() {
        $query = "SELECT e.id, e.nombre_completo, r.nombre_cargo, r.departamento, 
                         e.correo_electronico, e.telefono, e.fecha_ingreso, e.fecha_salida,
                         e.salario, e.activo, e.notas
                  FROM " . $this->table_name . " e
                  INNER JOIN roles r ON e.rol_id = r.id
                  WHERE e.activo = 1
                  ORDER BY e.fecha_ingreso DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Leer un empleado específico
    public function leerUno() {
        $query = "SELECT e.id, e.nombre_completo, e.rol_id, r.nombre_cargo, r.departamento,
                         e.correo_electronico, e.telefono, e.fecha_ingreso, e.fecha_salida,
                         e.salario, e.activo, e.notas
                  FROM " . $this->table_name . " e
                  INNER JOIN roles r ON e.rol_id = r.id
                  WHERE e.id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre_completo = $row['nombre_completo'];
            $this->rol_id = $row['rol_id'];
            $this->correo_electronico = $row['correo_electronico'];
            $this->telefono = $row['telefono'];
            $this->fecha_ingreso = $row['fecha_ingreso'];
            $this->fecha_salida = $row['fecha_salida'];
            $this->salario = $row['salario'];
            $this->activo = $row['activo'];
            $this->notas = $row['notas'];
            return true;
        }
        return false;
    }

    // Actualizar empleado
    public function actualizar() {
        $empleado_actual = new Empleado($this->conn);
        $empleado_actual->id = $this->id;
        $empleado_actual->leerUno();
        
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_completo = :nombre_completo,
                      rol_id = :rol_id,
                      correo_electronico = :correo_electronico,
                      telefono = :telefono,
                      fecha_ingreso = :fecha_ingreso,
                      fecha_salida = :fecha_salida,
                      salario = :salario,
                      notas = :notas
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->correo_electronico = htmlspecialchars(strip_tags($this->correo_electronico));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->fecha_ingreso = htmlspecialchars(strip_tags($this->fecha_ingreso));
        $this->fecha_salida = htmlspecialchars(strip_tags($this->fecha_salida));
        $this->notas = htmlspecialchars(strip_tags($this->notas));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular valores
        $stmt->bindParam(':nombre_completo', $this->nombre_completo);
        $stmt->bindParam(':rol_id', $this->rol_id);
        $stmt->bindParam(':correo_electronico', $this->correo_electronico);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':fecha_ingreso', $this->fecha_ingreso);
        $stmt->bindParam(':fecha_salida', $this->fecha_salida);
        $stmt->bindParam(':salario', $this->salario);
        $stmt->bindParam(':notas', $this->notas);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            
            // Compare and log changes
            if($empleado_actual->nombre_completo != $this->nombre_completo) {
                $this->registrarCambio('nombre_completo', $empleado_actual->nombre_completo, $this->nombre_completo, $usuario_id);
            }
            if($empleado_actual->rol_id != $this->rol_id) {
                $this->registrarCambio('rol_id', $empleado_actual->rol_id, $this->rol_id, $usuario_id);
            }
            if($empleado_actual->correo_electronico != $this->correo_electronico) {
                $this->registrarCambio('correo_electronico', $empleado_actual->correo_electronico, $this->correo_electronico, $usuario_id);
            }
            if($empleado_actual->telefono != $this->telefono) {
                $this->registrarCambio('telefono', $empleado_actual->telefono, $this->telefono, $usuario_id);
            }
            if($empleado_actual->fecha_ingreso != $this->fecha_ingreso) {
                $this->registrarCambio('fecha_ingreso', $empleado_actual->fecha_ingreso, $this->fecha_ingreso, $usuario_id);
            }
            if($empleado_actual->fecha_salida != $this->fecha_salida) {
                $this->registrarCambio('fecha_salida', $empleado_actual->fecha_salida, $this->fecha_salida, $usuario_id);
            }
            if($empleado_actual->salario != $this->salario) {
                $this->registrarCambio('salario', $empleado_actual->salario, $this->salario, $usuario_id);
            }
            if($empleado_actual->notas != $this->notas) {
                $this->registrarCambio('notas', $empleado_actual->notas, $this->notas, $usuario_id);
            }
            
            return true;
        }
        return false;
    }

    // Eliminar empleado
    public function eliminar() {
        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            $usuario_id = $_SESSION['usuario_id'] ?? 1;
            $this->registrarCambio('activo', '1', '0', $usuario_id);
            return true;
        }
        return false;
    }

    // Validar email único
    public function emailExiste($email, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE correo_electronico = :email";
        
        if($id) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if($id) {
            $stmt->bindParam(':id', $id);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function obtenerRoles() {
        $query = "SELECT id, nombre_cargo, departamento FROM roles WHERE activo = 1 ORDER BY departamento, nombre_cargo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarCambio($campo, $valor_anterior, $valor_nuevo, $usuario_id) {
        $query = "INSERT INTO empleados_historial 
                  SET empleado_id=:empleado_id, 
                      campo_modificado=:campo, 
                      valor_anterior=:valor_anterior, 
                      valor_nuevo=:valor_nuevo, 
                      usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':empleado_id', $this->id);
        $stmt->bindParam(':campo', $campo);
        $stmt->bindParam(':valor_anterior', $valor_anterior);
        $stmt->bindParam(':valor_nuevo', $valor_nuevo);
        $stmt->bindParam(':usuario_id', $usuario_id);

        return $stmt->execute();
    }

    public function obtenerHistorial() {
        $query = "SELECT h.campo_modificado, h.valor_anterior, h.valor_nuevo, 
                         h.fecha_cambio, u.nombre_completo as usuario_nombre
                  FROM empleados_historial h
                  INNER JOIN usuarios u ON h.usuario_id = u.id
                  WHERE h.empleado_id = :empleado_id
                  ORDER BY h.fecha_cambio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empleado_id', $this->id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Leer empleados con paginación y filtros
    public function leerConPaginacion($busqueda = '', $pagina = 1, $por_pagina = 10) {
        $offset = ($pagina - 1) * $por_pagina;
        
        $where_clause = "WHERE e.activo = 1";
        $params = [];
        
        if (!empty($busqueda)) {
            $where_clause .= " AND (e.nombre_completo LIKE :busqueda OR e.correo_electronico LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        
        $query = "SELECT e.id, e.nombre_completo, r.nombre_cargo, r.departamento, 
                         e.correo_electronico, e.telefono, e.fecha_ingreso, e.fecha_salida,
                         e.salario, e.activo, e.notas
                  FROM " . $this->table_name . " e
                  INNER JOIN roles r ON e.rol_id = r.id
                  " . $where_clause . "
                  ORDER BY e.fecha_ingreso DESC
                  LIMIT :offset, :por_pagina";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Contar total de empleados para paginación
    public function contarEmpleados($busqueda = '') {
        $where_clause = "WHERE e.activo = 1";
        $params = [];
        
        if (!empty($busqueda)) {
            $where_clause .= " AND (e.nombre_completo LIKE :busqueda OR e.correo_electronico LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }
        
        $query = "SELECT COUNT(*) as total
                  FROM " . $this->table_name . " e
                  INNER JOIN roles r ON e.rol_id = r.id
                  " . $where_clause;

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
}
?>
