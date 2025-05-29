<?php
require_once __DIR__ . '/../config/database.php';

class ProductModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = getDbConnection();
    }

    /**
     * Crea un nuevo producto.
     * @param string $name
     * @param string $reference
     * @param int $price
     * @param int $weight
     * @param int $category_id
     * @param int $stock
     * @param string $creation_date
     * @return bool True si se creó con éxito, false en caso contrario.
     */
    public function createProduct($name, $reference, $price, $weight, $category_id, $stock, $creation_date)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO products (name, reference, price, weight, category_id, stock, creation_date) VALUES (:name, :reference, :price, :weight, :category_id, :stock, :creation_date)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':reference', $reference);
            $stmt->bindParam(':price', $price, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':creation_date', $creation_date);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un producto por su ID.
     * @param int $id
     * @return array|null El producto o null si no se encuentra.
     */
    public function getProductById($id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id
            ");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener producto por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza la información de un producto existente.
     * @param int $id
     * @param string $name
     * @param string $reference
     * @param int $price
     * @param int $weight
     * @param int $category_id
     * @param int $stock
     * @return bool True si se actualizó con éxito, false en caso contrario.
     */
    public function updateProduct($id, $name, $reference, $price, $weight, $category_id, $stock)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE products SET name = :name, reference = :reference, price = :price, weight = :weight, category_id = :category_id, stock = :stock WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':reference', $reference);
            $stmt->bindParam(':price', $price, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un producto.
     * @param int $id
     * @return bool True si se eliminó con éxito, false en caso contrario (puede fallar por FK si hay ventas).
     */
    public function deleteProduct($id)
    {
        try {
            // Se puede agregar una verificación si el producto tiene ventas asociadas
            // antes de eliminarlo, o configurar la FK ON DELETE CASCADE/SET NULL.
            // Por ahora, si hay ventas, la FK lo impedirá y devolverá false.
            $stmt = $this->conn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los productos (incluyendo el nombre de la categoría).
     * @return array Un array de productos.
     */
    public function getAllProducts()
    {
        try {
            $stmt = $this->conn->query("
                SELECT p.id, p.name, p.reference, p.price, p.weight, p.stock, p.creation_date, c.name AS category_name, p.category_id
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                ORDER BY p.name ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los productos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el producto con mayor cantidad de stock.
     * @return array|null El producto con más stock o null si no hay productos.
     */
    public function getProductWithMostStock()
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY stock DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener el producto con mayor stock: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Busca un producto por su referencia.
     * @param string $reference
     * @return array|null El producto o null si no se encuentra.
     */
    public function findByReference($reference)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id, reference FROM products WHERE reference = :reference");
            $stmt->bindParam(':reference', $reference);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar producto por referencia: " . $e->getMessage());
            return null;
        }
    }
}
