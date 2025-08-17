<?php
class Rol {
    private $conn;
    private $table_name = "roles";

    public $id;
    public $nombre_cargo;
    public $departamento;
    public $descripcion;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear rol
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre_cargo=:nombre_cargo, 
                      departamento=:departamento, 
                      descripcion=:descripcion";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre_cargo = htmlspecialchars(strip_tags($this->nombre_cargo));
        $this->departamento = htmlspecialchars(strip_tags($this->departamento));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));

        // Vincular valores
        $stmt->bindParam(":nombre_cargo", $this->nombre_cargo);
        $stmt->bindParam(":departamento", $this->departamento);
        $stmt->bindParam(":descripcion", $this->descripcion);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todos los roles
    public function leer() {
        $query = "SELECT r.id, r.nombre_cargo, r.departamento, r.descripcion, r.activo,
                         COUNT(e.id) as empleados_count
                  FROM " . $this->table_name . " r
                  LEFT JOIN empleados e ON r.id = e.rol_id AND e.activo = 1
                  GROUP BY r.id, r.nombre_cargo, r.departamento, r.descripcion, r.activo
                  ORDER BY r.departamento, r.nombre_cargo";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Leer un rol específico
    public function leerUno() {
        $query = "SELECT id, nombre_cargo, departamento, descripcion, activo 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre_cargo = $row['nombre_cargo'];
            $this->departamento = $row['departamento'];
            $this->descripcion = $row['descripcion'];
            $this->activo = $row['activo'];
            return true;
        }
        return false;
    }

    // Actualizar rol
    public function actualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_cargo = :nombre_cargo,
                      departamento = :departamento,
                      descripcion = :descripcion,
                      activo = :activo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre_cargo = htmlspecialchars(strip_tags($this->nombre_cargo));
        $this->departamento = htmlspecialchars(strip_tags($this->departamento));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular valores
        $stmt->bindParam(':nombre_cargo', $this->nombre_cargo);
        $stmt->bindParam(':departamento', $this->departamento);
        $stmt->bindParam(':descripcion', $this->descripcion);
        $stmt->bindParam(':activo', $this->activo);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si el rol está siendo usado por empleados
    public function estaEnUso() {
        $query = "SELECT COUNT(*) as count FROM empleados WHERE rol_id = :id AND activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Desactivar rol (eliminación suave)
    public function desactivar() {
        if($this->estaEnUso()) {
            return false; // No se puede desactivar si está en uso
        }

        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si nombre de cargo existe
    public function nombreCargoExiste($nombre_cargo, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nombre_cargo = :nombre_cargo";
        
        if($id) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre_cargo', $nombre_cargo);
        
        if($id) {
            $stmt->bindParam(':id', $id);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Obtener departamentos únicos
    public function obtenerDepartamentos() {
        $query = "SELECT DISTINCT departamento FROM " . $this->table_name . " WHERE activo = 1 ORDER BY departamento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
