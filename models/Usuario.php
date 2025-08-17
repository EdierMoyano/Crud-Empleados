<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $nombre_completo;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Autenticar usuario
    public function autenticar($username, $password) {
        $query = "SELECT id, username, email, password_hash, nombre_completo, activo 
                  FROM " . $this->table_name . " 
                  WHERE (username = :username OR email = :username) AND activo = 1 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row && password_verify($password, $row['password_hash'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->nombre_completo = $row['nombre_completo'];
            $this->activo = $row['activo'];
            return true;
        }
        return false;
    }

    // Crear usuario
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, 
                      email=:email, 
                      password_hash=:password_hash, 
                      nombre_completo=:nombre_completo";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        
        // Hash de la contraseña
        $password_hash = password_hash($this->password_hash, PASSWORD_DEFAULT);

        // Vincular valores
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si username existe
    public function usernameExiste($username, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        
        if($id) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if($id) {
            $stmt->bindParam(':id', $id);
        }
        
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Verificar si email existe
    public function emailExiste($email, $id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        
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
}
?>
