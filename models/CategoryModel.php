<?php
require_once __DIR__ . '/../config/database.php';

class CategoryModel {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    /**
     * Obtiene todas las categorías.
     * @return array Un array de categorías.
     */
    public function getAllCategories() {
        try {
            $stmt = $this->conn->query("SELECT * FROM categories ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todas las categorías: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene una categoría por su ID.
     * @param int $id El ID de la categoría.
     * @return array|null La categoría o null si no se encuentra.
     */
    public function getCategoryById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categoría por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crea una nueva categoría.
     * @param string $name El nombre de la categoría.
     * @param string|null $description La descripción de la categoría (opcional).
     * @return bool True si la categoría se creó con éxito, false en caso contrario.
     */
    public function createCategory($name, $description = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la información de una categoría existente.
     * @param int $id El ID de la categoría a actualizar.
     * @param string $name El nuevo nombre de la categoría.
     * @param string|null $description La nueva descripción (opcional).
     * @return bool True si la categoría se actualizó con éxito, false en caso contrario.
     */
    public function updateCategory($id, $name, $description = null) {
        try {
            $stmt = $this->conn->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una categoría.
     * @param int $id El ID de la categoría a eliminar.
     * @return array Un array con 'success' (bool) y 'message' (string).
     */
    public function deleteCategory($id) {
        try {
            // Antes de eliminar, verificar si hay productos asociados a esta categoría
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $productCount = $stmt->fetchColumn();

            if ($productCount > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar la categoría porque tiene productos asociados. Primero reasigna o elimina los productos.'];
            }

            $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();
            return ['success' => $success, 'message' => $success ? 'Categoría eliminada con éxito.' : 'Error al eliminar la categoría.'];
        } catch (PDOException $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al eliminar la categoría: ' . $e->getMessage()];
        }
    }
}
?>