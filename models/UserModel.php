<?php
require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private $conn;
    private $table_name = "users";

    public function __construct()
    {
        $this->conn = getDbConnection();
    }

    /**
     * Método para crear un nuevo usuario.
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     * @return bool True si el usuario se creó con éxito, false en caso contrario.
     */
    public function create($name, $email, $password, $role)
    {
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

    /**
     * Método para encontrar un usuario por email (para login y recuperación).
     * @param string $email
     * @return array|false El usuario como array asociativo o false si no se encuentra.
     */
    public function findByEmail($email)
    {
        $query = "SELECT id, name, email, password, role, reset_token, token_expiry FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    /**
     * Método para obtener todos los usuarios (para admin).
     * @return array Un array de usuarios.
     */
    public function getAllUsers()
    {
        $query = "SELECT id, name, email, role FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para obtener un usuario por ID.
     * @param int $id
     * @return array|false El usuario como array asociativo o false si no se encuentra.
     */
    public function getUserById($id)
    {
        $query = "SELECT id, name, email, role FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Método para actualizar un usuario.
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $role
     * @param string|null $password
     * @return bool True si el usuario se actualizó con éxito, false en caso contrario.
     */
    public function update($id, $name, $email, $role, $password = null)
    {
        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, role = :role";
        if ($password) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        $role = htmlspecialchars(strip_tags($role));
        $id = htmlspecialchars(strip_tags($id)); // Aunque es un ID, lo mantengo por tu estilo

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs

        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hashed_password);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Método para eliminar un usuario.
     * @param int $id
     * @return bool True si el usuario se eliminó con éxito, false en caso contrario.
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id)); // Aunque es un ID, lo mantengo por tu estilo
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Usar PDO::PARAM_INT para IDs

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // --- MÉTODOS AÑADIDOS PARA RECUPERACIÓN DE CONTRASEÑA ---

    /**
     * Guarda el token de restablecimiento de contraseña y su fecha de expiración para un usuario.
     * @param int $userId El ID del usuario.
     * @param string $token El token generado.
     * @param string $expiry La fecha y hora de expiración del token.
     * @return bool True si se guardó con éxito, false en caso contrario.
     */
    public function saveResetToken($userId, $token, $expiry)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET reset_token = :token, token_expiry = :expiry WHERE id = :user_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al guardar token de restablecimiento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por su token de restablecimiento.
     * @param string $token El token de restablecimiento.
     * @return array|false El usuario si se encuentra, o false.
     */
    public function findByToken($token)
    {
        try {
            $query = "SELECT id, name, email, token_expiry FROM " . $this->table_name . " WHERE reset_token = :token LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar usuario por token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la contraseña de un usuario.
     * @param int $userId El ID del usuario.
     * @param string $hashedPassword La contraseña hasheada.
     * @return bool True si se actualizó con éxito, false en caso contrario.
     */
    public function updatePassword($userId, $hashedPassword)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET password = :password, reset_token = NULL, token_expiry = NULL WHERE id = :user_id"; // Limpiar token al actualizar
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpia el token de restablecimiento de contraseña de un usuario.
     */
    public function clearResetToken($userId)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET reset_token = NULL, token_expiry = NULL WHERE id = :user_id";
            $stmt = $this->conn->prepare($query); // <-- USAR $this->conn
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al limpiar token de restablecimiento: " . $e->getMessage());
            return false;
        }
    }
}
