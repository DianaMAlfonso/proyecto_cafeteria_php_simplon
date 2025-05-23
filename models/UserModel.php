<?php require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $this->conn = getDbConnection();
    }

    // Método para crear un nuevo usuario
    public function create($name, $email, $password, $role) { 
        $query = "INSERT INTO " . $this->table_name . " (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $role = htmlspecialchars(strip_tags($role));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para encontrar un usuario por email (para login)
    public function findByEmail($email) {
        $query = "SELECT id, name, email, password, role FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    // Método para obtener todos los usuarios (para admin)
    public function getAllUsers() {
        $query = "SELECT id, name, email, role FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener un usuario por ID
    public function getUserById($id) {
        $query = "SELECT id, name, email, role FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para actualizar un usuario
    public function update($id, $name, $email, $role, $password = null) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, role = :role";
        if ($password) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $role = htmlspecialchars(strip_tags($role));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":id", $id);

        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hashed_password);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para eliminar un usuario
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>