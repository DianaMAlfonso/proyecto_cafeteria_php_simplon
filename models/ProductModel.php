<?php
require_once __DIR__ . '/../config/database.php';

class ProductModel {
    private $conn;
    private $table_name = "products";

    public function __construct() {
        $this->conn = getDbConnection();
    }

    // Obtener todos los productos
    public function getAllProducts() {
        $query = "SELECT id, name, reference, price, weight, category_id, stock, created_at FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por ID
    public function getProductById($id) {
        $query = "SELECT id, name, reference, price, weight, category_id, stock, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo producto
    public function create($name, $reference, $price, $weight, $category_id, $stock) {
        $query = "INSERT INTO " . $this->table_name . " (name, reference, price, weight, category_id, stock) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        // Sanear datos
        $name = htmlspecialchars(strip_tags($name));
        $reference = htmlspecialchars(strip_tags($reference));
        $price = htmlspecialchars(strip_tags($price));
        $weight = htmlspecialchars(strip_tags($weight));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $stock = htmlspecialchars(strip_tags($stock));

        // Vincular parámetros
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $reference);
        $stmt->bindParam(3, $price);
        $stmt->bindParam(4, $weight);
        $stmt->bindParam(5, $category_id);
        $stmt->bindParam(6, $stock);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Actualizar un producto existente
    public function update($id, $name, $reference, $price, $weight, $category_id, $stock) {
        $query = "UPDATE " . $this->table_name . " SET name = ?, reference = ?, price = ?, weight = ?, category_id = ?, stock = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Sanear datos
        $name = htmlspecialchars(strip_tags($name));
        $reference = htmlspecialchars(strip_tags($reference));
        $price = htmlspecialchars(strip_tags($price));
        $weight = htmlspecialchars(strip_tags($weight));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $stock = htmlspecialchars(strip_tags($stock));
        $id = htmlspecialchars(strip_tags($id)); // También sanear el ID

        // Vincular parámetros
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $reference);
        $stmt->bindParam(3, $price);
        $stmt->bindParam(4, $weight);
        $stmt->bindParam(5, $category_id);
        $stmt->bindParam(6, $stock);
        $stmt->bindParam(7, $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar un producto
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        // Sanear y vincular
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Buscar producto por referencia (útil para validaciones)
    public function findByReference($reference) {
        $query = "SELECT id, name, reference FROM " . $this->table_name . " WHERE reference = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $reference);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>